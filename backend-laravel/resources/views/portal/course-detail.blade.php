@extends('portal.layout')

@section('content')
    <div class="tw:bg-gradient-to-b tw:from-emerald-50 tw:to-transparent">
        <!-- Hero Section -->
        <section class="hero">
            <div class="hero__panel">
                <div class="tw:flex tw:items-center tw:gap-3">
                    <span class="tw:text-5xl">{{ $course->icon_emoji ?? '📚' }}</span>
                    <div>
                        <span class="eyebrow">Cours Citoyen</span>
                        <h1 class="tw:font-semibold">{{ $course->title }}</h1>
                    </div>
                </div>
                <p class="muted tw:mt-3">{{ $course->description }}</p>
                <div class="actions tw:mt-4">
                    <a class="button button--ghost" href="{{ route('portal.citizen') }}">← Retour aux cours</a>
                </div>
            </div>
        </section>
    </div>

    <!-- Course Content -->
    <section class="section">
        <div class="tw:grid tw:grid-cols-1 lg:tw:grid-cols-3 tw:gap-6">
            <!-- Main Content -->
            <div class="lg:tw:col-span-2">
                <!-- Course Details Meta -->
                <div class="panel tw:rounded-2xl tw:border tw:border-emerald-200/60 tw:bg-white/95 tw:p-6 tw:mb-6">
                    <div class="tw:grid tw:grid-cols-2 md:tw:grid-cols-4 tw:gap-4">
                        <div class="tw:text-center">
                            <p class="tw:text-sm tw:text-gray-600 tw:mb-1">Durée</p>
                            <p class="tw:font-semibold tw:text-emerald-700">
                                <i class="bi bi-clock me-1"></i>{{ $course->duration_minutes }} min
                            </p>
                        </div>
                        <div class="tw:text-center">
                            <p class="tw:text-sm tw:text-gray-600 tw:mb-1">Type</p>
                            <p class="tw:font-semibold tw:text-emerald-700">
                                @if($course->course_type === 'online')
                                    <i class="bi bi-wifi me-1"></i>En ligne
                                @elseif($course->course_type === 'hybrid')
                                    <i class="bi bi-share me-1"></i>Hybride
                                @else
                                    <i class="bi bi-door-open me-1"></i>Présentiel
                                @endif
                            </p>
                        </div>
                        <div class="tw:text-center">
                            <p class="tw:text-sm tw:text-gray-600 tw:mb-1">Niveau</p>
                            <p class="tw:font-semibold tw:text-emerald-700">
                                @if($course->difficulty_level === 'beginner')
                                    <i class="bi bi-cup-hot me-1"></i>Débutant
                                @elseif($course->difficulty_level === 'intermediate')
                                    <i class="bi bi-fire me-1"></i>Intermédj
                                @else
                                    <i class="bi bi-lightning-fill me-1"></i>Avancé
                                @endif
                            </p>
                        </div>
                        <div class="tw:text-center">
                            <p class="tw:text-sm tw:text-gray-600 tw:mb-1">Statut</p>
                            <p class="tw:inline-block tw:px-3 tw:py-1 tw:bg-emerald-100 tw:text-emerald-800 tw:rounded-full tw:text-sm tw:font-medium">
                                <i class="bi bi-check-circle me-1"></i>Active
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Course Content -->
                @if($course->content)
                    <div class="panel tw:rounded-2xl tw:border tw:border-emerald-200/60 tw:bg-white/95 tw:p-6 tw:mb-6">
                        <h2 class="tw:text-xl tw:font-semibold tw:text-gray-900 tw:mb-4">Contenu du cours</h2>
                        <div class="prose prose-sm tw:max-w-none">
                            {!! nl2br(e($course->content)) !!}
                        </div>
                    </div>
                @endif

                <!-- Topics -->
                @if($course->topicsArray())
                    <div class="panel tw:rounded-2xl tw:border tw:border-emerald-200/60 tw:bg-white/95 tw:p-6">
                        <h2 class="tw:text-xl tw:font-semibold tw:text-gray-900 tw:mb-4">
                            <i class="bi bi-list-check me-2 tw:text-emerald-600"></i>Thèmes abordés
                        </h2>
                        <ul class="tw:space-y-2">
                            @foreach($course->topicsArray() as $topic)
                                <li class="tw:flex tw:items-start tw:gap-3">
                                    <i class="bi bi-check2 tw:text-emerald-600 tw:mt-1 tw:flex-shrink-0"></i>
                                    <span>{{ $topic }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Fichiers joints -->
                @if($course->image_path || $course->document_path)
                    <div class="panel tw:rounded-2xl tw:border tw:border-emerald-200/60 tw:bg-white/95 tw:p-6">
                        <h2 class="tw:text-xl tw:font-semibold tw:text-gray-900 tw:mb-4">
                            <i class="bi bi-paperclip me-2 tw:text-emerald-600"></i>Fichiers du cours
                        </h2>
                        <div class="tw:space-y-2">
                            @if($course->image_path)
                                <div class="tw:flex tw:items-center tw:gap-3 tw:p-3 tw:bg-emerald-50/50 tw:rounded-lg tw:border tw:border-emerald-200/40">
                                    <i class="bi bi-image tw:text-emerald-600 tw:text-lg"></i>
                                    <div class="tw:flex-1">
                                        <p class="tw:text-sm tw:text-gray-700 tw:font-medium">Image</p>
                                        <p class="tw:text-xs tw:text-gray-600">{{ $course->image_name ?? 'image.jpg' }}</p>
                                    </div>
                                    <a href="{{ asset('storage/' . $course->image_path) }}" target="_blank" class="button button--ghost tw:px-3 tw:py-2" title="Voir l'image">
                                        <i class="bi bi-download"></i>
                                    </a>
                                </div>
                            @endif
                            @if($course->document_path)
                                <div class="tw:flex tw:items-center tw:gap-3 tw:p-3 tw:bg-emerald-50/50 tw:rounded-lg tw:border tw:border-emerald-200/40">
                                    <i class="bi bi-file-earmark tw:text-emerald-600 tw:text-lg"></i>
                                    <div class="tw:flex-1">
                                        <p class="tw:text-sm tw:text-gray-700 tw:font-medium">Document</p>
                                        <p class="tw:text-xs tw:text-gray-600">{{ $course->document_name ?? 'document.pdf' }}</p>
                                    </div>
                                    <a href="{{ asset('storage/' . $course->document_path) }}" target="_blank" download class="button button--ghost tw:px-3 tw:py-2" title="Télécharger">
                                        <i class="bi bi-download"></i>
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="tw:space-y-6">
                <!-- Action Card -->
                <div class="panel tw:rounded-2xl tw:border-2 tw:border-emerald-400 tw:bg-emerald-50/50 tw:p-6 tw:sticky tw:top-4">
                    <div class="tw:text-center tw:space-y-3">
                        <p class="tw:text-sm tw:text-gray-700">
                            <i class="bi bi-info-circle me-1"></i>
                            Ce cours est disponible pour votre apprentissage
                        </p>
                        <div class="tw:space-y-2">
                            <a href="{{ route('portal.citizen.course.view', ['civicCourse' => $course->id]) }}" class="button button--primary tw:w-full">
                                <i class="bi bi-book-half me-2"></i>Commencer le cours
                            </a>
                            <a href="{{ route('portal.citizen') }}" class="button button--ghost tw:w-full">
                                <i class="bi bi-arrow-left me-2"></i>Retour
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Course Info Card -->
                <div class="panel tw:rounded-2xl tw:border tw:border-gray-200 tw:bg-white tw:p-6">
                    <h3 class="tw:font-semibold tw:text-gray-900 tw:mb-3">
                        <i class="bi bi-info-lg me-2 tw:text-emerald-600"></i>Information
                    </h3>
                    <div class="tw:space-y-3 tw:text-sm">
                        <div>
                            <p class="tw:text-xs tw:text-gray-600 tw:uppercase tw:tracking-wide">Créé par</p>
                            <p class="tw:text-gray-900">{{ $course->createdBy?->name ?? 'Admin' }}</p>
                        </div>
                        <div>
                            <p class="tw:text-xs tw:text-gray-600 tw:uppercase tw:tracking-wide">Date de création</p>
                            <p class="tw:text-gray-900">{{ $course->created_at->format('d M Y') }}</p>
                        </div>
                        <div>
                            <p class="tw:text-xs tw:text-gray-600 tw:uppercase tw:tracking-wide">Dernière mise à jour</p>
                            <p class="tw:text-gray-900">{{ $course->updated_at->format('d M Y à H:i') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Contact/Support Card -->
                <div class="panel tw:rounded-2xl tw:border tw:border-amber-200/60 tw:bg-amber-50 tw:p-6">
                    <h3 class="tw:font-semibold tw:text-gray-900 tw:mb-2">
                        <i class="bi bi-question-circle me-2 tw:text-amber-600"></i>Aide
                    </h3>
                    <p class="tw:text-sm tw:text-gray-700 tw:mb-3">
                        Vous avez des questions sur ce cours?
                    </p>
                    <a href="mailto:contact@example.com" class="tw:text-amber-700 tw:font-medium tw:text-sm tw:hover:tw:underline">
                        <i class="bi bi-envelope me-1"></i>Nous contacter
                    </a>
                </div>
            </div>
        </div>
    </section>

    <style>
        .prose {
            color: #374151;
        }
        .prose strong {
            color: #111827;
            font-weight: 600;
        }
        .prose em {
            font-style: italic;
        }
    </style>
@endsection
