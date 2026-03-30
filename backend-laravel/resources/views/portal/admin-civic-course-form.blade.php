@extends('portal.layout')

@section('content')
    @include('portal.components.admin-nav')

    <section class="hero">
        <div class="hero__panel">
            <span class="eyebrow">Administration - Citoyenneté</span>
            <h1 class="tw:font-semibold">{{ $pageTitle }}</h1>
            <p class="muted">Remplissez le formulaire pour {{ $course ? 'modifier' : 'créer' }} un cours citoyen.</p>
            <div class="actions tw:mt-3">
                <a class="button button--ghost" href="{{ route('portal.admin.civic_courses') }}">← Retour à la liste</a>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="panel tw:rounded-2xl tw:border tw:border-emerald-200/60 tw:bg-white/95 tw:max-w-3xl tw:p-6">
            <form action="{{ $course ? route('portal.admin.civic_courses.update', $course) : route('portal.admin.civic_courses.store') }}" method="post" class="tw:space-y-4" enctype="multipart/form-data">
                @csrf

                <div class="grid tw:grid-cols-1 md:tw:grid-cols-2 tw:gap-4">
                    <div class="field">
                        <label for="icon_emoji">Icône (Emoji)</label>
                        <input id="icon_emoji" name="icon_emoji" type="text" value="{{ old('icon_emoji', $course?->icon_emoji ?? '📚') }}" required maxlength="10" class="tw:rounded-xl tw:border-emerald-200 tw:bg-white tw:px-3 tw:py-2.5 focus:tw:ring-2 focus:tw:ring-emerald-400/40" placeholder="📚">
                        <small class="tw:text-xs tw:text-gray-600">Choisissez un emoji pour représenter le cours</small>
                        @error('icon_emoji')<small class="tw:text-xs tw:text-red-600">{{ $message }}</small>@enderror
                    </div>

                    <div class="field">
                        <label for="duration_minutes">Durée (minutes)</label>
                        <input id="duration_minutes" name="duration_minutes" type="number" value="{{ old('duration_minutes', $course?->duration_minutes ?? '30') }}" required min="1" class="tw:rounded-xl tw:border-emerald-200 tw:bg-white tw:px-3 tw:py-2.5 focus:tw:ring-2 focus:tw:ring-emerald-400/40">
                        @error('duration_minutes')<small class="tw:text-xs tw:text-red-600">{{ $message }}</small>@enderror
                    </div>
                </div>

                <div class="field">
                    <label for="title">Titre du cours *</label>
                    <input id="title" name="title" type="text" value="{{ old('title', $course?->title) }}" required maxlength="255" class="tw:rounded-xl tw:border-emerald-200 tw:bg-white tw:px-3 tw:py-2.5 focus:tw:ring-2 focus:tw:ring-emerald-400/40">
                    @error('title')<small class="tw:text-xs tw:text-red-600">{{ $message }}</small>@enderror
                </div>

                <div class="field">
                    <label for="slug">Identifiant (slug) *</label>
                    <input id="slug" name="slug" type="text" value="{{ old('slug', $course?->slug) }}" required maxlength="120" class="tw:rounded-xl tw:border-emerald-200 tw:bg-white tw:px-3 tw:py-2.5 focus:tw:ring-2 focus:tw:ring-emerald-400/40" placeholder="engagement-citoyen">
                    <small class="tw:text-xs tw:text-gray-600">Utiliser des tirets, pas d'espaces (ex: ma-formation)</small>
                    @error('slug')<small class="tw:text-xs tw:text-red-600">{{ $message }}</small>@enderror
                </div>

                <div class="field field--full">
                    <label for="description">Description *</label>
                    <textarea id="description" name="description" required maxlength="1000" rows="3" class="tw:rounded-xl tw:border-emerald-200 tw:bg-white tw:px-3 tw:py-2.5 focus:tw:ring-2 focus:tw:ring-emerald-400/40">{{ old('description', $course?->description) }}</textarea>
                    <small class="tw:text-xs tw:text-gray-600">Brève description du cours (max 1000 caractères)</small>
                    @error('description')<small class="tw:text-xs tw:text-red-600">{{ $message }}</small>@enderror
                </div>

                <div class="field field--full">
                    <label for="content">Contenu détaillé du cours</label>
                    <textarea id="content" name="content" rows="6" class="tw:rounded-xl tw:border-emerald-200 tw:bg-white tw:px-3 tw:py-2.5 focus:tw:ring-2 focus:tw:ring-emerald-400/40">{{ old('content', $course?->content) }}</textarea>
                    <small class="tw:text-xs tw:text-gray-600">Contenu complet du cours (facultatif)</small>
                    @error('content')<small class="tw:text-xs tw:text-red-600">{{ $message }}</small>@enderror
                </div>

                <div class="field field--full">
                    <label for="topics">Thèmes abordés</label>
                    <textarea id="topics" name="topics" rows="4" class="tw:rounded-xl tw:border-emerald-200 tw:bg-white tw:px-3 tw:py-2.5 focus:tw:ring-2 focus:tw:ring-emerald-400/40" placeholder="Un thème par ligne&#10;Exemple:&#10;Les droits du citoyen&#10;Les devoirs du citoyen&#10;La participation démocratique">{{ old('topics', $course ? implode("\n", $course->topicsArray()) : '') }}</textarea>
                    <small class="tw:text-xs tw:text-gray-600">Un thème par ligne (facultatif)</small>
                    @error('topics')<small class="tw:text-xs tw:text-red-600">{{ $message }}</small>@enderror
                </div>

                <div class="grid tw:grid-cols-1 md:tw:grid-cols-2 tw:gap-4">
                    <div class="field">
                        <label for="course_type">Type de cours *</label>
                        <select id="course_type" name="course_type" required class="tw:rounded-xl tw:border-emerald-200 tw:bg-white tw:px-3 tw:py-2.5 focus:tw:ring-2 focus:tw:ring-emerald-400/40">
                            <option value="">-- Sélectionner --</option>
                            <option value="online" @selected(old('course_type', $course?->course_type) === 'online')>En ligne</option>
                            <option value="hybrid" @selected(old('course_type', $course?->course_type) === 'hybrid')>Hybride</option>
                            <option value="offline" @selected(old('course_type', $course?->course_type) === 'offline')>En présentiel</option>
                        </select>
                        @error('course_type')<small class="tw:text-xs tw:text-red-600">{{ $message }}</small>@enderror
                    </div>

                    <div class="field">
                        <label for="difficulty_level">Niveau de difficulté *</label>
                        <select id="difficulty_level" name="difficulty_level" required class="tw:rounded-xl tw:border-emerald-200 tw:bg-white tw:px-3 tw:py-2.5 focus:tw:ring-2 focus:tw:ring-emerald-400/40">
                            <option value="">-- Sélectionner --</option>
                            <option value="beginner" @selected(old('difficulty_level', $course?->difficulty_level) === 'beginner')>Débutant</option>
                            <option value="intermediate" @selected(old('difficulty_level', $course?->difficulty_level) === 'intermediate')>Intermédiaire</option>
                            <option value="advanced" @selected(old('difficulty_level', $course?->difficulty_level) === 'advanced')>Avancé</option>
                        </select>
                        @error('difficulty_level')<small class="tw:text-xs tw:text-red-600">{{ $message }}</small>@enderror
                    </div>
                </div>

                <div class="grid tw:grid-cols-1 md:tw:grid-cols-2 tw:gap-4">
                    <div class="field">
                        <label for="sort_order">Ordre d'affichage</label>
                        <input id="sort_order" name="sort_order" type="number" value="{{ old('sort_order', $course?->sort_order ?? 0) }}" min="0" class="tw:rounded-xl tw:border-emerald-200 tw:bg-white tw:px-3 tw:py-2.5 focus:tw:ring-2 focus:tw:ring-emerald-400/40">
                        <small class="tw:text-xs tw:text-gray-600">Plus bas = affiché en premier</small>
                        @error('sort_order')<small class="tw:text-xs tw:text-red-600">{{ $message }}</small>@enderror
                    </div>

                    <div class="field">
                        <label>
                            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $course?->is_active ?? true))>
                            <span class="tw:ml-2">Actif (visible pour les citoyens)</span>
                        </label>
                    </div>
                </div>

                <!-- Fichiers joints -->
                <hr class="tw:border-emerald-200/40 tw:my-6">
                
                <h3 class="tw:text-lg tw:font-semibold tw:text-gray-900 tw:mb-4">📎 Fichiers joints</h3>

                <div class="grid tw:grid-cols-1 md:tw:grid-cols-2 tw:gap-4">
                    <div class="field">
                        <label for="image">Image du cours</label>
                        <input id="image" name="image" type="file" accept="image/*" class="tw:rounded-xl tw:border-emerald-200 tw:bg-white tw:px-3 tw:py-2.5 focus:tw:ring-2 focus:tw:ring-emerald-400/40">
                        <small class="tw:text-xs tw:text-gray-600">JPG, PNG, GIF (max 5 MB)</small>
                        @if($course?->image_name)
                            <div class="tw:mt-2 tw:text-sm tw:text-emerald-700">
                                <i class="bi bi-check-circle me-1"></i>Image actuelle: <strong>{{ $course->image_name }}</strong>
                            </div>
                        @endif
                        @error('image')<small class="tw:text-xs tw:text-red-600">{{ $message }}</small>@enderror
                    </div>

                    <div class="field">
                        <label for="document">Document (PDF, Word, Excel, etc.)</label>
                        <input id="document" name="document" type="file" accept=".pdf,.doc,.docx,.xls,.xlsx,.txt,.ppt,.pptx" class="tw:rounded-xl tw:border-emerald-200 tw:bg-white tw:px-3 tw:py-2.5 focus:tw:ring-2 focus:tw:ring-emerald-400/40">
                        <small class="tw:text-xs tw:text-gray-600">PDF, Word, Excel, PowerPoint (max 10 MB)</small>
                        @if($course?->document_name)
                            <div class="tw:mt-2 tw:text-sm tw:text-emerald-700">
                                <i class="bi bi-check-circle me-1"></i>Document actuel: <strong>{{ $course->document_name }}</strong>
                            </div>
                        @endif
                        @error('document')<small class="tw:text-xs tw:text-red-600">{{ $message }}</small>@enderror
                    </div>
                </div>

                <div class="tw:flex tw:gap-2 tw:mt-6">
                    <button class="button button--primary tw:rounded-full tw:px-5 tw:py-2.5" type="submit">
                        <i class="bi bi-check-circle me-1"></i>{{ $course ? 'Enregistrer les modifications' : 'Créer le cours' }}
                    </button>
                    <a href="{{ route('portal.admin.civic_courses') }}" class="button button--ghost tw:rounded-full tw:px-5 tw:py-2.5">Annuler</a>
                </div>
            </form>
        </div>
    </section>
@endsection
