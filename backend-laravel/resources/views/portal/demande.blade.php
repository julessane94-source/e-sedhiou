@extends('portal.layout')

@section('content')
    @php
        $statusClassMap = [
            'pending' => 'pending',
            'assigned' => 'assigned',
            'processing' => 'processing',
            'completed' => 'completed',
            'rejected' => 'rejected',
        ];

        $ownerName = trim(($demande->citoyen->first_name ?? '') . ' ' . ($demande->citoyen->last_name ?? '')) ?: ($demande->citoyen->name ?? 'Usager');
        $agentName = trim(($demande->agent->first_name ?? '') . ' ' . ($demande->agent->last_name ?? '')) ?: ($demande->agent->name ?? 'Non assigne');
        $backUrl = $currentUser->isAdmin() ? route('portal.admin') : ($currentUser->isAgent() ? route('portal.agent') : route('portal.citizen'));
        $processingChannel = $demande->processing_channel ?? 'counter';
        $hasProcessedDocument = ! empty($demande->processed_document_path);
    @endphp

    <section class="hero">
        <div class="hero__panel">
            <span class="eyebrow">Dossier</span>
            <h1>{{ $demande->reference }}</h1>
            <p class="muted">{{ $requestTypes[$demande->request_type] ?? $demande->request_type }} - ouvert le {{ optional($demande->created_at)->format('d/m/Y H:i') }}</p>
            <div class="actions tw:mt-[18px]">
                <a class="button button--ghost" href="{{ $backUrl }}">Retour a mon espace</a>
                <span class="badge {{ $statusClassMap[$demande->status] ?? 'pending' }}">{{ $statusLabels[$demande->status] ?? $demande->status }}</span>
            </div>
        </div>

        <div class="panel">
            <span class="eyebrow">Rattachement</span>
            <h2>Acteurs du dossier</h2>
            <ul class="list">
                <li><strong>Citoyen</strong><br><span class="muted">{{ $ownerName }} - {{ $demande->email }}</span></li>
                @if ($demande->citoyen?->citizen_number)
                    <li><strong>N°CIT</strong><br><span class="muted">{{ $demande->citoyen->citizen_number }}</span></li>
                @endif
                <li><strong>Agent</strong><br><span class="muted">{{ $agentName }}</span></li>
                <li><strong>Source</strong><br><span class="muted">{{ $demande->source }}</span></li>
            </ul>
        </div>
    </section>

    <section class="grid grid--2 section">
        <div class="panel">
            <span class="eyebrow">Informations demandees</span>
            <h2>Details du dossier</h2>
            <ul class="list">
                <li><strong>Date de naissance</strong><br><span class="muted">{{ optional($demande->birth_date)->format('d/m/Y') }}</span></li>
                <li><strong>Lieu de naissance</strong><br><span class="muted">{{ $demande->birth_place }}</span></li>
                <li><strong>Numero de registre</strong><br><span class="muted">{{ $demande->register_number }}</span></li>
                <li><strong>Adresse</strong><br><span class="muted">{{ $demande->address }}</span></li>
                <li><strong>Paiement</strong><br><span class="muted">{{ $demande->payment_status === 'paid' ? 'Regle' : 'En attente' }} - {{ number_format($paymentAmount, 0, ',', ' ') }} FCFA</span></li>
                @if ($demande->payment_status === 'paid')
                    <li><strong>Reference paiement</strong><br><span class="muted">{{ $demande->payment_reference }} @if($demande->paid_at) le {{ $demande->paid_at->format('d/m/Y H:i') }} @endif</span></li>
                @endif
                <li><strong>Parent 1</strong><br><span class="muted">{{ $demande->parent_one_first_name }} {{ $demande->parent_one_last_name }}</span></li>
                <li><strong>Parent 2</strong><br><span class="muted">{{ $demande->parent_two_first_name }} {{ $demande->parent_two_last_name }}</span></li>
                @if ($demande->details)
                    <li><strong>Precisions</strong><br><span class="muted">{{ $demande->details }}</span></li>
                @endif
                @if ($demande->agent_notes)
                    <li><strong>Notes de traitement</strong><br><span class="muted">{{ $demande->agent_notes }}</span></li>
                @endif
                <li><strong>Mode de traitement</strong><br><span class="muted">{{ $processingChannel === 'online' ? 'En ligne' : 'Au guichet' }}</span></li>
                @if ($hasProcessedDocument && $demande->status === 'completed' && $processingChannel === 'online')
                    <li>
                        <strong>Document traite</strong><br>
                        <a class="button button--ghost" href="{{ route('portal.demandes.document.download', $demande) }}">Telecharger le document final</a>
                    </li>
                @endif
            </ul>
        </div>

        <div class="panel">
            <span class="eyebrow">Messagerie</span>
            <h2>Echanges sur le dossier</h2>
            @if ($demande->messages->isEmpty())
                <p class="muted">Aucun message pour le moment.</p>
            @else
                <div class="message-list">
                    @foreach ($demande->messages as $message)
                        <article class="message">
                            <div class="message__meta">
                                <strong>{{ trim(($message->sender->first_name ?? '') . ' ' . ($message->sender->last_name ?? '')) ?: ($message->sender->name ?? 'Utilisateur') }}</strong>
                                <span>{{ optional($message->created_at)->format('d/m/Y H:i') }}</span>
                            </div>
                            <div>{{ $message->body }}</div>
                        </article>
                    @endforeach
                </div>
            @endif

            <form action="{{ route('portal.demandes.messages.store', $demande) }}" method="post" class="tw:mt-[18px]">
                @csrf
                <div class="field">
                    <label for="message-body">Nouveau message</label>
                    <textarea id="message-body" name="body" required>{{ old('body') }}</textarea>
                </div>
                <button class="button button--primary" type="submit">Envoyer</button>
            </form>

            @if ($currentUser->isCitoyen() && $demande->payment_status !== 'paid')
                <form action="{{ route('portal.citizen.demandes.pay', $demande) }}" method="post" class="tw:mt-3">
                    @csrf
                    <button class="button button--accent" type="submit">Payer {{ number_format($paymentAmount, 0, ',', ' ') }} FCFA</button>
                </form>
            @endif

            @if ($currentUser->isCitoyen() && $demande->status === 'completed' && $processingChannel === 'online' && $hasProcessedDocument)
                <div class="tw:mt-3">
                    <a class="button button--primary" href="{{ route('portal.demandes.document.download', $demande) }}">Telecharger mon document traite</a>
                </div>
            @endif
        </div>
    </section>

    @if ($currentUser->isAgent() || $currentUser->isAdmin())
        <section class="grid grid--2 section">
            <div class="panel">
                <span class="eyebrow">Affectation</span>
                <h2>Prise en charge</h2>
                <form action="{{ route('portal.demandes.assign', $demande) }}" method="post">
                    @csrf
                    @if ($currentUser->isAdmin())
                        <div class="field">
                            <label for="agent_id">Assigner a un agent</label>
                            <select id="agent_id" name="agent_id" required>
                                @foreach ($agentOptions as $agentOption)
                                    @php
                                        $agentOptionName = trim(($agentOption->first_name ?? '') . ' ' . ($agentOption->last_name ?? '')) ?: $agentOption->name;
                                    @endphp
                                    <option value="{{ $agentOption->id }}" @selected((int) $demande->agent_id === (int) $agentOption->id)>{{ $agentOptionName }}</option>
                                @endforeach
                            </select>
                        </div>
                    @else
                        <p class="muted">Attribuez ce dossier a votre compte pour le sortir de la file d attente.</p>
                    @endif
                    <button class="button button--accent" type="submit">Assigner le dossier</button>
                </form>
            </div>

            <div class="panel">
                <span class="eyebrow">Traitement</span>
                <h2>Mettre a jour le statut</h2>
                <form action="{{ route('portal.demandes.process', $demande) }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="field">
                        <label for="demande-status">Statut</label>
                        <select id="demande-status" name="status" required>
                            <option value="processing" @selected($demande->status === 'processing')>En cours</option>
                            <option value="completed" @selected($demande->status === 'completed')>Traitee</option>
                            <option value="rejected" @selected($demande->status === 'rejected')>Rejetee</option>
                        </select>
                    </div>
                    <div class="field">
                        <label for="processing_channel">Mode de traitement</label>
                        <select id="processing_channel" name="processing_channel" required>
                            <option value="counter" @selected(old('processing_channel', $processingChannel) === 'counter')>Traitement au guichet</option>
                            <option value="online" @selected(old('processing_channel', $processingChannel) === 'online')>Traitement en ligne (telechargement citoyen)</option>
                        </select>
                        <small class="muted">Le traitement en ligne necessite une signature numerique du maire configuree dans Parametres.</small>
                    </div>
                    <div class="field">
                        <label for="processed_document">Document final traite (PDF ou Word)</label>
                        <input id="processed_document" type="file" name="processed_document" accept=".pdf,.doc,.docx">
                        @if ($hasProcessedDocument)
                            <small class="muted">Un document est deja disponible: <a href="{{ route('portal.demandes.document.download', $demande) }}">telecharger l actuel</a></small>
                        @endif
                    </div>
                    @if (! $hasMayorSignature)
                        <p class="muted">Aucune signature numerique du maire n est configuree pour le traitement en ligne.</p>
                    @endif
                    <div class="field">
                        <label for="agent_notes">Notes agent</label>
                        <textarea id="agent_notes" name="agent_notes">{{ old('agent_notes', $demande->agent_notes) }}</textarea>
                    </div>
                    <button class="button button--primary" type="submit">Enregistrer</button>
                </form>
            </div>
        </section>
    @endif
@endsection
