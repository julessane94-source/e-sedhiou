@extends('portal.layout')

@section('content')
    @include('portal.components.admin-nav')

    <section class="hero">
        <div class="hero__panel">
            <span class="eyebrow">Administration</span>
            <div class="tw:overflow-hidden tw:h-12 tw:flex tw:items-center">
                <h1 class="tw:font-semibold slide-animate">Statistiques</h1>
            </div>
            <p class="muted">Activite globale du portail.</p>
            <div class="hero__actions">
                <a class="button button--primary" href="{{ route('portal.admin.documents.architecture.download') }}">
                    Telecharger le document d'architecture (PDF)
                </a>
            </div>
            <p class="panel-note">Le PDF est genere puis le document source est supprime automatiquement apres ce telechargement.</p>
        </div>

        <div class="panel">
            <span class="eyebrow">Pilotage</span>
            <h2 class="tw:font-semibold">Vue d'ensemble</h2>
            <div class="fact-grid">
                <div class="fact-card">
                    <strong>Charge globale</strong>
                    <span>{{ $global['total'] }} dossiers suivis sur le portail.</span>
                </div>
                <div class="fact-card">
                    <strong>Rythme de traitement</strong>
                    <span>{{ $global['completed'] }} demandes bouclees et {{ $global['pending'] }} encore en attente.</span>
                </div>
                <div class="fact-card">
                    <strong>Population active</strong>
                    <span>{{ $global['citoyens'] }} citoyens inscrits et un suivi RH centralise.</span>
                </div>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="metric-strip tw:mb-7">
            @foreach ([
                ['label' => 'Demandes totales', 'val' => $global['total'],     'class' => 'assigned'],
                ['label' => 'Traitees',          'val' => $global['completed'], 'class' => 'completed'],
                ['label' => 'En attente',         'val' => $global['pending'],   'class' => 'pending'],
                ['label' => 'Citoyens inscrits',  'val' => $global['citoyens'], 'class' => 'processing'],
            ] as $kpi)
                <div class="metric-tile tw:bg-white tw:shadow-sm tw:rounded-lg">
                    <span class="metric-tile__label tw:text-xs tw:text-gray-500 tw:font-medium">{{ $kpi['label'] }}</span>
                    <strong class="metric-tile__value tw:text-xl md:tw:text-2xl tw:font-bold">{{ $kpi['val'] }}</strong>
                    <div class="metric-tile__meta">
                        <span class="badge {{ $kpi['class'] }}">{{ $kpi['label'] }}</span>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="grid grid--2 tw:mb-[22px]">
            <div class="panel">
                <div class="panel-header">
                    <div class="panel-header__title">
                        <span class="eyebrow">Ressources humaines</span>
                        <h2 class="tw:font-semibold">Pointage des agents</h2>
                    </div>
                </div>
                <p class="muted">
                    La saisie du pointage est desormais reservee au superviseur.
                    Cette page permet uniquement le suivi global des presences.
                </p>
            </div>

            <div class="panel">
                <div class="panel-header">
                    <div class="panel-header__title">
                        <span class="eyebrow">Derniers pointages</span>
                        <h2 class="tw:font-semibold">Historique recent</h2>
                    </div>
                    <span class="panel-header__meta">{{ $recentAttendances->count() }} entree(s)</span>
                </div>
                @if ($recentAttendances->isEmpty())
                    <p class="muted">Aucun pointage enregistre.</p>
                @else
                    <div class="table-card admin-table">
                        <table>
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Agent</th>
                                    <th>Statut</th>
                                    <th>Plage horaire</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($recentAttendances as $row)
                                    @php $agentName = trim(($row->agent->first_name ?? '') . ' ' . ($row->agent->last_name ?? '')) ?: ($row->agent->name ?? 'Agent'); @endphp
                                    <tr>
                                        <td>{{ optional($row->attendance_date)->format('d/m/Y') }}</td>
                                        <td>{{ $agentName }}</td>
                                        <td>{{ $attendanceStatusLabels[$row->status] ?? ucfirst($row->status) }}</td>
                                        <td>
                                            @if ($row->check_in_time || $row->check_out_time)
                                                {{ $row->check_in_time ?: '--:--' }} - {{ $row->check_out_time ?: '--:--' }}
                                            @else
                                                --
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

        <div class="panel tw:mb-6">
            <div class="panel-header">
                <div class="panel-header__title">
                    <span class="eyebrow">Performance agent</span>
                    <h2 class="tw:font-semibold">Suivi compare des agents (mois en cours)</h2>
                </div>
                <span class="panel-header__meta">{{ $agentPerformanceRows->count() }} agent(s)</span>
            </div>
            @if ($agentPerformanceRows->isEmpty())
                <p class="muted">Aucun agent disponible.</p>
            @else
                <div class="table-card admin-table">
                    <table>
                        <thead>
                            <tr>
                                <th>Agent</th>
                                <th>Dossiers assignes</th>
                                <th>Traites</th>
                                <th>Rejetes</th>
                                <th>Taux de reussite</th>
                                <th>Traites ce mois</th>
                                <th>Presences</th>
                                <th>Absences</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($agentPerformanceRows as $row)
                                <tr>
                                    <td>{{ $row['name'] }}</td>
                                    <td>{{ $row['assigned_total'] }}</td>
                                    <td>{{ $row['completed'] }}</td>
                                    <td>{{ $row['rejected'] }}</td>
                                    <td>{{ $row['completion_rate'] }} %</td>
                                    <td>{{ $row['monthly_completed'] }}</td>
                                    <td>{{ $row['present_days'] }}</td>
                                    <td>{{ $row['absent_days'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <div class="grid grid--2">
            {{-- Par type de demande --}}
            <div class="panel">
                <div class="panel-header">
                    <div class="panel-header__title">
                        <span class="eyebrow">Repartition</span>
                        <h2 class="tw:font-semibold">Demandes par type</h2>
                    </div>
                </div>
                @if ($byType->isEmpty())
                    <p class="muted">Aucune donnee.</p>
                @else
                    <div class="bar-list">
                    @foreach ($byType as $row)
                        @php $label = $requestTypes[$row->request_type] ?? $row->request_type; @endphp
                        <div class="bar-row">
                            <div class="bar-row__meta">
                                <span>{{ $label }}</span>
                                <strong>{{ $row->total }}</strong>
                            </div>
                            @if ($global['total'] > 0)
                                <div class="bar-row__track">
                                    <div class="bar-row__fill" data-width="{{ round($row->total / $global['total'] * 100) }}"></div>
                                </div>
                            @endif
                        </div>
                    @endforeach
                    </div>
                @endif
            </div>

            <div class="panel">
                <div class="panel-header">
                    <div class="panel-header__title">
                        <span class="eyebrow">Evolution</span>
                        <h2 class="tw:font-semibold">Demandes par mois (12 mois)</h2>
                    </div>
                </div>
                @if ($byMonth->isEmpty())
                    <p class="muted">Aucune donnee.</p>
                @else
                    @php $maxMonth = $byMonth->max('total') ?: 1; @endphp
                    <div class="bar-list">
                    @foreach ($byMonth as $row)
                        <div class="bar-row">
                            <div class="bar-row__meta">
                                <span>{{ $row->month }}</span>
                                <strong>{{ $row->total }}</strong>
                            </div>
                            <div class="bar-row__track">
                                <div class="bar-row__fill bar-row__fill--accent" data-width="{{ round($row->total / $maxMonth * 100) }}"></div>
                            </div>
                        </div>
                    @endforeach
                    </div>
                @endif
            </div>

            <div class="panel">
                <div class="panel-header">
                    <div class="panel-header__title">
                        <span class="eyebrow">Agents</span>
                        <h2 class="tw:font-semibold">Demandes traitees par agent</h2>
                    </div>
                </div>
                @if ($byAgent->isEmpty())
                    <p class="muted">Aucun agent.</p>
                @else
                    @php $maxAgent = $byAgent->max('total') ?: 1; @endphp
                    <div class="bar-list">
                    @foreach ($byAgent as $ag)
                        @php $agName = trim(($ag->first_name ?? '') . ' ' . ($ag->last_name ?? '')) ?: $ag->name; @endphp
                        <div class="bar-row">
                            <div class="bar-row__meta">
                                <span>{{ $agName }}</span>
                                <strong>{{ $ag->total }}</strong>
                            </div>
                            <div class="bar-row__track">
                                <div class="bar-row__fill bar-row__fill--dark" data-width="{{ round($ag->total / $maxAgent * 100) }}"></div>
                            </div>
                        </div>
                    @endforeach
                    </div>
                @endif
            </div>

            <div class="panel">
                <div class="panel-header">
                    <div class="panel-header__title">
                        <span class="eyebrow">Synthese</span>
                        <h2 class="tw:font-semibold">Taux de traitement</h2>
                    </div>
                </div>
                @php
                    $total   = $global['total'] ?: 1;
                    $txOk    = round($global['completed'] / $total * 100);
                    $txKo    = round($global['rejected']  / $total * 100);
                    $txPend  = round($global['pending']   / $total * 100);
                @endphp
                <div class="bar-list">
                @foreach ([
                    ['label' => 'Traitees', 'pct' => $txOk,   'class' => 'bar-row__fill--success'],
                    ['label' => 'Rejetees', 'pct' => $txKo,   'class' => 'bar-row__fill--danger'],
                    ['label' => 'En attente', 'pct' => $txPend,'class' => 'bar-row__fill--warning'],
                ] as $row)
                    <div class="bar-row">
                        <div class="bar-row__meta">
                            <span>{{ $row['label'] }}</span>
                            <strong>{{ $row['pct'] }} %</strong>
                        </div>
                        <div class="bar-row__track">
                            <div class="bar-row__fill {{ $row['class'] }}" data-width="{{ $row['pct'] }}"></div>
                        </div>
                    </div>
                @endforeach
                </div>
            </div>
        </div>
    </section>
@endsection
