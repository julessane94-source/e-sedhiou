@extends('portal.layout')

@section('content')
    @include('portal.components.admin-nav')

    <section class="hero">
        <div class="hero__panel">
            <span class="eyebrow">Administration - Citoyenneté</span>
            <h1 class="tw:font-semibold">{{ $pageTitle }}</h1>
            <p class="muted">Liste des citoyens inscrits à cette activité</p>
            <div class="actions tw:mt-3">
                <a class="button button--ghost" href="{{ route('portal.admin.civic_activities') }}">← Retour aux activités</a>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="panel tw:rounded-2xl tw:border tw:border-emerald-200/60 tw:bg-white/95 tw:p-6">
            <div class="tw:mb-6">
                <h2 class="tw:text-lg tw:font-semibold tw:mb-2">{{ $activity->title }}</h2>
                <p class="tw:text-sm tw:text-gray-600">
                    📅 {{ $activity->event_date?->format('d/m/Y') }}
                    @if($activity->event_start_time && $activity->event_end_time)
                        • ⏰ {{ $activity->event_start_time }} - {{ $activity->event_end_time }}
                    @endif
                    @if($activity->location)
                        • 📍 {{ $activity->location }}
                    @endif
                </p>
            </div>

            @if($registrations->count() > 0)
                <div class="tw:overflow-x-auto">
                    <table class="tw:w-full tw:text-sm">
                        <thead class="tw:bg-emerald-50 tw:border-b tw:border-emerald-200">
                            <tr>
                                <th class="tw:text-left tw:px-4 tw:py-3 tw:font-semibold">Nom</th>
                                <th class="tw:text-left tw:px-4 tw:py-3 tw:font-semibold">Email</th>
                                <th class="tw:text-left tw:px-4 tw:py-3 tw:font-semibold">Téléphone</th>
                                <th class="tw:text-left tw:px-4 tw:py-3 tw:font-semibold">Date d'inscription</th>
                                <th class="tw:text-center tw:px-4 tw:py-3 tw:font-semibold">Statut</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($registrations as $reg)
                                <tr class="tw:border-b tw:border-gray-200 tw:hover:tw:bg-gray-50">
                                    <td class="tw:px-4 tw:py-3 tw:font-medium">
                                        {{ $reg->first_name }} {{ $reg->last_name }}
                                    </td>
                                    <td class="tw:px-4 tw:py-3">
                                        <a href="mailto:{{ $reg->email }}" class="tw:text-emerald-600 tw:hover:tw:underline">
                                            {{ $reg->email }}
                                        </a>
                                    </td>
                                    <td class="tw:px-4 tw:py-3">
                                        @if($reg->phone)
                                            <a href="tel:{{ $reg->phone }}" class="tw:text-emerald-600 tw:hover:tw:underline">
                                                {{ $reg->phone }}
                                            </a>
                                        @else
                                            <span class="tw:text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="tw:px-4 tw:py-3">
                                        @if($reg->registered_at)
                                            {{ \Carbon\Carbon::parse($reg->registered_at)->format('d/m/Y H:i') }}
                                        @else
                                            <span class="tw:text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="tw:px-4 tw:py-3 tw:text-center">
                                        @if($reg->status === 'attended')
                                            <span class="tw:inline-block tw:px-3 tw:py-1 tw:bg-green-100 tw:text-green-800 tw:rounded-full tw:text-xs tw:font-medium">
                                                ✓ Participé
                                            </span>
                                        @elseif($reg->status === 'cancelled')
                                            <span class="tw:inline-block tw:px-3 tw:py-1 tw:bg-red-100 tw:text-red-800 tw:rounded-full tw:text-xs tw:font-medium">
                                                ✗ Annulé
                                            </span>
                                        @else
                                            <span class="tw:inline-block tw:px-3 tw:py-1 tw:bg-blue-100 tw:text-blue-800 tw:rounded-full tw:text-xs tw:font-medium">
                                                📋 Inscrit
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="tw:mt-6 tw:p-4 tw:bg-emerald-50 tw:rounded-lg tw:border tw:border-emerald-200">
                    <p class="tw:text-sm tw:text-emerald-900">
                        <strong>Total inscriptions:</strong> {{ $registrations->count() }}
                        @if($activity->max_participants)
                            / {{ $activity->max_participants }}
                        @endif
                    </p>
                </div>
            @else
                <div class="panel tw:rounded-2xl tw:border tw:border-amber-200/60 tw:bg-amber-50/50 tw:p-6">
                    <p class="muted tw:text-center">
                        <i class="bi bi-info-circle me-2"></i>
                        Aucune inscription pour cette activité.
                    </p>
                </div>
            @endif
        </div>
    </section>
@endsection
