@extends('portal.layout')

@section('content')
    <section class="hero">
        <div class="hero__panel">
            <span class="eyebrow">Espace agent</span>
            <h1 class="tw:font-semibold">Mes performances</h1>
            <p class="muted">Suivi de votre productivite et de vos pointages ({{ $periodLabel }}).</p>
            <div class="actions tw:mt-3">
                <a class="button button--ghost" href="{{ route('portal.agent') }}">Retour a mon tableau de bord</a>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="grid grid--4 tw:mb-6">
            <div class="panel tw:text-center tw:bg-white tw:shadow-sm tw:rounded-lg">
                <div class="kpi-number tw:text-xl md:tw:text-2xl tw:font-bold">{{ $stats['total'] }}</div>
                <div class="badge assigned tw:text-xs tw:font-medium">Dossiers assignes</div>
            </div>
            <div class="panel tw:text-center tw:bg-white tw:shadow-sm tw:rounded-lg">
                <div class="kpi-number tw:text-xl md:tw:text-2xl tw:font-bold">{{ $agentPerformance['completed'] }}</div>
                <div class="badge completed tw:text-xs tw:font-medium">Traites</div>
            </div>
            <div class="panel tw:text-center tw:bg-white tw:shadow-sm tw:rounded-lg">
                <div class="kpi-number tw:text-xl md:tw:text-2xl tw:font-bold">{{ $agentPerformance['monthly_completed'] }}</div>
                <div class="badge processing tw:text-xs tw:font-medium">Traites ce mois</div>
            </div>
            <div class="panel tw:text-center tw:bg-white tw:shadow-sm tw:rounded-lg">
                <div class="kpi-number tw:text-xl md:tw:text-2xl tw:font-bold">{{ $agentPerformance['completion_rate'] }} %</div>
                <div class="badge pending tw:text-xs tw:font-medium">Taux de reussite</div>
            </div>
        </div>

        <div class="grid grid--2 tw:mb-6">
            <div class="panel">
                <span class="eyebrow">Traitement</span>
                <h2 class="tw:font-semibold">Indicateurs metier</h2>
                <ul class="list">
                    <li><strong>Dossiers rejetes</strong><br><span class="muted">{{ $agentPerformance['rejected'] }}</span></li>
                    <li><strong>Delai moyen de traitement</strong><br><span class="muted">{{ $agentPerformance['avg_processing_hours'] !== null ? ($agentPerformance['avg_processing_hours'] . ' h') : 'Non disponible' }}</span></li>
                    <li><strong>Dossiers en attente</strong><br><span class="muted">{{ $stats['pending'] }}</span></li>
                    <li><strong>Dossiers en cours</strong><br><span class="muted">{{ $stats['processing'] }}</span></li>
                </ul>
            </div>

            <div class="panel">
                <span class="eyebrow">Pointage</span>
                <h2 class="tw:font-semibold">Presence mensuelle</h2>
                <ul class="list">
                    <li><strong>Presences</strong><br><span class="muted">{{ $attendanceSummary['present'] }}</span></li>
                    <li><strong>Retards</strong><br><span class="muted">{{ $attendanceSummary['late'] }}</span></li>
                    <li><strong>Absences</strong><br><span class="muted">{{ $attendanceSummary['absent'] }}</span></li>
                </ul>
            </div>
        </div>

        <div class="panel">
            <span class="eyebrow">Historique</span>
            <h2 class="tw:font-semibold">Mes pointages recents</h2>
            @if ($attendanceRows->isEmpty())
                <p class="muted">Aucun pointage enregistre pour ce mois.</p>
            @else
                <div class="table-card tw:mt-2.5">
                    <table>
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Statut</th>
                                <th>Heure d'arrivee</th>
                                <th>Heure de sortie</th>
                                <th>Note</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($attendanceRows as $row)
                                <tr>
                                    <td>{{ optional($row->attendance_date)->format('d/m/Y') }}</td>
                                    <td>{{ $attendanceStatusLabels[$row->status] ?? ucfirst($row->status) }}</td>
                                    <td>{{ $row->check_in_time ?: '--:--' }}</td>
                                    <td>{{ $row->check_out_time ?: '--:--' }}</td>
                                    <td>{{ $row->notes ?: '—' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </section>
@endsection
