@extends('portal.layout')

@section('content')
    @include('portal.components.admin-nav')

    <section class="hero">
        <div class="hero__panel">
            <span class="eyebrow">Administration - Citoyenneté</span>
            <h1 class="tw:font-semibold">Gestion des activités communautaires</h1>
            <p class="muted">Créez, modifiez et gérez les activités et événements organisés par la mairie.</p>
        </div>

        <div class="panel">
            <div class="panel-header">
                <div class="panel-header__title">
                    <span class="eyebrow">Nouvelle activité</span>
                    <h2 class="tw:font-semibold">Ajouter une activité</h2>
                </div>
            </div>
            <div class="tw:flex tw:justify-end">
                <a href="{{ route('portal.admin.civic_activities.create') }}" class="button button--primary tw:rounded-full tw:px-5 tw:py-2.5">
                    <i class="bi bi-plus-circle me-1"></i>Créer une activité
                </a>
            </div>
        </div>
    </section>

    <section class="section">
        @if ($activities->isEmpty())
            <div class="panel tw:text-center tw:rounded-2xl tw:border tw:border-emerald-200/60 tw:bg-white/90 tw:p-8">
                <div class="tw:text-4xl tw:mb-3">🎯</div>
                <p class="muted tw:mb-4">Aucune activité créée pour le moment.</p>
                <a href="{{ route('portal.admin.civic_activities.create') }}" class="button button--primary tw:rounded-full tw:px-5 tw:py-2.5">
                    Créer la première activité
                </a>
            </div>
        @else
            <div class="grid tw:gap-3">
                @foreach ($activities as $activity)
                    <div class="panel tw:rounded-2xl tw:border tw:border-emerald-200/60 tw:bg-white/95 tw:p-5">
                        <div class="tw:flex tw:items-start tw:justify-between tw:gap-4">
                            <div class="tw:flex-1">
                                <div class="tw:flex tw:items-center tw:gap-3 tw:mb-2">
                                    <span class="tw:text-3xl">{{ $activity->icon_emoji }}</span>
                                    <div>
                                        <h3 class="tw:font-bold tw:text-lg">{{ $activity->title }}</h3>
                                        <p class="muted tw:text-xs">{{ $activity->slug }}</p>
                                    </div>
                                </div>
                                <p class="tw:text-sm tw:text-gray-700 tw:mb-3">{{ Str::limit($activity->description, 100) }}</p>
                                <div class="tw:flex tw:flex-wrap tw:gap-2 tw:items-center tw:text-xs">
                                    @if ($activity->event_date)
                                        <span class="tw:px-2 tw:py-1 tw:rounded-full tw:bg-amber-100 tw:text-amber-800">
                                            📅 {{ $activity->event_date->format('d/m/Y') }}
                                        </span>
                                    @endif
                                    <span class="tw:px-2 tw:py-1 tw:rounded-full tw:bg-blue-100 tw:text-blue-800">
                                        {{ $activity->getTypeLabel() }}
                                    </span>
                                    <span class="tw:px-2 tw:py-1 tw:rounded-full tw:bg-purple-100 tw:text-purple-800">
                                        {{ $activity->getFrequencyLabel() }}
                                    </span>
                                    <span class="tw:px-2 tw:py-1 tw:rounded-full 
                                        {{ match($activity->status) {
                                            'upcoming' => 'tw:bg-green-100 tw:text-green-800',
                                            'ongoing' => 'tw:bg-blue-100 tw:text-blue-800',
                                            'completed' => 'tw:bg-gray-100 tw:text-gray-800',
                                            'cancelled' => 'tw:bg-red-100 tw:text-red-800',
                                            default => 'tw:bg-gray-100 tw:text-gray-800',
                                        } }}">
                                        {{ $activity->getStatusLabel() }}
                                    </span>
                                    <span class="tw:ml-auto tw:px-2 tw:py-1 tw:rounded-full {{ $activity->is_active ? 'tw:bg-green-100 tw:text-green-800' : 'tw:bg-red-100 tw:text-red-800' }}">
                                        {{ $activity->is_active ? '✓ Actif' : '✕ Inactif' }}
                                    </span>
                                </div>
                                <p class="tw:text-xs tw:text-gray-600 tw:mt-2">📍 {{ $activity->location }}</p>
                            </div>
                            <div class="tw:flex tw:gap-2">
                                <a href="{{ route('portal.admin.civic_activities.edit', $activity) }}" class="button button--ghost tw:rounded-full tw:px-4 tw:py-2 tw:text-sm">
                                    <i class="bi bi-pencil me-1"></i>Modifier
                                </a>
                                <a href="{{ route('portal.admin.civic_activities.registrations', $activity) }}" class="button button--primary tw:rounded-full tw:px-4 tw:py-2 tw:text-sm">
                                    <i class="bi bi-people me-1"></i>Inscriptions
                                </a>
                                <form action="{{ route('portal.admin.civic_activities.destroy', $activity) }}" method="post" style="display:inline;">
                                    @csrf
                                    <button class="button button--danger tw:rounded-full tw:px-4 tw:py-2 tw:text-sm" type="submit" onclick="return confirm('Êtes-vous sûr?')">
                                        <i class="bi bi-trash me-1"></i>Supprimer
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </section>
@endsection
