@extends('portal.layout')

@section('content')
    @include('portal.components.admin-nav')

    <section class="hero">
        <div class="hero__panel">
            <span class="eyebrow">Administration - Citoyenneté</span>
            <h1 class="tw:font-semibold">Gestion des cours citoyens</h1>
            <p class="muted">Créez, modifiez et gérez les cours de formation en citoyenneté et patriotisme.</p>
        </div>

        <div class="panel">
            <div class="panel-header">
                <div class="panel-header__title">
                    <span class="eyebrow">Nouveau cours</span>
                    <h2 class="tw:font-semibold">Ajouter un cours</h2>
                </div>
            </div>
            <div class="tw:flex tw:justify-end">
                <a href="{{ route('portal.admin.civic_courses.create') }}" class="button button--primary tw:rounded-full tw:px-5 tw:py-2.5">
                    <i class="bi bi-plus-circle me-1"></i>Créer un cours
                </a>
            </div>
        </div>
    </section>

    <section class="section">
        @if ($courses->isEmpty())
            <div class="panel tw:text-center tw:rounded-2xl tw:border tw:border-emerald-200/60 tw:bg-white/90 tw:p-8">
                <div class="tw:text-4xl tw:mb-3">📚</div>
                <p class="muted tw:mb-4">Aucun cours créé pour le moment.</p>
                <a href="{{ route('portal.admin.civic_courses.create') }}" class="button button--primary tw:rounded-full tw:px-5 tw:py-2.5">
                    Créer le premier cours
                </a>
            </div>
        @else
            <div class="grid tw:gap-3">
                @foreach ($courses as $course)
                    <div class="panel tw:rounded-2xl tw:border tw:border-emerald-200/60 tw:bg-white/95 tw:p-5">
                        <div class="tw:flex tw:items-start tw:justify-between tw:gap-4">
                            <div class="tw:flex-1">
                                <div class="tw:flex tw:items-center tw:gap-3 tw:mb-2">
                                    <span class="tw:text-3xl">{{ $course->icon_emoji }}</span>
                                    <div>
                                        <h3 class="tw:font-bold tw:text-lg">{{ $course->title }}</h3>
                                        <p class="muted tw:text-xs">{{ $course->slug }}</p>
                                    </div>
                                </div>
                                <p class="tw:text-sm tw:text-gray-700 tw:mb-3">{{ Str::limit($course->description, 100) }}</p>
                                <div class="tw:flex tw:flex-wrap tw:gap-2 tw:items-center tw:text-xs">
                                    <span class="tw:px-2 tw:py-1 tw:rounded-full tw:bg-emerald-100 tw:text-emerald-800">
                                        ⏱ {{ $course->duration_minutes }} min
                                    </span>
                                    <span class="tw:px-2 tw:py-1 tw:rounded-full tw:bg-blue-100 tw:text-blue-800">
                                        📚 {{ ucfirst($course->course_type) }}
                                    </span>
                                    <span class="tw:px-2 tw:py-1 tw:rounded-full tw:bg-purple-100 tw:text-purple-800">
                                        {{ match($course->difficulty_level) {
                                            'beginner' => 'Débutant',
                                            'intermediate' => 'Intermédiaire',
                                            'advanced' => 'Avancé',
                                            default => ucfirst($course->difficulty_level),
                                        } }}
                                    </span>
                                    @if ($course->topics && count($course->topics) > 0)
                                        <span class="tw:px-2 tw:py-1 tw:rounded-full tw:bg-gray-100 tw:text-gray-800">
                                            {{ count($course->topics) }} thème{{ count($course->topics) > 1 ? 's' : '' }}
                                        </span>
                                    @endif
                                    <span class="tw:ml-auto tw:px-2 tw:py-1 tw:rounded-full {{ $course->is_active ? 'tw:bg-green-100 tw:text-green-800' : 'tw:bg-red-100 tw:text-red-800' }}">
                                        {{ $course->is_active ? '✓ Actif' : '✕ Inactif' }}
                                    </span>
                                </div>
                            </div>
                            <div class="tw:flex tw:gap-2">
                                <a href="{{ route('portal.admin.civic_courses.edit', $course) }}" class="button button--ghost tw:rounded-full tw:px-4 tw:py-2 tw:text-sm">
                                    <i class="bi bi-pencil me-1"></i>Modifier
                                </a>
                                <a href="{{ route('portal.admin.civic_courses.readers', $course) }}" class="button button--primary tw:rounded-full tw:px-4 tw:py-2 tw:text-sm">
                                    <i class="bi bi-eye me-1"></i>Lecteurs
                                </a>
                                <form action="{{ route('portal.admin.civic_courses.destroy', $course) }}" method="post" style="display:inline;">
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
