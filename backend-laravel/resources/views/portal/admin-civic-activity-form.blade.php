@extends('portal.layout')

@section('content')
    @include('portal.components.admin-nav')

    <section class="hero">
        <div class="hero__panel">
            <span class="eyebrow">Administration - Citoyenneté</span>
            <h1 class="tw:font-semibold">{{ $pageTitle }}</h1>
            <p class="muted">Remplissez le formulaire pour {{ $activity ? 'modifier' : 'créer' }} une activité communautaire.</p>
            <div class="actions tw:mt-3">
                <a class="button button--ghost" href="{{ route('portal.admin.civic_activities') }}">← Retour à la liste</a>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="panel tw:rounded-2xl tw:border tw:border-emerald-200/60 tw:bg-white/95 tw:max-w-3xl tw:p-6">
            <form action="{{ $activity ? route('portal.admin.civic_activities.update', $activity) : route('portal.admin.civic_activities.store') }}" method="post" class="tw:space-y-4" enctype="multipart/form-data">
                @csrf

                <div class="grid tw:grid-cols-1 md:tw:grid-cols-2 tw:gap-4">
                    <div class="field">
                        <label for="icon_emoji">Icône (Emoji)</label>
                        <input id="icon_emoji" name="icon_emoji" type="text" value="{{ old('icon_emoji', $activity?->icon_emoji ?? '🎯') }}" required maxlength="10" class="tw:rounded-xl tw:border-emerald-200 tw:bg-white tw:px-3 tw:py-2.5 focus:tw:ring-2 focus:tw:ring-emerald-400/40" placeholder="🎯">
                        <small class="tw:text-xs tw:text-gray-600">Choisissez un emoji pour l'activité</small>
                        @error('icon_emoji')<small class="tw:text-xs tw:text-red-600">{{ $message }}</small>@enderror
                    </div>

                    <div class="field">
                        <label for="activity_type">Type d'activité *</label>
                        <select id="activity_type" name="activity_type" required class="tw:rounded-xl tw:border-emerald-200 tw:bg-white tw:px-3 tw:py-2.5 focus:tw:ring-2 focus:tw:ring-emerald-400/40">
                            <option value="">-- Sélectionner --</option>
                            <option value="community" @selected(old('activity_type', $activity?->activity_type) === 'community')>Communautaire</option>
                            <option value="workshop" @selected(old('activity_type', $activity?->activity_type) === 'workshop')>Atelier</option>
                            <option value="forum" @selected(old('activity_type', $activity?->activity_type) === 'forum')>Forum</option>
                            <option value="celebration" @selected(old('activity_type', $activity?->activity_type) === 'celebration')>Célébration</option>
                        </select>
                        @error('activity_type')<small class="tw:text-xs tw:text-red-600">{{ $message }}</small>@enderror
                    </div>
                </div>

                <div class="field">
                    <label for="title">Titre de l'activité *</label>
                    <input id="title" name="title" type="text" value="{{ old('title', $activity?->title) }}" required maxlength="255" class="tw:rounded-xl tw:border-emerald-200 tw:bg-white tw:px-3 tw:py-2.5 focus:tw:ring-2 focus:tw:ring-emerald-400/40">
                    @error('title')<small class="tw:text-xs tw:text-red-600">{{ $message }}</small>@enderror
                </div>

                <div class="field">
                    <label for="slug">Identifiant (slug) *</label>
                    <input id="slug" name="slug" type="text" value="{{ old('slug', $activity?->slug) }}" required maxlength="120" class="tw:rounded-xl tw:border-emerald-200 tw:bg-white tw:px-3 tw:py-2.5 focus:tw:ring-2 focus:tw:ring-emerald-400/40" placeholder="journee-jeunesse">
                    <small class="tw:text-xs tw:text-gray-600">Utiliser des tirets, pas d'espaces</small>
                    @error('slug')<small class="tw:text-xs tw:text-red-600">{{ $message }}</small>@enderror
                </div>

                <div class="field field--full">
                    <label for="description">Description *</label>
                    <textarea id="description" name="description" required maxlength="1000" rows="3" class="tw:rounded-xl tw:border-emerald-200 tw:bg-white tw:px-3 tw:py-2.5 focus:tw:ring-2 focus:tw:ring-emerald-400/40">{{ old('description', $activity?->description) }}</textarea>
                    <small class="tw:text-xs tw:text-gray-600">Brève description (max 1000 caractères)</small>
                    @error('description')<small class="tw:text-xs tw:text-red-600">{{ $message }}</small>@enderror
                </div>

                <div class="field field--full">
                    <label for="content">Contenu détaillé</label>
                    <textarea id="content" name="content" rows="4" class="tw:rounded-xl tw:border-emerald-200 tw:bg-white tw:px-3 tw:py-2.5 focus:tw:ring-2 focus:tw:ring-emerald-400/40">{{ old('content', $activity?->content) }}</textarea>
                    <small class="tw:text-xs tw:text-gray-600">Détails complets de l'activité</small>
                    @error('content')<small class="tw:text-xs tw:text-red-600">{{ $message }}</small>@enderror
                </div>

                <div class="grid tw:grid-cols-1 md:tw:grid-cols-3 tw:gap-4">
                    <div class="field">
                        <label for="event_date">Date de l'événement</label>
                        <input id="event_date" name="event_date" type="date" value="{{ old('event_date', $activity?->event_date?->format('Y-m-d')) }}" class="tw:rounded-xl tw:border-emerald-200 tw:bg-white tw:px-3 tw:py-2.5 focus:tw:ring-2 focus:tw:ring-emerald-400/40">
                        @error('event_date')<small class="tw:text-xs tw:text-red-600">{{ $message }}</small>@enderror
                    </div>

                    <div class="field">
                        <label for="event_start_time">Heure de début</label>
                        <input id="event_start_time" name="event_start_time" type="time" value="{{ old('event_start_time', $activity?->event_start_time?->format('H:i')) }}" class="tw:rounded-xl tw:border-emerald-200 tw:bg-white tw:px-3 tw:py-2.5 focus:tw:ring-2 focus:tw:ring-emerald-400/40">
                        @error('event_start_time')<small class="tw:text-xs tw:text-red-600">{{ $message }}</small>@enderror
                    </div>

                    <div class="field">
                        <label for="event_end_time">Heure de fin</label>
                        <input id="event_end_time" name="event_end_time" type="time" value="{{ old('event_end_time', $activity?->event_end_time?->format('H:i')) }}" class="tw:rounded-xl tw:border-emerald-200 tw:bg-white tw:px-3 tw:py-2.5 focus:tw:ring-2 focus:tw:ring-emerald-400/40">
                        @error('event_end_time')<small class="tw:text-xs tw:text-red-600">{{ $message }}</small>@enderror
                    </div>
                </div>

                <div class="field">
                    <label for="location">Lieu de l'événement *</label>
                    <input id="location" name="location" type="text" value="{{ old('location', $activity?->location) }}" required maxlength="500" class="tw:rounded-xl tw:border-emerald-200 tw:bg-white tw:px-3 tw:py-2.5 focus:tw:ring-2 focus:tw:ring-emerald-400/40" placeholder="Complexe sportif de Sédhiou">
                    @error('location')<small class="tw:text-xs tw:text-red-600">{{ $message }}</small>@enderror
                </div>

                <div class="field field--full">
                    <label for="location_details">Détails du lieu</label>
                    <textarea id="location_details" name="location_details" rows="2" class="tw:rounded-xl tw:border-emerald-200 tw:bg-white tw:px-3 tw:py-2.5 focus:tw:ring-2 focus:tw:ring-emerald-400/40">{{ old('location_details', $activity?->location_details) }}</textarea>
                    <small class="tw:text-xs tw:text-gray-600">Adresse, accès, parking, etc.</small>
                    @error('location_details')<small class="tw:text-xs tw:text-red-600">{{ $message }}</small>@enderror
                </div>

                <div class="field">
                    <label for="target_audience">Public cible *</label>
                    <input id="target_audience" name="target_audience" type="text" value="{{ old('target_audience', $activity?->target_audience) }}" required maxlength="255" class="tw:rounded-xl tw:border-emerald-200 tw:bg-white tw:px-3 tw:py-2.5 focus:tw:ring-2 focus:tw:ring-emerald-400/40" placeholder="Tous les jeunes de 13 à 35 ans">
                    <small class="tw:text-xs tw:text-gray-600">Qui peut participer?</small>
                    @error('target_audience')<small class="tw:text-xs tw:text-red-600">{{ $message }}</small>@enderror
                </div>

                <div class="grid tw:grid-cols-1 md:tw:grid-cols-3 tw:gap-4">
                    <div class="field">
                        <label for="frequency">Fréquence *</label>
                        <select id="frequency" name="frequency" required class="tw:rounded-xl tw:border-emerald-200 tw:bg-white tw:px-3 tw:py-2.5 focus:tw:ring-2 focus:tw:ring-emerald-400/40">
                            <option value="">-- Sélectionner --</option>
                            <option value="once" @selected(old('frequency', $activity?->frequency) === 'once')>Unique</option>
                            <option value="weekly" @selected(old('frequency', $activity?->frequency) === 'weekly')>Hebdomadaire</option>
                            <option value="monthly" @selected(old('frequency', $activity?->frequency) === 'monthly')>Mensuel</option>
                            <option value="quarterly" @selected(old('frequency', $activity?->frequency) === 'quarterly')>Trimestriel</option>
                        </select>
                        @error('frequency')<small class="tw:text-xs tw:text-red-600">{{ $message }}</small>@enderror
                    </div>

                    <div class="field">
                        <label for="status">Statut *</label>
                        <select id="status" name="status" required class="tw:rounded-xl tw:border-emerald-200 tw:bg-white tw:px-3 tw:py-2.5 focus:tw:ring-2 focus:tw:ring-emerald-400/40">
                            <option value="">-- Sélectionner --</option>
                            <option value="upcoming" @selected(old('status', $activity?->status) === 'upcoming')>À venir</option>
                            <option value="ongoing" @selected(old('status', $activity?->status) === 'ongoing')>En cours</option>
                            <option value="completed" @selected(old('status', $activity?->status) === 'completed')>Complétée</option>
                            <option value="cancelled" @selected(old('status', $activity?->status) === 'cancelled')>Annulée</option>
                        </select>
                        @error('status')<small class="tw:text-xs tw:text-red-600">{{ $message }}</small>@enderror
                    </div>

                    <div class="field">
                        <label for="max_participants">Nombre max de participants</label>
                        <input id="max_participants" name="max_participants" type="number" value="{{ old('max_participants', $activity?->max_participants) }}" min="1" class="tw:rounded-xl tw:border-emerald-200 tw:bg-white tw:px-3 tw:py-2.5 focus:tw:ring-2 focus:tw:ring-emerald-400/40" placeholder="Illimité">
                        @error('max_participants')<small class="tw:text-xs tw:text-red-600">{{ $message }}</small>@enderror
                    </div>
                </div>

                <div class="grid tw:grid-cols-1 md:tw:grid-cols-2 tw:gap-4">
                    <div class="field">
                        <label for="sort_order">Ordre d'affichage</label>
                        <input id="sort_order" name="sort_order" type="number" value="{{ old('sort_order', $activity?->sort_order ?? 0) }}" min="0" class="tw:rounded-xl tw:border-emerald-200 tw:bg-white tw:px-3 tw:py-2.5 focus:tw:ring-2 focus:tw:ring-emerald-400/40">
                        <small class="tw:text-xs tw:text-gray-600">Plus bas = affiché en premier</small>
                        @error('sort_order')<small class="tw:text-xs tw:text-red-600">{{ $message }}</small>@enderror
                    </div>

                    <div class="field">
                        <input type="hidden" name="is_active" value="0">
                        <label>
                            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $activity?->is_active ?? true))>
                            <span class="tw:ml-2">Actif (visible pour les citoyens)</span>
                        </label>
                    </div>
                </div>

                <!-- Fichiers joints -->
                <hr class="tw:border-emerald-200/40 tw:my-6">
                
                <h3 class="tw:text-lg tw:font-semibold tw:text-gray-900 tw:mb-4">📎 Fichiers joints</h3>

                <div class="grid tw:grid-cols-1 md:tw:grid-cols-2 tw:gap-4">
                    <div class="field">
                        <label for="image">Image de l'activité</label>
                        <input id="image" name="image" type="file" accept="image/*" class="tw:rounded-xl tw:border-emerald-200 tw:bg-white tw:px-3 tw:py-2.5 focus:tw:ring-2 focus:tw:ring-emerald-400/40">
                        <small class="tw:text-xs tw:text-gray-600">JPG, PNG, GIF (max 5 MB)</small>
                        @if($activity?->image_name)
                            <div class="tw:mt-2 tw:text-sm tw:text-emerald-700">
                                <i class="bi bi-check-circle me-1"></i>Image actuelle: <strong>{{ $activity->image_name }}</strong>
                            </div>
                        @endif
                        @error('image')<small class="tw:text-xs tw:text-red-600">{{ $message }}</small>@enderror
                    </div>

                    <div class="field">
                        <label for="document">Document (PDF, Word, Excel, etc.)</label>
                        <input id="document" name="document" type="file" accept=".pdf,.doc,.docx,.xls,.xlsx,.txt,.ppt,.pptx" class="tw:rounded-xl tw:border-emerald-200 tw:bg-white tw:px-3 tw:py-2.5 focus:tw:ring-2 focus:tw:ring-emerald-400/40">
                        <small class="tw:text-xs tw:text-gray-600">PDF, Word, Excel, PowerPoint (max 10 MB)</small>
                        @if($activity?->document_name)
                            <div class="tw:mt-2 tw:text-sm tw:text-emerald-700">
                                <i class="bi bi-check-circle me-1"></i>Document actuel: <strong>{{ $activity->document_name }}</strong>
                            </div>
                        @endif
                        @error('document')<small class="tw:text-xs tw:text-red-600">{{ $message }}</small>@enderror
                    </div>
                </div>

                <div class="tw:flex tw:gap-2 tw:mt-6">
                    <button class="button button--primary tw:rounded-full tw:px-5 tw:py-2.5" type="submit">
                        <i class="bi bi-check-circle me-1"></i>{{ $activity ? 'Enregistrer les modifications' : 'Créer l\'activité' }}
                    </button>
                    <a href="{{ route('portal.admin.civic_activities') }}" class="button button--ghost tw:rounded-full tw:px-5 tw:py-2.5">Annuler</a>
                </div>
            </form>
        </div>
    </section>
@endsection
