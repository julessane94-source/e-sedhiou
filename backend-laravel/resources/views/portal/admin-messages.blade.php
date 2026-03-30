@extends('portal.layout')

@section('content')
    @include('portal.components.admin-nav')

    <section class="hero">
        <div class="hero__panel">
            <span class="eyebrow">Administration</span>
            <div class="tw:overflow-hidden tw:h-12 tw:flex tw:items-center">
                <h1 class="slide-animate">Messagerie</h1>
            </div>
            <p class="muted">Consultez tous les messages echanges entre citoyens et agents.</p>
        </div>

        <div class="panel">
            <span class="eyebrow">Vue d'ensemble</span>
            <h2>Centre de communication</h2>
            <div class="fact-grid">
                <div class="fact-card">
                    <strong>Messages WordPress</strong>
                    <span>{{ $wpContactMessages->total() }} element(s) synchronises depuis le site public.</span>
                </div>
                <div class="fact-card">
                    <strong>Echanges internes</strong>
                    <span>{{ $messages->total() }} conversation(s) visibles dans l'espace d'administration.</span>
                </div>
                <div class="fact-card">
                    <strong>Envoi manuel</strong>
                    <span>Composez un message vers un utilisateur et rattachez-le si besoin a un dossier.</span>
                </div>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="section-stack">
        <div class="panel">
            <div class="panel-header">
                <div class="panel-header__title">
                    <span class="eyebrow">Messages WordPress</span>
                    <h2>Messages recus depuis le formulaire de contact</h2>
                </div>
                <span class="panel-header__meta">{{ $wpContactMessages->total() }} message(s)</span>
            </div>

            @if ($wpContactMessages->isEmpty())
                <p class="muted">Aucun message WordPress synchronise pour le moment.</p>
            @else
                <div class="message-list tw:mb-[18px]">
                    @foreach ($wpContactMessages as $contact)
                        @php
                            $replyBadgeClass = match ($contact->reply_status) {
                                'sent' => 'completed',
                                'failed' => 'rejected',
                                default => 'pending',
                            };
                            $replyLabel = match ($contact->reply_status) {
                                'sent' => 'Repondu',
                                'failed' => 'Echec envoi',
                                default => 'En attente',
                            };
                        @endphp
                        <article class="message">
                            <div class="message__meta tw:items-start tw:gap-2.5 tw:flex-wrap">
                                <span>
                                    <strong>{{ $contact->sender_name }}</strong>
                                    — <a href="mailto:{{ $contact->sender_email }}">{{ $contact->sender_email }}</a>
                                    <span class="badge {{ $replyBadgeClass }} tw:text-[0.75em] tw:ml-2">{{ $replyLabel }}</span>
                                </span>
                                <span>{{ optional($contact->received_at ?? $contact->created_at)->format('d/m/Y H:i') }}</span>
                            </div>

                            <p class="tw:mb-2"><strong>Objet:</strong> {{ $contact->subject ?: 'Sans objet' }}</p>
                            <div class="tw:whitespace-pre-wrap">{{ $contact->message }}</div>

                            @if ($contact->reply_status === 'sent')
                                <p class="muted tw:mt-2.5 tw:mb-0">
                                    Derniere reponse envoyee le {{ optional($contact->replied_at)->format('d/m/Y H:i') }}
                                    @if ($contact->repliedBy)
                                        par {{ trim(($contact->repliedBy->first_name ?? '') . ' ' . ($contact->repliedBy->last_name ?? '')) ?: $contact->repliedBy->name }}
                                    @endif
                                </p>
                            @endif

                            @if ($contact->reply_status === 'failed' && $contact->reply_error)
                                <p class="tw:text-red-600 tw:mt-2.5 tw:mb-0">Erreur: {{ $contact->reply_error }}</p>
                            @endif

                            <form action="{{ route('portal.admin.messages.wordpress.reply', $contact) }}" method="post" class="tw:mt-3">
                                @csrf
                                <div class="form-grid">
                                    <div class="field field--full">
                                        <label>Objet de la reponse</label>
                                        <input type="text" name="subject" value="Re: {{ $contact->subject ?: 'Votre message' }}">
                                    </div>
                                    <div class="field field--full">
                                        <label>Votre reponse</label>
                                        <textarea name="body" rows="4" required>Bonjour {{ $contact->sender_name }},

Merci pour votre message.

Cordialement,
Service mairie</textarea>
                                    </div>
                                </div>
                                <button class="button button--accent" type="submit">Repondre par email</button>
                            </form>
                        </article>
                    @endforeach
                </div>

                <div class="tw:mb-[18px]">{{ $wpContactMessages->links() }}</div>
            @endif
        </div>

        <div class="panel">
            <div class="panel-header">
                <div class="panel-header__title">
                    <span class="eyebrow">Nouveau message</span>
                    <h2>Composer un message</h2>
                </div>
            </div>
            <form action="{{ route('portal.messages.store') }}" method="post">
                @csrf
                <div class="form-grid">
                    <div class="field">
                        <label>Destinataire</label>
                        <select name="receiver_id" required>
                            <option value="">Choisir un utilisateur</option>
                            @foreach ($messageUsers as $user)
                                @php
                                    $userName = trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? '')) ?: $user->name;
                                    $userRole = match ($user->role) {
                                        'admin' => 'Admin',
                                        'agent' => 'Agent',
                                        'superviseur' => 'Superviseur',
                                        default => 'Citoyen',
                                    };
                                @endphp
                                <option value="{{ $user->id }}" @selected(old('receiver_id') == $user->id)>{{ $userName }} — {{ $userRole }}</option>
                            @endforeach
                        </select>
                        @error('receiver_id')<small class="tw:text-xs tw:text-red-600">{{ $message }}</small>@enderror
                    </div>
                    <div class="field">
                        <label>Dossier lié (optionnel)</label>
                        <select name="demande_id">
                            <option value="">Aucun dossier</option>
                            @foreach ($messageDemandes as $demande)
                                @php
                                    $citizenName = trim(($demande->citoyen->first_name ?? '') . ' ' . ($demande->citoyen->last_name ?? '')) ?: ($demande->citoyen->name ?? 'Citoyen');
                                @endphp
                                <option value="{{ $demande->id }}" @selected(old('demande_id') == $demande->id)>{{ $demande->reference }} — {{ $citizenName }}</option>
                            @endforeach
                        </select>
                        @error('demande_id')<small class="tw:text-xs tw:text-red-600">{{ $message }}</small>@enderror
                    </div>
                    <div class="field field--full">
                        <label>Message</label>
                        <textarea name="body" rows="4" required>{{ old('body') }}</textarea>
                        @error('body')<small class="tw:text-xs tw:text-red-600">{{ $message }}</small>@enderror
                    </div>
                </div>
                <button class="button button--primary" type="submit">Envoyer le message</button>
            </form>
        </div>

        <div class="panel">
            <div class="panel-header">
                <div class="panel-header__title">
                    <span class="eyebrow">{{ $messages->total() }} message(s)</span>
                    <h2>Tous les echanges</h2>
                </div>
            </div>

            @if ($messages->isEmpty())
                <p class="muted">Aucun message.</p>
            @else
                <div class="message-list">
                    @foreach ($messages as $msg)
                        @php
                            $senderName = trim(($msg->sender->first_name ?? '') . ' ' . ($msg->sender->last_name ?? '')) ?: ($msg->sender->name ?? 'Utilisateur');
                            $receiverName = trim(($msg->receiver->first_name ?? '') . ' ' . ($msg->receiver->last_name ?? '')) ?: ($msg->receiver->name ?? 'Utilisateur');
                            $roleLabel  = match ($msg->sender->role ?? '') {
                                'admin'   => 'Admin',
                                'agent'   => 'Agent',
                                'superviseur' => 'Superviseur',
                                default   => 'Citoyen',
                            };
                            $receiverRoleLabel = match ($msg->receiver->role ?? '') {
                                'admin'   => 'Admin',
                                'agent'   => 'Agent',
                                'superviseur' => 'Superviseur',
                                default   => 'Citoyen',
                            };
                        @endphp
                        <article class="message">
                            <div class="message__meta">
                                <span>
                                    <strong>{{ $senderName }}</strong>
                                    <span class="badge {{ $msg->sender->role ?? 'pending' }} tw:text-[0.75em] tw:ml-1.5">{{ $roleLabel }}</span>
                                    →
                                    <strong>{{ $receiverName }}</strong>
                                    <span class="badge {{ $msg->receiver->role ?? 'pending' }} tw:text-[0.75em] tw:ml-1.5">{{ $receiverRoleLabel }}</span>
                                    @if ($msg->demande)
                                        — dossier <a href="{{ route('portal.demandes.show', $msg->demande) }}">{{ $msg->demande->reference }}</a>
                                    @endif
                                </span>
                                <span>{{ optional($msg->created_at)->format('d/m/Y H:i') }}</span>
                            </div>
                            <div>{{ $msg->body }}</div>
                        </article>
                    @endforeach
                </div>

                <div class="tw:mt-4">{{ $messages->links() }}</div>
            @endif
        </div>
        </div>
    </section>
@endsection
