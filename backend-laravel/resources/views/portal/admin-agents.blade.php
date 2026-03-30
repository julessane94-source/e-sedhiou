@extends('portal.layout')

@section('content')
    @include('portal.components.admin-nav')

    <section class="hero">
        <div class="hero__panel">
            <span class="eyebrow">Administration</span>
            <div class="tw:overflow-hidden tw:h-12 tw:flex tw:items-center">
                <h1 class="tw:font-semibold slide-animate">Gestion des agents et superviseurs</h1>
            </div>
            <p class="muted">Inscrivez, modifiez et activez les agents et superviseurs du portail.</p>
        </div>

        <div class="panel">
            <div class="panel-header">
                <div class="panel-header__title">
                    <span class="eyebrow">Nouveau compte interne</span>
                    <h2 class="tw:font-semibold">Inscrire un agent ou un superviseur</h2>
                </div>
            </div>
            <form action="{{ route('portal.admin.agents.store') }}" method="post">
                @csrf
                <div class="form-grid">
                    <div class="field">
                        <label for="ag-role">Role</label>
                        <select id="ag-role" name="role" required>
                            <option value="agent" @selected(old('role', 'agent') === 'agent')>Agent</option>
                            <option value="superviseur" @selected(old('role') === 'superviseur')>Superviseur</option>
                        </select>
                    </div>
                    <div class="field">
                        <label for="ag-fn">Prenom</label>
                        <input id="ag-fn" type="text" name="first_name" value="{{ old('first_name') }}" placeholder="Prenom">
                    </div>
                    <div class="field">
                        <label for="ag-ln">Nom</label>
                        <input id="ag-ln" type="text" name="last_name" value="{{ old('last_name') }}" placeholder="Nom">
                    </div>
                    <div class="field">
                        <label for="ag-email">Email <span class="muted">*</span></label>
                        <input id="ag-email" type="email" name="email" value="{{ old('email') }}" required>
                    </div>
                    <div class="field">
                        <label for="ag-pw">Mot de passe <span class="muted">*</span></label>
                        <input id="ag-pw" type="password" name="password" required autocomplete="new-password">
                    </div>
                </div>
                <button class="button button--primary tw:mt-3" type="submit">Inscrire le compte</button>
            </form>
        </div>
    </section>

    <section class="section">
        <div class="metric-strip tw:mb-5">
            @php
                $agentsCount = $agents->where('role', 'agent')->count();
                $superviseursCount = $agents->where('role', 'superviseur')->count();
                $activeCount = $agents->where('is_active', true)->count();
                $inactiveCount = $agents->where('is_active', false)->count();
            @endphp
            @foreach ([
                ['label' => 'Comptes internes', 'val' => $agents->count(), 'class' => 'assigned'],
                ['label' => 'Agents', 'val' => $agentsCount, 'class' => 'processing'],
                ['label' => 'Superviseurs', 'val' => $superviseursCount, 'class' => 'pending'],
                ['label' => 'Actifs', 'val' => $activeCount . ' / ' . $inactiveCount . ' inactifs', 'class' => 'completed'],
            ] as $card)
                <div class="metric-tile tw:bg-white tw:shadow-sm tw:rounded-lg">
                    <span class="metric-tile__label tw:text-xs tw:text-gray-500 tw:font-medium">{{ $card['label'] }}</span>
                    <strong class="metric-tile__value tw:text-xl md:tw:text-2xl tw:font-bold">{{ $card['val'] }}</strong>
                    <div class="metric-tile__meta">
                        <span class="badge {{ $card['class'] }}">{{ $card['label'] }}</span>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="panel">
            <div class="panel-header">
                <div class="panel-header__title">
                    <span class="eyebrow">{{ $agents->count() }} compte(s)</span>
                    <h2 class="tw:font-semibold">Liste des agents et superviseurs</h2>
                </div>
                <span class="panel-header__meta">Cliquer sur un compte pour modifier ses parametres</span>
            </div>

            @if ($agents->isEmpty())
                <p class="muted">Aucun compte interne enregistre.</p>
            @else
                @foreach ($agents as $agent)
                    @php $agentName = trim(($agent->first_name ?? '') . ' ' . ($agent->last_name ?? '')) ?: $agent->name; @endphp
                    <details class="details-card">
                        <summary>
                            <span>
                                {{ $agentName }} — <span class="muted">{{ $agent->email }}</span>
                            </span>
                            <span class="details-card__summary-meta">
                                <span class="badge assigned">{{ $agent->role === 'superviseur' ? 'Superviseur' : 'Agent' }}</span>
                                <span class="badge {{ $agent->is_active ? 'completed' : 'rejected' }}">
                                    {{ $agent->is_active ? 'Actif' : 'Inactif' }}
                                </span>
                            </span>
                        </summary>

                        <div class="details-card__content">
                            <form action="{{ route('portal.admin.agents.update', $agent) }}" method="post">
                                @csrf
                                <div class="form-grid">
                                    <div class="field">
                                        <label>Prenom</label>
                                        <input type="text" name="first_name" value="{{ $agent->first_name }}">
                                    </div>
                                    <div class="field">
                                        <label>Nom</label>
                                        <input type="text" name="last_name" value="{{ $agent->last_name }}">
                                    </div>
                                    <div class="field">
                                        <label>Email</label>
                                        <input type="email" name="email" value="{{ $agent->email }}" required>
                                    </div>
                                    <div class="field">
                                        <label>Nouveau mot de passe <span class="muted">(laisser vide)</span></label>
                                        <input type="password" name="password" autocomplete="new-password">
                                    </div>
                                </div>
                                <div class="field tw:mt-2.5">
                                    <label class="tw:flex tw:items-center tw:gap-2 tw:cursor-pointer">
                                        <input type="hidden" name="is_active" value="0">
                                        <input type="checkbox" name="is_active" value="1" {{ $agent->is_active ? 'checked' : '' }}>
                                        Compte actif
                                    </label>
                                </div>
                                <div class="actions tw:mt-3">
                                    <button class="button button--accent" type="submit">Enregistrer</button>
                                </div>
                            </form>

                                <form action="{{ route('portal.admin.agents.delete', $agent) }}" method="post" class="tw:mt-2"
                                  onsubmit="return confirm('Supprimer cet agent ?');">
                                @csrf
                                <button class="button button--danger" type="submit">Supprimer</button>
                            </form>
                        </div>
                    </details>
                @endforeach
            @endif
        </div>
    </section>
@endsection
