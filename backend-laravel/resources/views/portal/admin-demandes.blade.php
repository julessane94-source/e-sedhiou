@extends('portal.layout')

@section('content')
    @include('portal.components.admin-nav')

    @php
        $activeFilters = (request('status') ? 1 : 0) + (request('type') ? 1 : 0);
    @endphp

    <section class="hero">
        <div class="hero__panel">
            <span class="eyebrow">Administration</span>
            <h1 class="tw:font-semibold">Suivi des demandes</h1>
            <p class="muted">Filtrez, consultez et pilotez toutes les demandes du portail.</p>
        </div>

        <div class="panel">
            <div class="panel-header">
                <div class="panel-header__title">
                    <span class="eyebrow">Filtres</span>
                    <h2 class="tw:font-semibold">Recherche</h2>
                </div>
                <span class="panel-header__meta">Affichage transversal des dossiers</span>
            </div>
            <form method="get" action="{{ route('portal.admin.demandes') }}" class="admin-toolbar">
                <div class="field">
                    <label for="f-status">Statut</label>
                    <select id="f-status" name="status">
                        <option value="">Tous</option>
                        @foreach ($statusLabels as $key => $label)
                            <option value="{{ $key }}" @selected(request('status') === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="field">
                    <label for="f-type">Type</label>
                    <select id="f-type" name="type">
                        <option value="">Tous</option>
                        @foreach ($requestTypes as $key => $label)
                            <option value="{{ $key }}" @selected(request('type') === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="actions">
                    <button class="button button--accent" type="submit">Filtrer</button>
                    <a class="button button--ghost" href="{{ route('portal.admin.demandes') }}">Reinitialiser</a>
                </div>
            </form>
        </div>
    </section>

    <section class="section">
        <div class="metric-strip tw:mb-6">
            @foreach ([['label'=>'Total','val'=>$counts['total'],'class'=>'assigned'],['label'=>'En attente','val'=>$counts['pending'],'class'=>'pending'],['label'=>'En cours','val'=>$counts['processing'],'class'=>'processing'],['label'=>'Traitees','val'=>$counts['completed'],'class'=>'completed']] as $card)
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
                    <span class="eyebrow">{{ $demandes->total() }} dossier(s)</span>
                    <h2 class="tw:font-semibold">Demandes</h2>
                </div>
                <span class="panel-header__meta">Filtres actifs: {{ $activeFilters }}</span>
            </div>

            @if ($demandes->isEmpty())
                <p class="muted">Aucune demande.</p>
            @else
                <div class="table-card admin-table">
                <table class="tw:text-[0.92em]">
                    <thead>
                        <tr>
                            <th>Reference</th>
                            <th>Citoyen</th>
                            <th>Type</th>
                            <th>Agent</th>
                            <th>Statut</th>
                            <th>Date</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($demandes as $d)
                            @php
                                $cName = trim(($d->citoyen->first_name ?? '') . ' ' . ($d->citoyen->last_name ?? '')) ?: ($d->citoyen->name ?? '—');
                                $aName = $d->agent ? (trim(($d->agent->first_name ?? '') . ' ' . ($d->agent->last_name ?? '')) ?: $d->agent->name) : '—';
                            @endphp
                            <tr>
                                <td><strong>{{ $d->reference }}</strong></td>
                                <td>{{ $cName }}</td>
                                <td>{{ $requestTypes[$d->request_type] ?? $d->request_type }}</td>
                                <td>{{ $aName }}</td>
                                <td><span class="badge {{ $d->status }}">{{ $statusLabels[$d->status] ?? $d->status }}</span></td>
                                <td>{{ optional($d->created_at)->format('d/m/Y') }}</td>
                                <td><a class="button button--ghost" href="{{ route('portal.demandes.show', $d) }}">Voir</a></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                </div>

                <div class="tw:mt-4">{{ $demandes->withQueryString()->links() }}</div>
            @endif
        </div>
    </section>
@endsection
