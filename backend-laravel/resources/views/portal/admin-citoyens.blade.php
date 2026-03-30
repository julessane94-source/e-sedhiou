@extends('portal.layout')

@section('content')
    @include('portal.components.admin-nav')

    <section class="hero">
        <div class="hero__panel">
            <span class="eyebrow">Administration</span>
            <div class="tw:overflow-hidden tw:h-12 tw:flex tw:items-center">
                <h1 class="tw:font-semibold slide-animate">Liste des citoyens</h1>
            </div>
            <p class="muted">Consultez et exportez la liste des citoyens inscrits.</p>
        </div>

        <div class="panel">
            <div class="panel-header">
                <div class="panel-header__title">
                    <span class="eyebrow">Filtre &amp; Export</span>
                    <h2 class="tw:font-semibold">Options</h2>
                </div>
            </div>

            <form method="get" action="{{ route('portal.admin.citoyens') }}" class="admin-toolbar tw:mb-4 tw:flex tw:gap-4">
                <div class="field tw:min-w-[180px]">
                    <label for="f-type">Type de demande</label>
                    <select id="f-type" name="type">
                        <option value="">Tous</option>
                        @foreach ($requestTypes as $key => $label)
                            <option value="{{ $key }}" @selected($filterType === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="field tw:min-w-[180px]">
                    <label for="f-category">Catégorie</label>
                    <select id="f-category" name="category">
                        <option value="">Toutes</option>
                        <option value="etudiant" @selected(request('category')==='etudiant')>Étudiant</option>
                        <option value="fonctionnaire" @selected(request('category')==='fonctionnaire')>Fonctionnaire</option>
                        <option value="autre" @selected(request('category')==='autre')>Autre</option>
                    </select>
                </div>
                <div class="field tw:min-w-[180px]">
                    <label for="f-location">Localisation</label>
                    <input id="f-location" name="location" type="text" value="{{ request('location') }}" placeholder="Sédhiou ou autre" class="tw-w-full tw-border tw-rounded tw-px-3 tw-py-2">
                </div>
                <div class="actions tw-flex tw-items-end tw-gap-2">
                    <button class="button button--accent" type="submit">Filtrer</button>
                    <a class="button button--ghost" href="{{ route('portal.admin.citoyens') }}">Tous</a>
                </div>
            </form>

            <div class="actions">
                <a class="button button--primary"
                   href="{{ route('portal.admin.citoyens.export', ['type' => $filterType, 'format' => 'csv']) }}">
                    Exporter Excel / CSV
                </a>
                <a class="button button--accent"
                   href="{{ route('portal.admin.citoyens.export', ['type' => $filterType, 'format' => 'pdf']) }}">
                    Exporter PDF
                </a>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="metric-strip tw:mb-5">
            @php
                $activeCitoyens = $citoyens->getCollection()->where('is_active', true)->count();
                $inactiveCitoyens = $citoyens->getCollection()->where('is_active', false)->count();
                $totalDemandesCitoyens = (int) $citoyens->getCollection()->sum('demandes_count');
                $avgDemandes = $citoyens->count() > 0 ? round($totalDemandesCitoyens / $citoyens->count(), 1) : 0;
            @endphp
            @foreach ([
                ['label' => 'Resultats affiches', 'val' => $citoyens->count(), 'class' => 'assigned'],
                ['label' => 'Comptes actifs', 'val' => $activeCitoyens, 'class' => 'completed'],
                ['label' => 'Comptes inactifs', 'val' => $inactiveCitoyens, 'class' => 'rejected'],
                ['label' => 'Moyenne dossiers', 'val' => $avgDemandes, 'class' => 'processing'],
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
                    <span class="eyebrow">{{ $citoyens->total() }} citoyen(s)</span>
                    <h2 class="tw:font-semibold">
                        Citoyens
                        @if ($filterType)
                            <span class="muted tw:font-normal tw:text-[0.85em]">— {{ $requestTypes[$filterType] ?? $filterType }}</span>
                        @endif
                    </h2>
                </div>
            </div>

            @if ($citoyens->isEmpty())
                <p class="muted">Aucun citoyen trouve.</p>
            @else
                <div class="table-card admin-table">
                <table class="tw:text-[0.92em]">
                    <thead>
                        <tr>
                            <th>Nom</th>
                            <th>Email</th>
                            <th>Telephone</th>
                            <th>Adresse</th>
                            <th>Date naiss.</th>
                            <th>Lieu naiss.</th>
                            <th>N registre</th>
                            <th>N citoyen</th>
                            <th>Dossiers</th>
                            <th>Actif</th>
                            <th>Inscrit le</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($citoyens as $c)
                            <tr>
                                <td><strong>{{ trim(($c->first_name ?? '') . ' ' . ($c->last_name ?? '')) ?: $c->name }}</strong></td>
                                <td>{{ $c->email }}</td>
                                <td>{{ $c->phone ?? '—' }}</td>
                                <td class="wrap-pre tw:max-w-[220px]">{{ $c->address ?? '—' }}</td>
                                <td class="nowrap">{{ optional($c->birth_date)->format('d/m/Y') ?? '—' }}</td>
                                <td>{{ $c->birth_place ?? '—' }}</td>
                                <td>{{ $c->register_number ?? '—' }}</td>
                                <td class="nowrap tw:text-[0.82em]">{{ $c->citizen_number ?? '—' }}</td>
                                <td>{{ $c->demandes_count }}</td>
                                <td>
                                    <span class="badge {{ $c->is_active ? 'completed' : 'rejected' }}">{{ $c->is_active ? 'Oui' : 'Non' }}</span>
                                </td>
                                <td class="nowrap">{{ optional($c->created_at)->format('d/m/Y') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                </div>

                <div class="tw:mt-4">{{ $citoyens->withQueryString()->links() }}</div>
            @endif
        </div>
    </section>
@endsection
