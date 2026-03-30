@extends('portal.layout')

@section('content')
    <section class="hero">
        <div class="hero__panel">
            <span class="eyebrow">Superviseur</span>
            <h1 class="tw:font-semibold">Pointage des agents</h1>
            <p class="muted">Saisissez et suivez la presence quotidienne des agents.</p>
        </div>
    </section>

    <section class="section">
        <div class="grid grid--3 tw:mb-[18px]">
            <div class="panel tw:text-center tw:bg-white tw:shadow-sm tw:rounded-lg">
                <div class="kpi-number tw:text-xl md:tw:text-2xl tw:font-bold">{{ $todaySummary['present'] }}</div>
                <div class="badge completed tw:mt-1.5 tw:text-xs tw:font-medium">Presents aujourd'hui</div>
            </div>
            <div class="panel tw:text-center tw:bg-white tw:shadow-sm tw:rounded-lg">
                <div class="kpi-number tw:text-xl md:tw:text-2xl tw:font-bold">{{ $todaySummary['late'] }}</div>
                <div class="badge pending tw:mt-1.5 tw:text-xs tw:font-medium">Retards aujourd'hui</div>
            </div>
            <div class="panel tw:text-center tw:bg-white tw:shadow-sm tw:rounded-lg">
                <div class="kpi-number tw:text-xl md:tw:text-2xl tw:font-bold">{{ $todaySummary['absent'] }}</div>
                <div class="badge rejected tw:mt-1.5 tw:text-xs tw:font-medium">Absents aujourd'hui</div>
            </div>
        </div>

        <div class="grid grid--2">
            <div class="panel">
                <span class="eyebrow">Saisie</span>
                <h2 class="tw:font-semibold">Nouveau pointage</h2>
                <form action="{{ route('portal.superviseur.attendance.store') }}" method="post">
                    @csrf
                    <div class="form-grid">
                        <div class="field">
                            <label>Agent</label>
                            <select name="user_id" required>
                                <option value="">Choisir un agent</option>
                                @foreach ($attendanceAgents as $agent)
                                    @php $agentName = trim(($agent->first_name ?? '') . ' ' . ($agent->last_name ?? '')) ?: $agent->name; @endphp
                                    <option value="{{ $agent->id }}" @selected((int) old('user_id') === (int) $agent->id)>{{ $agentName }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="field">
                            <label>Date</label>
                            <input type="date" name="attendance_date" value="{{ old('attendance_date', $attendanceDate) }}" required>
                        </div>
                        <div class="field">
                            <label>Statut</label>
                            <select name="status" required>
                                <option value="present" @selected(old('status') === 'present')>Present</option>
                                <option value="late" @selected(old('status') === 'late')>Retard</option>
                                <option value="absent" @selected(old('status') === 'absent')>Absent</option>
                            </select>
                        </div>
                        <div class="field">
                            <label>Heure d'arrivee</label>
                            <input type="time" name="check_in_time" value="{{ old('check_in_time') }}">
                        </div>
                        <div class="field">
                            <label>Heure de sortie</label>
                            <input type="time" name="check_out_time" value="{{ old('check_out_time') }}">
                        </div>
                        <div class="field field--full">
                            <label>Note (optionnel)</label>
                            <textarea name="notes" rows="2">{{ old('notes') }}</textarea>
                        </div>
                    </div>
                    <button class="button button--primary" type="submit">Enregistrer le pointage</button>
                </form>
            </div>

            <div class="panel">
                <span class="eyebrow">Historique</span>
                <h2 class="tw:font-semibold">Derniers pointages</h2>
                @if ($recentAttendances->isEmpty())
                    <p class="muted">Aucun pointage enregistre.</p>
                @else
                    <div class="table-card tw:mt-2.5">
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
    </section>
@endsection
