@extends('portal.layout')

@section('content')
@php
    $statusClassMap = [
        'pending'    => 'pending',
        'assigned'   => 'assigned',
        'processing' => 'processing',
        'completed'  => 'completed',
        'rejected'   => 'rejected',
    ];

    if ($role === 'citoyen') {
        $roleTitle = 'Espace citoyen';
        $roleLead = '';
        $dashboardHighlights = [
            ['label' => 'Dossiers', 'value' => $stats['total'], 'meta' => 'Total de demandes deposees'],
            ['label' => 'Messages recus', 'value' => $receivedMessages->count(), 'meta' => 'Echanges en attente de lecture'],
            ['label' => 'Paiements', 'value' => $paymentStats['unpaid'], 'meta' => 'Dossiers restant a regler'],
        ];
    } elseif ($role === 'agent') {
        $roleTitle = 'Espace agent';
        $roleLead = '';
        $dashboardHighlights = [
            ['label' => 'A traiter', 'value' => $pendingDemandes->count(), 'meta' => 'Dossiers encore sans prise en charge'],
            ['label' => 'Paiements', 'value' => $paymentValidationStats['pending'], 'meta' => 'Validations en attente'],
            ['label' => 'Performance', 'value' => $agentPerformance['completion_rate'] . '%', 'meta' => 'Taux global de traitement'],
        ];
    } else {
        $roleTitle = 'Administration';
        $roleLead = '';
        $dashboardHighlights = [
            ['label' => 'Utilisateurs', 'value' => $userCounts['total'], 'meta' => 'Comptes actifs sur le portail'],
            ['label' => 'Demandes', 'value' => $demandeCounts['total'], 'meta' => 'Volume global de dossiers'],
            ['label' => 'Messages recus', 'value' => $adminReceivedMessages->count(), 'meta' => 'Conversations a suivre'],
        ];
    }
@endphp

{{-- ════════════════════════════ WELCOME BAR ════════════════════════════ --}}
<section class="dashboard-hero panel tw:rounded-3xl tw:border tw:border-emerald-800 tw:bg-emerald-700 tw:text-white tw:shadow-xl tw:p-5 lg:tw:p-7">
    <div class="welcome-bar welcome-bar--hero tw:flex tw:flex-col md:tw:flex-row tw:justify-between tw:gap-4">
        <div>
            <div class="welcome-bar__date tw:text-emerald-100">{{ now()->translatedFormat('l d F Y') }}</div>
            <h1 class="welcome-bar__name tw:text-sm tw:font-medium tw:text-white">Bonjour, {{ $currentUser->first_name ?: ($currentUser->name ?: $currentUser->email) }}</h1>
            <div class="welcome-bar__meta tw:flex tw:flex-wrap tw:items-center tw:gap-2">
                <span class="role-badge role-badge--{{ $role }}">{{ $roleTitle }}</span>
                <span class="muted tw:text-xs md:tw:text-sm tw:text-emerald-100">{{ $currentUser->email }}</span>
                <span class="pill tw:rounded-full tw:border tw:border-amber-300 tw:bg-amber-100 tw:px-3 tw:py-1 tw:font-semibold">Tableau de bord securise</span>
            </div>
        </div>
        <div class="welcome-bar__actions tw:flex tw:flex-wrap tw:items-center tw:gap-2">
            <a class="button button--ghost tw:inline-flex tw:items-center tw:justify-center tw:px-4 tw:py-2.5 tw:rounded-full" href="{{ $wordpressUrl }}/">Retour site mairie</a>
            <form action="{{ route('portal.logout') }}" method="post">
                @csrf
                <button class="button button--danger tw:inline-flex tw:items-center tw:justify-center tw:px-4 tw:py-2.5 tw:rounded-full" type="submit">Déconnexion</button>
            </form>
        </div>
    </div>
    <div class="dashboard-hero__foot">
        {{-- Texte d’intro supprimé --}}
        <div class="dashboard-summary tw:grid tw:grid-cols-1 md:tw:grid-cols-3 tw:gap-3 tw:mt-4">
            @foreach ($dashboardHighlights as $highlight)
                <article class="dashboard-summary__card tw:rounded-lg tw:border tw:border-emerald-600 tw:bg-emerald-50 tw:shadow-sm tw:p-4">
                    <span class="dashboard-summary__label tw:text-xs tw:text-emerald-800 tw:font-semibold">{{ $highlight['label'] }}</span>
                    <strong class="tw:text-xl md:tw:text-2xl tw:font-bold tw:text-emerald-900">{{ $highlight['value'] }}</strong>
                    {{-- Meta supprimée --}}
                </article>
            @endforeach
        </div>
    </div>
</section>


<section class="dashboard-stage tw:mt-5 tw:rounded-3xl tw:border tw:border-emerald-200/60 tw:bg-gray-50 tw:p-4 lg:tw:p-6">

{{-- ══════════════════════════════ CITOYEN ══════════════════════════════ --}}
@if ($role === 'citoyen')

    {{-- Bouton de recensement citoyen --}}
    <div class="tw:mb-4 tw:flex tw:justify-end">
        <a href="{{ route('portal.citoyen.recensement') }}" class="button button--success tw:rounded-full tw:px-5 tw:py-2 tw:bg-success tw:text-white tw:font-semibold hover:tw:bg-success" title="Enregistrez-vous ou aidez un citoyen à s'enregistrer auprès de la mairie">
            📝 Me faire connaître de la mairie
        </a>
    </div>

<nav class="tab-nav tw:flex tw:flex-wrap tw:gap-2 tw:mb-5 tw:bg-gray-50">
    <button class="tab-btn tw:rounded-full tw:px-4 tw:py-2 tw:text-sm tw:font-medium" data-tab="accueil">🏠 Accueil</button>
    <button class="tab-btn tw:rounded-full tw:px-4 tw:py-2 tw:text-sm tw:font-medium" data-tab="dossiers">
        📁 Mes dossiers
        @if ($stats['total'] > 0)<span class="tab-badge">{{ $stats['total'] }}</span>@endif
    </button>
    <button class="tab-btn tw:rounded-full tw:px-4 tw:py-2 tw:text-sm tw:font-medium" data-tab="nouvelle-demande">✚ Nouvelle demande</button>
    <button class="tab-btn tw:rounded-full tw:px-4 tw:py-2 tw:text-sm tw:font-medium" data-tab="citoyennete">🏛️ Citoyenneté</button>
    <button class="tab-btn tw:rounded-full tw:px-4 tw:py-2 tw:text-sm tw:font-medium" data-tab="messages">
        ✉ Messages
        @if ($receivedMessages->count() > 0)<span class="tab-badge">{{ $receivedMessages->count() }}</span>@endif
    </button>
    <button class="tab-btn tw:rounded-full tw:px-4 tw:py-2 tw:text-sm tw:font-medium" data-tab="profil">👤 Mon profil</button>
</nav>

{{-- ── Accueil citoyen ─────────────────── --}}
<div class="tab-pane" data-pane="accueil">
    <div class="kpi-row tw:grid tw:grid-cols-2 lg:tw:grid-cols-3 tw:gap-3">
        <div class="kpi kpi--brand tw:bg-white tw:shadow-sm tw:rounded-lg">
            <span class="kpi__icon">📁</span>
            <span class="kpi__value tw:text-xl md:tw:text-2xl tw:font-bold">{{ $stats['total'] }}</span>
            <span class="kpi__label tw:text-xs tw:text-gray-500 tw:font-medium">Dossiers déposés</span>
        </div>
        <div class="kpi kpi--warn tw:bg-white tw:shadow-sm tw:rounded-lg">
            <span class="kpi__icon">⏳</span>
            <span class="kpi__value tw:text-xl md:tw:text-2xl tw:font-bold">{{ $stats['pending'] }}</span>
            <span class="kpi__label tw:text-xs tw:text-gray-500 tw:font-medium">En attente</span>
        </div>
        <div class="kpi kpi--accent tw:bg-white tw:shadow-sm tw:rounded-lg">
            <span class="kpi__icon">⚡</span>
            <span class="kpi__value tw:text-xl md:tw:text-2xl tw:font-bold">{{ $stats['processing'] }}</span>
            <span class="kpi__label tw:text-xs tw:text-gray-500 tw:font-medium">En traitement</span>
        </div>
        <div class="kpi kpi--success tw:bg-white tw:shadow-sm tw:rounded-lg">
            <span class="kpi__icon">✅</span>
            <span class="kpi__value tw:text-xl md:tw:text-2xl tw:font-bold">{{ $stats['completed'] }}</span>
            <span class="kpi__label tw:text-xs tw:text-gray-500 tw:font-medium">Finalisés</span>
        </div>
        <div class="kpi kpi--danger tw:bg-white tw:shadow-sm tw:rounded-lg">
            <span class="kpi__icon">💳</span>
            <span class="kpi__value tw:text-xl md:tw:text-2xl tw:font-bold">{{ $paymentStats['unpaid'] }}</span>
            <span class="kpi__label tw:text-xs tw:text-gray-500 tw:font-medium">À payer</span>
        </div>
        <div class="kpi kpi--success tw:bg-white tw:shadow-sm tw:rounded-lg">
            <span class="kpi__icon">✔</span>
            <span class="kpi__value tw:text-xl md:tw:text-2xl tw:font-bold">{{ $paymentStats['paid'] }}</span>
            <span class="kpi__label tw:text-xs tw:text-gray-500 tw:font-medium">Paiements réglés</span>
        </div>
    </div>

    <div class="sec-header tw:flex tw:flex-wrap tw:items-center tw:justify-between tw:gap-3 tw:mt-5">
        <h2>Derniers dossiers</h2>
        <button class="button button--ghost tw:rounded-full tw:px-4 tw:py-2" data-goto-tab="dossiers">Voir tout →</button>
    </div>

    @if ($demandes->isEmpty())
        <div class="panel tw:text-center tw:rounded-2xl tw:border tw:border-emerald-200/60 tw:bg-white/90 tw:p-8">
            <div class="tw:text-4xl tw:mb-3">📋</div>
            <p class="muted tw:mb-4">Aucun dossier pour le moment.</p>
            <button class="button button--primary tw:rounded-full tw:px-5 tw:py-2.5" data-goto-tab="nouvelle-demande">Déposer mon premier dossier →</button>
        </div>
    @else
        <div class="grid grid--2 tw:grid-cols-1 md:tw:grid-cols-2 tw:gap-3">
            @foreach ($demandes->take(6) as $demande)
                <div class="panel tw:rounded-2xl tw:border tw:border-emerald-200/60 tw:bg-white/95 tw:p-4">
                    <div class="split">
                        <div>
                            <strong>{{ $demande->reference }}</strong><br>
                            <span class="muted tw:text-sm">{{ $requestTypes[$demande->request_type] ?? $demande->request_type }} · {{ optional($demande->created_at)->format('d/m/Y') }}</span>
                        </div>
                        <span class="badge {{ $statusClassMap[$demande->status] ?? 'pending' }}">{{ $statusLabels[$demande->status] ?? $demande->status }}</span>
                    </div>
                    <div class="actions tw:mt-2.5 tw:flex tw:flex-wrap tw:gap-2">
                        <a class="button button--ghost tw:rounded-full tw:px-4 tw:py-2" href="{{ route('portal.demandes.show', $demande) }}">Voir</a>
                        @if ($demande->payment_status !== 'paid')
                            <form action="{{ route('portal.citizen.demandes.pay', $demande) }}" method="post">
                                @csrf
                                <button class="button button--accent tw:rounded-full tw:px-4 tw:py-2" type="submit">Payer</button>
                            </form>
                        @else
                            <span class="badge completed">Payé ✓</span>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

{{-- ── Mes dossiers citoyen ────────────── --}}
<div class="tab-pane" data-pane="dossiers">
    <div class="sec-header tw:flex tw:flex-wrap tw:items-center tw:justify-between tw:gap-3">
        <h2>Tous mes dossiers</h2>
        <button class="button button--primary tw:rounded-full tw:px-4 tw:py-2" data-goto-tab="nouvelle-demande">+ Nouvelle demande</button>
    </div>
    @if ($demandes->isEmpty())
        <p class="muted">Aucun dossier déposé.</p>
    @else
        <div class="grid tw:gap-3">
            @foreach ($demandes as $demande)
                <div class="panel tw:rounded-2xl tw:border tw:border-emerald-200/60 tw:bg-white/95 tw:p-4">
                    <div class="split">
                        <div>
                            <strong>{{ $demande->reference }}</strong>
                            <span class="muted tw:ml-2 tw:text-sm">{{ $requestTypes[$demande->request_type] ?? $demande->request_type }}</span><br>
                            <span class="muted tw:text-sm">
                                Déposé le {{ optional($demande->created_at)->format('d/m/Y') }}
                                @if ($demande->agent) · Agent : {{ trim(($demande->agent->first_name ?? '') . ' ' . ($demande->agent->last_name ?? '')) ?: $demande->agent->name }}@endif
                            </span>
                        </div>
                        <span class="badge {{ $statusClassMap[$demande->status] ?? 'pending' }}">{{ $statusLabels[$demande->status] ?? $demande->status }}</span>
                    </div>
                    <div class="actions tw:mt-3 tw:flex tw:flex-wrap tw:gap-2">
                        <a class="button button--ghost tw:rounded-full tw:px-4 tw:py-2" href="{{ route('portal.demandes.show', $demande) }}">Voir le dossier</a>
                        @if ($demande->payment_status !== 'paid')
                            <form action="{{ route('portal.citizen.demandes.pay', $demande) }}" method="post">
                                @csrf
                                <button class="button button--accent tw:rounded-full tw:px-4 tw:py-2" type="submit">Payer</button>
                            </form>
                        @else
                            <span class="badge completed">Paiement confirmé</span>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

{{-- ── Nouvelle demande ────────────────── --}}
<div class="tab-pane" data-pane="nouvelle-demande">
    <div class="sec-header"><h2>Déposer un nouveau dossier</h2></div>
    <div class="panel tw:rounded-2xl tw:border tw:border-emerald-200/60 tw:bg-white/95 tw:max-w-3xl">
        <form action="{{ route('portal.citizen.demandes.store') }}" method="post" enctype="multipart/form-data" class="tw:space-y-2">
            @csrf
            <div class="form-grid tw:gap-4">
                <div class="field">
                    <label for="request_type">Type de demande</label>
                    <select id="request_type" name="request_type" required class="tw:rounded-xl tw:border-emerald-200 tw:bg-white tw:px-3 tw:py-2.5 focus:tw:ring-2 focus:tw:ring-emerald-400/40">
                        <option value="">Sélectionner…</option>
                        @foreach ($requestTypes as $k => $v)
                            <option value="{{ $k }}" @selected(old('request_type') === $k)>{{ $v }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="field">
                    <label for="for_other_person">Cette demande concerne</label>
                    <select id="for_other_person" name="for_other_person" class="tw:rounded-xl tw:border-emerald-200 tw:bg-white tw:px-3 tw:py-2.5 focus:tw:ring-2 focus:tw:ring-emerald-400/40">
                        <option value="0" @selected((string) old('for_other_person', '0') === '0')>Moi-meme</option>
                        <option value="1" @selected((string) old('for_other_person') === '1')>Une autre personne</option>
                    </select>
                    @error('for_other_person')<small class="tw:text-xs tw:text-red-600">{{ $message }}</small>@enderror
                </div>
                <div class="field">
                    <label for="representative_link">Lien avec la personne (si tiers)</label>
                    <input id="representative_link" name="representative_link" type="text" value="{{ old('representative_link') }}" placeholder="Ex: pere, mere, conjoint, tuteur" class="tw:rounded-xl tw:border-emerald-200 tw:bg-white tw:px-3 tw:py-2.5 focus:tw:ring-2 focus:tw:ring-emerald-400/40">
                    @error('representative_link')<small class="tw:text-xs tw:text-red-600">{{ $message }}</small>@enderror
                </div>
                <div class="field">
                    <label>Email</label>
                    <input name="email" type="email" value="{{ old('email', $currentUser->email) }}" required class="tw:rounded-xl tw:border-emerald-200 tw:bg-white tw:px-3 tw:py-2.5 focus:tw:ring-2 focus:tw:ring-emerald-400/40">
                </div>
                <div class="field">
                    <label>Prénom</label>
                    <input name="first_name" type="text" value="{{ old('first_name', $currentUser->first_name) }}" required class="tw:rounded-xl tw:border-emerald-200 tw:bg-white tw:px-3 tw:py-2.5 focus:tw:ring-2 focus:tw:ring-emerald-400/40">
                </div>
                <div class="field">
                    <label>Nom</label>
                    <input name="last_name" type="text" value="{{ old('last_name', $currentUser->last_name) }}" required class="tw:rounded-xl tw:border-emerald-200 tw:bg-white tw:px-3 tw:py-2.5 focus:tw:ring-2 focus:tw:ring-emerald-400/40">
                </div>
                <div class="field">
                    <label>Date de naissance</label>
                    <input name="birth_date" type="date" value="{{ old('birth_date', optional($currentUser->birth_date)->format('Y-m-d')) }}" required class="tw:rounded-xl tw:border-emerald-200 tw:bg-white tw:px-3 tw:py-2.5 focus:tw:ring-2 focus:tw:ring-emerald-400/40">
                </div>
                <div class="field">
                    <label>Lieu de naissance</label>
                    <input name="birth_place" type="text" value="{{ old('birth_place') }}" required class="tw:rounded-xl tw:border-emerald-200 tw:bg-white tw:px-3 tw:py-2.5 focus:tw:ring-2 focus:tw:ring-emerald-400/40">
                </div>
                <div class="field">
                    <label>N° de registre</label>
                    <input name="register_number" type="text" value="{{ old('register_number', $currentUser->register_number) }}" required class="tw:rounded-xl tw:border-emerald-200 tw:bg-white tw:px-3 tw:py-2.5 focus:tw:ring-2 focus:tw:ring-emerald-400/40">
                </div>
                <div class="field field--full">
                    <label>Adresse</label>
                    <textarea name="address" required class="tw:rounded-xl tw:border-emerald-200 tw:bg-white tw:px-3 tw:py-2.5 focus:tw:ring-2 focus:tw:ring-emerald-400/40">{{ old('address', $currentUser->address) }}</textarea>
                </div>
                <div class="field">
                    <label>👨 Prénom du père</label>
                    <input name="parent_one_first_name" type="text" value="{{ old('parent_one_first_name') }}" required class="tw:rounded-xl tw:border-emerald-200 tw:bg-white tw:px-3 tw:py-2.5 focus:tw:ring-2 focus:tw:ring-emerald-400/40">
                </div>
                <div class="field">
                    <label>👨 Nom du père</label>
                    <input name="parent_one_last_name" type="text" value="{{ old('parent_one_last_name') }}" required class="tw:rounded-xl tw:border-emerald-200 tw:bg-white tw:px-3 tw:py-2.5 focus:tw:ring-2 focus:tw:ring-emerald-400/40">
                </div>
                <div class="field">
                    <label>👩 Prénom de la mère</label>
                    <input name="parent_two_first_name" type="text" value="{{ old('parent_two_first_name') }}" required class="tw:rounded-xl tw:border-emerald-200 tw:bg-white tw:px-3 tw:py-2.5 focus:tw:ring-2 focus:tw:ring-emerald-400/40">
                </div>
                <div class="field">
                    <label>👩 Nom de la mère</label>
                    <input name="parent_two_last_name" type="text" value="{{ old('parent_two_last_name') }}" required class="tw:rounded-xl tw:border-emerald-200 tw:bg-white tw:px-3 tw:py-2.5 focus:tw:ring-2 focus:tw:ring-emerald-400/40">
                </div>
                <div class="field field--full">
                    <label>Précisions supplémentaires</label>
                    <textarea name="details" class="tw:rounded-xl tw:border-emerald-200 tw:bg-white tw:px-3 tw:py-2.5 focus:tw:ring-2 focus:tw:ring-emerald-400/40">{{ old('details') }}</textarea>
                </div>
                <div class="field field--full">
                    <label><i class="bi bi-file-earmark-pdf me-1"></i>📎 Fichiers/Pièces jointes (optionnel)</label>
                    <input name="attachment" type="file" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" class="tw:rounded-xl tw:border-emerald-200 tw:bg-white tw:px-3 tw:py-2.5 focus:tw:ring-2 focus:tw:ring-emerald-400/40">
                    <small class="tw:text-xs tw:text-gray-600">Formats acceptés: PDF, Word, Images. Maximum 10 MB</small>
                    @error('attachment')<small class="tw:text-xs tw:text-red-600">{{ $message }}</small>@enderror
                </div>
            </div>
            <button class="button button--primary tw:rounded-full tw:px-5 tw:py-2.5" type="submit">Envoyer le dossier</button>
        </form>
    </div>
</div>

{{-- ── Citoyenneté et Patriotisme ──────── --}}
<div class="tab-pane" data-pane="citoyennete">
    <div class="sec-header tw:mb-5">
        <h2>🏛️ Citoyenneté et Patriotisme</h2>
        <p class="muted tw:mt-2">Développez vos connaissances civiques et participez aux activités de la mairie</p>
        @if($role === 'admin')
            <a href="{{ route('portal.admin.civic_courses') }}" class="button button--primary tw:text-xs tw:mt-3 tw:inline-flex tw:items-center">
                <i class="bi bi-gear me-1"></i>Gérer les cours
            </a>
        @endif
    </div>

    {{-- Cours citoyens --}}
    <h3 class="tw:font-bold tw:text-lg tw:mb-4">📚 Cours de citoyenneté</h3>
    @if($civicCourses && $civicCourses->count() > 0)
        <div class="grid tw:grid-cols-1 lg:tw:grid-cols-2 tw:gap-4 tw:mb-6">
            @foreach($civicCourses as $course)
                <div class="panel tw:rounded-2xl tw:border tw:border-emerald-200/60 tw:bg-gradient-to-br tw:from-emerald-50 tw:to-white tw:shadow-sm tw:p-6 hover:tw:shadow-md tw:transition-all">
                    <div class="tw:flex tw:items-start tw:justify-between tw:mb-3">
                        <div class="tw:text-4xl">{{ $course->icon_emoji ?? '📖' }}</div>
                        <span class="badge tw:rounded-full tw:bg-emerald-100 tw:text-emerald-800 tw:text-xs tw:font-medium tw:px-2 tw:py-1">
                            @if($course->course_type === 'online') En ligne
                            @elseif($course->course_type === 'hybrid') Hybride
                            @else Présentiel
                            @endif
                        </span>
                    </div>
                    <h3 class="tw:font-bold tw:text-lg tw:mb-2">{{ $course->title }}</h3>
                    <p class="muted tw:text-sm tw:mb-4">{{ $course->description }}</p>
                    
                    @if($course->topicsArray() && count($course->topicsArray()) > 0)
                        <ul class="tw:space-y-2 tw:text-sm tw:mb-4 tw:text-gray-700">
                            @foreach($course->topicsArray() as $topic)
                                <li>✓ {{ $topic }}</li>
                            @endforeach
                        </ul>
                    @endif
                    
                    <div class="tw:flex tw:flex-wrap tw:gap-2">
                        <a href="{{ route('portal.citizen.course.view', $course) }}" class="button button--primary tw:rounded-full tw:px-4 tw:py-2 tw:text-sm tw:inline-flex tw:items-center" title="Accédez au cours">
                            <i class="bi bi-play-circle me-1"></i>Accéder au cours
                        </a>
                        <span class="tw:text-xs tw:text-gray-500 tw:self-center">⏱ ~{{ $course->duration_minutes ?? 30 }} minutes</span>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="panel tw:rounded-2xl tw:border tw:border-amber-200/60 tw:bg-amber-50/50 tw:p-6 tw:mb-6">
            <p class="muted tw:text-center tw:mb-3">Aucun cours disponible actuellement.</p>
            @if($role === 'admin')
                <div class="tw:text-center">
                    <a href="{{ route('portal.admin.civic_courses.create') }}" class="button button--primary tw:text-xs tw:inline-flex tw:items-center">
                        <i class="bi bi-plus-circle me-1"></i>Ajouter un cours
                    </a>
                </div>
            @endif
        </div>
    @endif

    {{-- Activités organisées --}}
    <div class="sec-header tw:mb-5 tw:mt-8">
        <h2 class="tw:text-lg">📅 Activités organisées par la mairie</h2>
        <p class="muted tw:text-sm tw:mt-2">Participez à nos événements et ateliers citoyens</p>
        @if($role === 'admin')
            <a href="{{ route('portal.admin.civic_activities') }}" class="button button--primary tw:text-xs tw:mt-3 tw:inline-flex tw:items-center">
                <i class="bi bi-gear me-1"></i>Gérer les activités
            </a>
        @endif
    </div>

    @if($civicActivities && $civicActivities->count() > 0)
        <div class="grid tw:grid-cols-1 lg:tw:grid-cols-2 tw:gap-4">
            @foreach($civicActivities as $activity)
                <div class="panel tw:rounded-2xl tw:border tw:border-blue-200/60 tw:bg-gradient-to-br tw:from-blue-50 tw:to-white tw:shadow-sm tw:p-6">
                    <div class="tw:flex tw:items-start tw:justify-between tw:mb-3">
                        <div class="tw:text-4xl">{{ $activity->icon_emoji ?? '🎯' }}</div>
                        <span class="badge tw:rounded-full @if($activity->status === 'upcoming') tw:bg-green-100 tw:text-green-800 @elseif($activity->status === 'ongoing') tw:bg-blue-100 tw:text-blue-800 @elseif($activity->status === 'completed') tw:bg-gray-100 tw:text-gray-800 @else tw:bg-red-100 tw:text-red-800 @endif tw:text-xs tw:font-medium tw:px-2 tw:py-1">
                            {{ $activity->getStatusLabel() }}
                        </span>
                    </div>
                    <h3 class="tw:font-bold tw:text-lg tw:mb-2">{{ $activity->title }}</h3>
                    <p class="muted tw:text-sm tw:mb-3">{{ $activity->description }}</p>
                    
                    <ul class="tw:space-y-2 tw:text-sm tw:mb-4 tw:text-gray-700">
                        @if($activity->event_date)
                            <li>📅 {{ \Carbon\Carbon::parse($activity->event_date)->translatedFormat('d F Y') }}</li>
                        @endif
                        @if($activity->event_start_time && $activity->event_end_time)
                            <li>⏰ {{ $activity->event_start_time }} - {{ $activity->event_end_time }}</li>
                        @endif
                        @if($activity->location)
                            <li>📍 {{ $activity->location }}</li>
                        @endif
                        @if($activity->target_audience)
                            <li>👥 {{ $activity->target_audience }}</li>
                        @endif
                    </ul>

                    <!-- Fichiers joints -->
                    @if($activity->image_path || $activity->document_path)
                        <div class="tw:flex tw:flex-wrap tw:gap-2 tw:mb-4">
                            @if($activity->image_path)
                                <a href="{{ asset('storage/' . $activity->image_path) }}" target="_blank" class="tw:inline-flex tw:items-center tw:gap-1 tw:text-xs tw:text-emerald-700 tw:bg-emerald-50 tw:px-2 tw:py-1 tw:rounded" title="Voir l'image">
                                    <i class="bi bi-image"></i>Image
                                </a>
                            @endif
                            @if($activity->document_path)
                                <a href="{{ asset('storage/' . $activity->document_path) }}" target="_blank" download class="tw:inline-flex tw:items-center tw:gap-1 tw:text-xs tw:text-emerald-700 tw:bg-emerald-50 tw:px-2 tw:py-1 tw:rounded" title="Télécharger le document">
                                    <i class="bi bi-file-earmark"></i>Document
                                </a>
                            @endif
                        </div>
                    @endif
                    
                    <form action="{{ route('portal.citizen.activity.register', $activity) }}" method="POST" class="tw:inline">
                        @csrf
                        <button type="submit" class="button button--accent tw:rounded-full tw:px-4 tw:py-2 tw:text-sm tw:inline-flex tw:items-center">
                            <i class="bi bi-clipboard-check me-1"></i>M'inscrire
                        </button>
                    </form>
                </div>
            @endforeach
        </div>
    @else
        <div class="panel tw:rounded-2xl tw:border tw:border-amber-200/60 tw:bg-amber-50/50 tw:p-6">
            <p class="muted tw:text-center tw:mb-3">Aucune activité organisée pour le moment.</p>
            @if($role === 'admin')
                <div class="tw:text-center">
                    <a href="{{ route('portal.admin.civic_activities.create') }}" class="button button--primary tw:text-xs tw:inline-flex tw:items-center">
                        <i class="bi bi-plus-circle me-1"></i>Créer une activité
                    </a>
                </div>
            @endif
        </div>
    @endif

    {{-- Section informations --}}
    <div class="panel tw:rounded-2xl tw:border tw:border-emerald-200/60 tw:bg-white/95 tw:mt-6 tw:p-6">
        <h3 class="tw:font-bold tw:text-lg tw:mb-3">📋 Pourquoi participer?</h3>
        <div class="tw:grid tw:grid-cols-2 lg:tw:grid-cols-4 tw:gap-4">
            <div class="tw:text-center">
                <div class="tw:text-2xl tw:mb-2">🤝</div>
                <strong class="tw:text-sm tw:block">Connexion</strong>
                <p class="tw:text-xs tw:text-gray-600">Rencontrez vos concitoyens</p>
            </div>
            <div class="tw:text-center">
                <div class="tw:text-2xl tw:mb-2">📚</div>
                <strong class="tw:text-sm tw:block">Apprentissage</strong>
                <p class="tw:text-xs tw:text-gray-600">Développez vos connaissances</p>
            </div>
            <div class="tw:text-center">
                <div class="tw:text-2xl tw:mb-2">🗣️</div>
                <strong class="tw:text-sm tw:block">Voix</strong>
                <p class="tw:text-xs tw:text-gray-600">Faites entendre votre avis</p>
            </div>
            <div class="tw:text-center">
                <div class="tw:text-2xl tw:mb-2">🌟</div>
                <strong class="tw:text-sm tw:block">Impact</strong>
                <p class="tw:text-xs tw:text-gray-600">Changez votre communauté</p>
            </div>
        </div>
    </div>
</div>

{{-- ── Messages citoyen ────────────────── --}}
<div class="tab-pane" data-pane="messages">
    <div class="sec-header"><h2>Messagerie</h2></div>
    <div class="panel tw:rounded-2xl tw:border tw:border-emerald-200/60 tw:bg-white/95 tw:mb-4">
        <h3 class="tw:mb-3">Nouveau message</h3>
        <form action="{{ route('portal.messages.store') }}" method="post">
            @csrf
            <div class="form-grid tw:gap-4">
                <div class="field">
                    <label>Dossier</label>
                    <select name="demande_id" required @disabled($citizenMessageDemandes->isEmpty()) class="tw:rounded-xl tw:border-emerald-200 tw:bg-white tw:px-3 tw:py-2.5 focus:tw:ring-2 focus:tw:ring-emerald-400/40">
                        <option value="">Choisir un dossier</option>
                        @foreach ($citizenMessageDemandes as $demande)
                            <option value="{{ $demande->id }}" @selected(old('demande_id') == $demande->id)>
                                {{ $demande->reference }} — {{ $requestTypes[$demande->request_type] ?? $demande->request_type }}
                            </option>
                        @endforeach
                    </select>
                    @error('demande_id')<small class="tw:text-xs tw:text-red-600">{{ $message }}</small>@enderror
                </div>
                <div class="field field--full">
                    <label>Message</label>
                    <textarea name="body" rows="4" required @disabled($citizenMessageDemandes->isEmpty()) class="tw:rounded-xl tw:border-emerald-200 tw:bg-white tw:px-3 tw:py-2.5 focus:tw:ring-2 focus:tw:ring-emerald-400/40">{{ old('body') }}</textarea>
                    @error('body')<small class="tw:text-xs tw:text-red-600">{{ $message }}</small>@enderror
                </div>
            </div>
            @if ($citizenMessageDemandes->isEmpty())
                <p class="muted tw:m-0">Vous devez créer un dossier avant d'envoyer un message.</p>
            @else
                <button class="button button--primary tw:rounded-full tw:px-5 tw:py-2.5" type="submit">Envoyer le message</button>
            @endif
        </form>
    </div>
    <div class="msg-switcher">
        <button class="msg-tab-btn active tw:text-sm tw:font-medium" data-msg-tab="recus" data-msg-group="c">
            📥 Reçus <span class="msg-count">{{ $receivedMessages->count() }}</span>
        </button>
        <button class="msg-tab-btn tw:text-sm tw:font-medium" data-msg-tab="envoyes" data-msg-group="c">
            📤 Envoyés <span class="msg-count">{{ $sentMessages->count() }}</span>
        </button>
    </div>
    <div class="msg-pane active" data-msg-pane="recus" data-msg-group="c">
        @if ($receivedMessages->isEmpty())
            <p class="muted">Aucun message reçu pour le moment.</p>
        @else
            <div class="msg-list">
                @foreach ($receivedMessages as $msg)
                    <div class="msg-card tw:rounded-2xl tw:border tw:border-emerald-200/60 tw:bg-white/95 tw:p-4">
                        <div class="msg-card__header">
                            <span class="msg-card__from">{{ trim(($msg->sender->first_name ?? '') . ' ' . ($msg->sender->last_name ?? '')) ?: ($msg->sender->name ?? 'Utilisateur') }}<span class="muted tw:text-xs tw:ml-1.5">({{ $msg->sender?->role === 'admin' ? 'Administrateur' : ($msg->sender?->role === 'agent' ? 'Agent' : 'Citoyen') }})</span></span>
                            <span class="msg-card__time">{{ optional($msg->created_at)->format('d/m/Y H:i') }}</span>
                        </div>
                        @if ($msg->demande)<span class="msg-card__ref">📁 Dossier {{ $msg->demande->reference }}</span>@endif
                        <p class="msg-card__body tw:text-slate-800">{{ $msg->body }}</p>
                        @if ($msg->demande)
                            <a class="button button--ghost tw:rounded-full tw:px-4 tw:py-2 tw:w-fit tw:text-sm" href="{{ route('portal.demandes.show', $msg->demande) }}">Ouvrir le dossier</a>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>
    <div class="msg-pane" data-msg-pane="envoyes" data-msg-group="c">
        @if ($sentMessages->isEmpty())
            <p class="muted">Aucun message envoyé.</p>
        @else
            <div class="msg-list">
                @foreach ($sentMessages as $msg)
                    <div class="msg-card tw:rounded-2xl tw:border tw:border-emerald-200/60 tw:bg-white/95 tw:p-4">
                        <div class="msg-card__header">
                            <span class="msg-card__from">→ {{ trim(($msg->receiver->first_name ?? '') . ' ' . ($msg->receiver->last_name ?? '')) ?: ($msg->receiver->name ?? 'Utilisateur') }}<span class="muted tw:text-xs tw:ml-1.5">({{ $msg->receiver?->role === 'admin' ? 'Administrateur' : ($msg->receiver?->role === 'agent' ? 'Agent' : 'Citoyen') }})</span></span>
                            <span class="msg-card__time">{{ optional($msg->created_at)->format('d/m/Y H:i') }}</span>
                        </div>
                        @if ($msg->demande)<span class="msg-card__ref">📁 Dossier {{ $msg->demande->reference }}</span>@endif
                        <p class="msg-card__body tw:text-slate-800">{{ $msg->body }}</p>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

{{-- ── Profil citoyen ──────────────────── --}}
<div class="tab-pane" data-pane="profil">
    <div class="sec-header"><h2>Mon profil citoyen</h2></div>
    <div class="grid grid--2 tw:grid-cols-1 lg:tw:grid-cols-2 tw:gap-5 tw:items-start">
        <div class="panel tw:rounded-2xl tw:border tw:border-emerald-200/60 tw:bg-white/95">
            <span class="eyebrow">Identité</span>
            <h3>Informations enregistrées</h3>
            <ul class="list">
                <li><strong>N°CIT</strong><br><span class="muted">{{ $currentUser->citizen_number ?: '—' }}</span></li>
                <li><strong>Email</strong><br><span class="muted">{{ $currentUser->email }}</span></li>
                <li><strong>Téléphone</strong><br><span class="muted">{{ $currentUser->phone ?: 'Non renseigné' }}</span></li>
                <li><strong>N° de registre</strong><br><span class="muted">{{ $currentUser->register_number ?: 'Non renseigné' }}</span></li>
                <li><strong>Dernière connexion</strong><br><span class="muted">{{ optional($currentUser->last_login_at)->format('d/m/Y H:i') ?: 'Première connexion' }}</span></li>
            </ul>
        </div>
        <div class="panel tw:rounded-2xl tw:border tw:border-emerald-200/60 tw:bg-white/95">
            <span class="eyebrow">Modifier</span>
            <h3>Mettre à jour</h3>
            <form action="{{ route('portal.citizen.profile.save') }}" method="post">
                @csrf
                <div class="form-grid tw:gap-4">
                    <div class="field">
                        <label>Prénom</label>
                        <input name="first_name" type="text" value="{{ old('first_name', $currentUser->first_name) }}" class="tw:rounded-xl tw:border-emerald-200 tw:bg-white tw:px-3 tw:py-2.5 focus:tw:ring-2 focus:tw:ring-emerald-400/40">
                    </div>
                    <div class="field">
                        <label>Nom</label>
                        <input name="last_name" type="text" value="{{ old('last_name', $currentUser->last_name) }}" class="tw:rounded-xl tw:border-emerald-200 tw:bg-white tw:px-3 tw:py-2.5 focus:tw:ring-2 focus:tw:ring-emerald-400/40">
                    </div>
                    <div class="field">
                        <label>Email</label>
                        <input name="email" type="email" value="{{ old('email', $currentUser->email) }}" required class="tw:rounded-xl tw:border-emerald-200 tw:bg-white tw:px-3 tw:py-2.5 focus:tw:ring-2 focus:tw:ring-emerald-400/40">
                    </div>
                    <div class="field">
                        <label>Téléphone</label>
                        <input name="phone" type="text" value="{{ old('phone', $currentUser->phone) }}" class="tw:rounded-xl tw:border-emerald-200 tw:bg-white tw:px-3 tw:py-2.5 focus:tw:ring-2 focus:tw:ring-emerald-400/40">
                    </div>
                    <div class="field">
                        <label>Date de naissance</label>
                        <input name="birth_date" type="date" value="{{ old('birth_date', optional($currentUser->birth_date)->format('Y-m-d')) }}" class="tw:rounded-xl tw:border-emerald-200 tw:bg-white tw:px-3 tw:py-2.5 focus:tw:ring-2 focus:tw:ring-emerald-400/40">
                    </div>
                    <div class="field">
                        <label>Lieu de naissance</label>
                        <input name="birth_place" type="text" value="{{ old('birth_place', $currentUser->birth_place) }}" class="tw:rounded-xl tw:border-emerald-200 tw:bg-white tw:px-3 tw:py-2.5 focus:tw:ring-2 focus:tw:ring-emerald-400/40">
                    </div>
                    <div class="field">
                        <label>N° de registre</label>
                        <input name="register_number" type="text" value="{{ old('register_number', $currentUser->register_number) }}" class="tw:rounded-xl tw:border-emerald-200 tw:bg-white tw:px-3 tw:py-2.5 focus:tw:ring-2 focus:tw:ring-emerald-400/40">
                    </div>
                    <div class="field field--full">
                        <label>Adresse</label>
                        <textarea name="address" class="tw:rounded-xl tw:border-emerald-200 tw:bg-white tw:px-3 tw:py-2.5 focus:tw:ring-2 focus:tw:ring-emerald-400/40">{{ old('address', $currentUser->address) }}</textarea>
                    </div>
                </div>
                <button class="button button--primary tw:rounded-full tw:px-5 tw:py-2.5" type="submit">Enregistrer</button>
            </form>
        </div>
    </div>
</div>

{{-- ══════════════════════════════ AGENT ════════════════════════════════ --}}
@elseif ($role === 'agent')

<nav class="tab-nav tw:flex tw:flex-wrap tw:gap-2 tw:mb-5 tw:bg-gray-50">
    <button class="tab-btn tw:rounded-full tw:px-4 tw:py-2 tw:text-sm tw:font-medium" data-tab="accueil">📊 Vue d'ensemble</button>
    <button class="tab-btn tw:rounded-full tw:px-4 tw:py-2 tw:text-sm tw:font-medium" data-tab="file-attente">
        📥 File d'attente
        @if ($pendingDemandes->count() > 0)<span class="tab-badge">{{ $pendingDemandes->count() }}</span>@endif
    </button>
    <button class="tab-btn tw:rounded-full tw:px-4 tw:py-2 tw:text-sm tw:font-medium" data-tab="mes-dossiers">
        🗂 Mes dossiers
        @if ($stats['total'] > 0)<span class="tab-badge">{{ $stats['total'] }}</span>@endif
    </button>
    <button class="tab-btn tw:rounded-full tw:px-4 tw:py-2 tw:text-sm tw:font-medium" data-tab="messages">
        ✉ Messages
        @if ($agentMessagesReceived->count() > 0)<span class="tab-badge">{{ $agentMessagesReceived->count() }}</span>@endif
    </button>
    <button class="tab-btn tw:rounded-full tw:px-4 tw:py-2 tw:text-sm tw:font-medium" data-tab="publipostage">📨 Publipostage</button>
    <button class="tab-btn tw:rounded-full tw:px-4 tw:py-2 tw:text-sm tw:font-medium" data-tab="profil">👤 Mon profil</button>
</nav>

{{-- ── Vue d'ensemble agent ────────────── --}}
<div class="tab-pane" data-pane="accueil">
    <div class="kpi-row tw:grid tw:grid-cols-2 lg:tw:grid-cols-3 tw:gap-3">
        <div class="kpi kpi--brand tw:bg-white tw:shadow-sm tw:rounded-lg">
            <span class="kpi__icon">🗂</span>
            <span class="kpi__value tw:text-xl md:tw:text-2xl tw:font-bold">{{ $stats['total'] }}</span>
            <span class="kpi__label tw:text-xs tw:text-gray-500 tw:font-medium">Dossiers assignés</span>
        </div>
        <div class="kpi kpi--warn tw:bg-white tw:shadow-sm tw:rounded-lg">
            <span class="kpi__icon">⏳</span>
            <span class="kpi__value tw:text-xl md:tw:text-2xl tw:font-bold">{{ $stats['assigned'] + $stats['processing'] }}</span>
            <span class="kpi__label tw:text-xs tw:text-gray-500 tw:font-medium">En cours</span>
        </div>
        <div class="kpi kpi--success tw:bg-white tw:shadow-sm tw:rounded-lg">
            <span class="kpi__icon">✅</span>
            <span class="kpi__value tw:text-xl md:tw:text-2xl tw:font-bold">{{ $agentPerformance['completed'] }}</span>
            <span class="kpi__label tw:text-xs tw:text-gray-500 tw:font-medium">Finalisés (total)</span>
        </div>
        <div class="kpi kpi--brand tw:bg-white tw:shadow-sm tw:rounded-lg">
            <span class="kpi__icon">📅</span>
            <span class="kpi__value tw:text-xl md:tw:text-2xl tw:font-bold">{{ $agentPerformance['monthly_completed'] }}</span>
            <span class="kpi__label tw:text-xs tw:text-gray-500 tw:font-medium">Traités ce mois</span>
        </div>
        <div class="kpi kpi--accent tw:bg-white tw:shadow-sm tw:rounded-lg">
            <span class="kpi__icon">⚡</span>
            <span class="kpi__value tw:text-xl md:tw:text-2xl tw:font-bold">{{ $agentPerformance['completion_rate'] }}%</span>
            <span class="kpi__label tw:text-xs tw:text-gray-500 tw:font-medium">Taux de traitement</span>
        </div>
        <div class="kpi kpi--warn tw:bg-white tw:shadow-sm tw:rounded-lg">
            <span class="kpi__icon">💳</span>
            <span class="kpi__value tw:text-xl md:tw:text-2xl tw:font-bold">{{ $paymentValidationStats['pending'] }}</span>
            <span class="kpi__label tw:text-xs tw:text-gray-500 tw:font-medium">Paiements à valider</span>
        </div>
    </div>

    @if ($agentPerformance['completion_rate'] > 0)
        <div class="panel tw:rounded-2xl tw:border tw:border-emerald-200/60 tw:bg-white/95 tw:px-5 tw:py-4 tw:mb-5">
            <div class="split tw:mb-2">
                <span class="eyebrow">Taux de traitement global</span>
                <strong>{{ $agentPerformance['completion_rate'] }}%</strong>
            </div>
            <div class="progress-bar">
                <div class="progress-bar__fill" data-width="{{ $agentPerformance['completion_rate'] }}"></div>
            </div>
        </div>
    @endif

    <div class="grid grid--2 tw:grid-cols-1 lg:tw:grid-cols-2 tw:gap-5">
        <div class="panel tw:rounded-2xl tw:border tw:border-emerald-200/60 tw:bg-white/95">
            <span class="eyebrow">Performance</span>
            <h3>Indicateurs détaillés</h3>
            <ul class="list">
                <li class="split"><span>Dossiers finalisés</span><strong>{{ $agentPerformance['completed'] }}</strong></li>
                <li class="split"><span>Dossiers rejetés</span><strong>{{ $agentPerformance['rejected'] }}</strong></li>
                <li class="split"><span>Délai moyen</span><strong>{{ $agentPerformance['avg_processing_hours'] !== null ? $agentPerformance['avg_processing_hours'] . ' h' : '—' }}</strong></li>
                <li class="split"><span>Paiements validés</span><strong>{{ $paymentValidationStats['validated'] }}</strong></li>
            </ul>
        </div>
        <div class="panel tw:rounded-2xl tw:border tw:border-emerald-200/60 tw:bg-white/95">
            <span class="eyebrow">Actions rapides</span>
            <h3>Accès direct</h3>
            <div class="tw:grid tw:gap-2.5">
                <button class="button button--primary tw:w-full tw:justify-start tw:rounded-xl tw:px-4 tw:py-2.5" data-goto-tab="file-attente">
                    📥 File d'attente ({{ $pendingDemandes->count() }} dossier(s))
                </button>
                @if ($paymentValidationStats['pending'] > 0)
                    <button class="button button--accent tw:w-full tw:justify-start tw:rounded-xl tw:px-4 tw:py-2.5" data-goto-tab="mes-dossiers">
                        💳 Valider paiements ({{ $paymentValidationStats['pending'] }})
                    </button>
                @endif
                <button class="button button--ghost tw:w-full tw:justify-start tw:rounded-xl tw:px-4 tw:py-2.5" data-goto-tab="messages">
                    ✉ Messagerie ({{ $agentMessagesReceived->count() }} reçu(s))
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ── File d'attente ──────────────────── --}}
<div class="tab-pane" data-pane="file-attente">
    <div class="sec-header"><h2>File d'attente — dossiers non assignés</h2></div>
    @if ($pendingDemandes->isEmpty())
        <div class="panel tw:text-center tw:rounded-2xl tw:border tw:border-emerald-200/60 tw:bg-white/95 tw:p-8">
            <div class="tw:text-4xl tw:mb-3">🎉</div>
            <p class="muted">Aucun dossier en attente d'assignation.</p>
        </div>
    @else
        <div class="grid tw:gap-3">
            @foreach ($pendingDemandes as $demande)
                <div class="panel tw:rounded-2xl tw:border tw:border-emerald-200/60 tw:bg-white/95 tw:p-4">
                    <div class="split">
                        <div>
                            <strong>{{ $demande->reference }}</strong>
                            <span class="muted tw:ml-2 tw:text-sm">{{ $requestTypes[$demande->request_type] ?? $demande->request_type }}</span><br>
                            <span class="muted tw:text-sm">
                                {{ trim(($demande->citoyen->first_name ?? '') . ' ' . ($demande->citoyen->last_name ?? '')) ?: ($demande->citoyen->name ?? 'Usager') }}
                                · Déposé le {{ optional($demande->created_at)->format('d/m/Y') }}
                            </span>
                        </div>
                        <span class="badge pending">En attente</span>
                    </div>
                    <div class="actions tw:mt-2 tw:flex tw:flex-wrap tw:gap-2">
                        <a class="button button--ghost tw:rounded-full tw:px-4 tw:py-2" href="{{ route('portal.demandes.show', $demande) }}">Analyser</a>
                        <form action="{{ route('portal.demandes.assign', $demande) }}" method="post">
                            @csrf
                            <button class="button button--primary tw:rounded-full tw:px-4 tw:py-2" type="submit">Prendre en charge</button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

{{-- ── Mes dossiers agent ──────────────── --}}
<div class="tab-pane" data-pane="mes-dossiers">
    <div class="sec-header"><h2>Mon portefeuille de dossiers</h2></div>
    @if ($assignedDemandes->isEmpty())
        <p class="muted">Aucun dossier assigné à votre compte.</p>
    @else
        <div class="grid tw:gap-3">
            @foreach ($assignedDemandes as $demande)
            <div class="panel tw:rounded-2xl tw:border tw:border-emerald-200/60 tw:bg-white/95 tw:p-4">
                    <div class="split">
                        <div>
                            <strong>{{ $demande->reference }}</strong>
                            <span class="muted tw:ml-2 tw:text-sm">{{ $requestTypes[$demande->request_type] ?? $demande->request_type }}</span><br>
                            <span class="muted tw:text-sm">
                                {{ trim(($demande->citoyen->first_name ?? '') . ' ' . ($demande->citoyen->last_name ?? '')) ?: ($demande->citoyen->name ?? 'Usager') }}
                                · {{ optional($demande->created_at)->format('d/m/Y') }}
                            </span>
                        </div>
                        <div class="tw:flex tw:flex-col tw:items-end tw:gap-1.5">
                            <span class="badge {{ $statusClassMap[$demande->status] ?? 'pending' }}">{{ $statusLabels[$demande->status] ?? $demande->status }}</span>
                            @if ($demande->payment_status === 'paid_pending')
                                <span class="badge tw:bg-amber-100 tw:text-amber-700 tw:border-amber-200">💳 À valider</span>
                            @elseif ($demande->payment_status === 'paid')
                                <span class="badge completed">✓ Payé</span>
                            @endif
                        </div>
                    </div>
                    <div class="actions tw:mt-2 tw:flex tw:flex-wrap tw:gap-2">
                        <a class="button button--ghost tw:rounded-full tw:px-4 tw:py-2" href="{{ route('portal.demandes.show', $demande) }}">Ouvrir</a>
                        @if ($demande->payment_status === 'paid_pending')
                            <form action="{{ route('portal.agent.demandes.payment.validate', $demande) }}" method="post">
                                @csrf
                                <button class="button button--primary tw:rounded-full tw:px-4 tw:py-2" type="submit">Valider paiement</button>
                            </form>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

{{-- ── Messages agent ──────────────────── --}}
<div class="tab-pane" data-pane="messages">
    <div class="sec-header"><h2>Messagerie</h2></div>
    <div class="panel tw:rounded-2xl tw:border tw:border-emerald-200/60 tw:bg-white/95 tw:mb-4">
        <h3 class="tw:mb-3">Nouveau message</h3>
        <form action="{{ route('portal.messages.store') }}" method="post">
            @csrf
            <div class="form-grid tw:gap-4">
                <div class="field">
                    <label>Dossier</label>
                    <select name="demande_id" required @disabled($agentMessageDemandes->isEmpty()) class="tw:rounded-xl tw:border-emerald-200 tw:bg-white tw:px-3 tw:py-2.5 focus:tw:ring-2 focus:tw:ring-emerald-400/40">
                        <option value="">Choisir un dossier</option>
                        @foreach ($agentMessageDemandes as $demande)
                            <option value="{{ $demande->id }}" @selected(old('demande_id') == $demande->id)>
                                {{ $demande->reference }} — {{ trim(($demande->citoyen->first_name ?? '') . ' ' . ($demande->citoyen->last_name ?? '')) ?: ($demande->citoyen->name ?? 'Citoyen') }}
                            </option>
                        @endforeach
                    </select>
                    @error('demande_id')<small class="tw:text-xs tw:text-red-600">{{ $message }}</small>@enderror
                </div>
                <div class="field field--full">
                    <label>Message</label>
                    <textarea name="body" rows="4" required @disabled($agentMessageDemandes->isEmpty()) class="tw:rounded-xl tw:border-emerald-200 tw:bg-white tw:px-3 tw:py-2.5 focus:tw:ring-2 focus:tw:ring-emerald-400/40">{{ old('body') }}</textarea>
                    @error('body')<small class="tw:text-xs tw:text-red-600">{{ $message }}</small>@enderror
                </div>
            </div>
            @if ($agentMessageDemandes->isEmpty())
                <p class="muted tw:m-0">Aucun dossier assigné pour envoyer un message.</p>
            @else
                <button class="button button--primary tw:rounded-full tw:px-5 tw:py-2.5" type="submit">Envoyer le message</button>
            @endif
        </form>
    </div>
    <div class="msg-switcher">
        <button class="msg-tab-btn active tw:text-sm tw:font-medium" data-msg-tab="recus" data-msg-group="a">
            📥 Reçus <span class="msg-count">{{ $agentMessagesReceived->count() }}</span>
        </button>
        <button class="msg-tab-btn tw:text-sm tw:font-medium" data-msg-tab="envoyes" data-msg-group="a">
            📤 Envoyés <span class="msg-count">{{ $agentMessagesSent->count() }}</span>
        </button>
    </div>
    <div class="msg-pane active" data-msg-pane="recus" data-msg-group="a">
        @if ($agentMessagesReceived->isEmpty())
            <p class="muted">Aucun message reçu.</p>
        @else
            <div class="msg-list">
                @foreach ($agentMessagesReceived as $msg)
                    <div class="msg-card tw:rounded-2xl tw:border tw:border-emerald-200/60 tw:bg-white/95 tw:p-4">
                        <div class="msg-card__header">
                            <span class="msg-card__from">{{ trim(($msg->sender->first_name ?? '') . ' ' . ($msg->sender->last_name ?? '')) ?: ($msg->sender->name ?? 'Utilisateur') }}<span class="muted tw:text-xs tw:ml-1.5">({{ $msg->sender?->role === 'admin' ? 'Administrateur' : ($msg->sender?->role === 'agent' ? 'Agent' : 'Citoyen') }})</span></span>
                            <span class="msg-card__time">{{ optional($msg->created_at)->format('d/m/Y H:i') }}</span>
                        </div>
                        @if ($msg->demande)<span class="msg-card__ref">📁 Dossier {{ $msg->demande->reference }}</span>@endif
                        <p class="msg-card__body tw:text-slate-800">{{ $msg->body }}</p>
                        @if ($msg->demande)
                            <a class="button button--ghost tw:rounded-full tw:px-4 tw:py-2 tw:w-fit tw:text-sm" href="{{ route('portal.demandes.show', $msg->demande) }}">Ouvrir le dossier</a>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>
    <div class="msg-pane" data-msg-pane="envoyes" data-msg-group="a">
        @if ($agentMessagesSent->isEmpty())
            <p class="muted">Aucun message envoyé.</p>
        @else
            <div class="msg-list">
                @foreach ($agentMessagesSent as $msg)
                    <div class="msg-card tw:rounded-2xl tw:border tw:border-emerald-200/60 tw:bg-white/95 tw:p-4">
                        <div class="msg-card__header">
                            <span class="msg-card__from">→ {{ trim(($msg->receiver->first_name ?? '') . ' ' . ($msg->receiver->last_name ?? '')) ?: ($msg->receiver->name ?? 'Utilisateur') }}<span class="muted tw:text-xs tw:ml-1.5">({{ $msg->receiver?->role === 'admin' ? 'Administrateur' : ($msg->receiver?->role === 'agent' ? 'Agent' : 'Citoyen') }})</span></span>
                            <span class="msg-card__time">{{ optional($msg->created_at)->format('d/m/Y H:i') }}</span>
                        </div>
                        @if ($msg->demande)<span class="msg-card__ref">📁 Dossier {{ $msg->demande->reference }}</span>@endif
                        <p class="msg-card__body tw:text-slate-800">{{ $msg->body }}</p>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

{{-- ── Publipostage ────────────────────── --}}
<div class="tab-pane" data-pane="publipostage">
    <div class="sec-header"><h2>Publipostage — Export par type</h2></div>
    <p class="muted tw:mb-5">Exports générés par lots de 10 demandeurs payés. Téléversez votre modèle Word (.docx) pour chaque type.</p>
    <div class="table-card">
        <table>
            <thead>
                <tr>
                    <th>Type de demande</th>
                    <th>Payés</th>
                    <th>Lots</th>
                    <th>Modèle Word</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($agentMailMergeByType as $typeKey => $data)
                    <tr>
                        <td><strong>{{ $data['label'] }}</strong></td>
                        <td>{{ $data['count'] }} <span class="muted">({{ $data['remainder'] }} hors lot)</span></td>
                        <td>
                            @if ($data['full_lots'] > 0)
                                <span class="badge completed">{{ $data['full_lots'] }}</span>
                            @else
                                <span class="muted">—</span>
                            @endif
                        </td>
                        <td>
                            @if (isset($agentTemplatesByType[$typeKey]))
                                <a class="button button--ghost tw:text-xs tw:rounded-full tw:px-3 tw:py-1.5" href="{{ route('portal.agent.mailmerge.template.download', $typeKey) }}">
                                    📄 {{ \Illuminate\Support\Str::limit($agentTemplatesByType[$typeKey]->original_name, 22) }}
                                </a>
                            @else
                                <span class="muted">Aucun modèle</span>
                            @endif
                        </td>
                        <td>
                            <form action="{{ route('portal.agent.mailmerge.template.upload', $typeKey) }}" method="post" enctype="multipart/form-data" class="actions tw:mb-2">
                                @csrf
                                <input type="file" name="template" accept=".doc,.docx" required class="tw:text-xs">
                                <button class="button button--primary tw:text-xs tw:rounded-full tw:px-3 tw:py-1.5" type="submit">Téléverser</button>
                            </form>
                            <div class="actions">
                                @if ($data['full_lots'] > 0)
                                    @for ($lot = 1; $lot <= $data['full_lots']; $lot++)
                                        <a class="button button--accent tw:text-xs tw:rounded-full tw:px-3 tw:py-1.5" href="{{ route('portal.agent.mailmerge.export', ['requestType' => $typeKey, 'lot' => $lot]) }}">Lot {{ $lot }}</a>
                                    @endfor
                                @else
                                    <span class="muted tw:text-xs">Pas de lot disponible</span>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

{{-- ── Profil agent ────────────────────── --}}
<div class="tab-pane" data-pane="profil">
    <div class="sec-header"><h2>Mon profil agent</h2></div>
    <div class="grid grid--2 tw:grid-cols-1 lg:tw:grid-cols-2 tw:gap-5 tw:items-start">
        <div class="panel">
            <span class="eyebrow">Informations</span>
            <h3>Compte agent</h3>
            <ul class="list">
                <li><strong>Identifiant</strong><br><span class="muted">{{ $currentUser->name ?: '—' }}</span></li>
                <li><strong>Email</strong><br><span class="muted">{{ $currentUser->email }}</span></li>
                <li><strong>Téléphone</strong><br><span class="muted">{{ $currentUser->phone ?: 'Non renseigné' }}</span></li>
                <li><strong>Dernière connexion</strong><br><span class="muted">{{ optional($currentUser->last_login_at)->format('d/m/Y H:i') ?: 'Première connexion' }}</span></li>
            </ul>
        </div>
        <div class="panel">
            <span class="eyebrow">Modifier</span>
            <h3>Mettre à jour mon profil</h3>
            <form action="{{ route('portal.agent.profile.save') }}" method="post">
                @csrf
                <div class="form-grid">
                    <div class="field">
                        <label>Prénom</label>
                        <input name="first_name" type="text" value="{{ old('first_name', $currentUser->first_name) }}">
                    </div>
                    <div class="field">
                        <label>Nom</label>
                        <input name="last_name" type="text" value="{{ old('last_name', $currentUser->last_name) }}">
                    </div>
                    <div class="field">
                        <label>Email</label>
                        <input name="email" type="email" value="{{ old('email', $currentUser->email) }}" required>
                    </div>
                    <div class="field">
                        <label>Téléphone</label>
                        <input name="phone" type="text" value="{{ old('phone', $currentUser->phone) }}">
                    </div>
                    <div class="field field--full">
                        <label>Adresse</label>
                        <textarea name="address">{{ old('address', $currentUser->address) }}</textarea>
                    </div>
                    <div class="field">
                        <label>Mot de passe actuel</label>
                        <input name="current_password" type="password">
                    </div>
                    <div class="field">
                        <label>Nouveau mot de passe</label>
                        <input name="password" type="password">
                    </div>
                    <div class="field">
                        <label>Confirmer</label>
                        <input name="password_confirmation" type="password">
                    </div>
                </div>
                <button class="button button--primary" type="submit">Enregistrer</button>
            </form>
        </div>
    </div>
</div>

{{-- ══════════════════════════════ ADMIN ════════════════════════════════ --}}
@else

<nav class="tab-nav">
    <button class="tab-btn tw:rounded-full tw:px-4 tw:py-2 tw:text-sm tw:font-medium" data-tab="accueil">📊 Vue d'ensemble</button>
    <button class="tab-btn tw:rounded-full tw:px-4 tw:py-2 tw:text-sm tw:font-medium" data-tab="utilisateurs">👥 Utilisateurs</button>
    <button class="tab-btn tw:rounded-full tw:px-4 tw:py-2 tw:text-sm tw:font-medium" data-tab="messages-admin">
        ✉ Messages
        @if ($adminReceivedMessages->count() > 0)<span class="tab-badge">{{ $adminReceivedMessages->count() }}</span>@endif
    </button>
    <button class="tab-btn tw:rounded-full tw:px-4 tw:py-2 tw:text-sm tw:font-medium" data-tab="dossiers-admin">📁 Derniers dossiers</button>
</nav>

{{-- ── Vue d'ensemble admin ────────────── --}}
<div class="tab-pane" data-pane="accueil">
    <div class="kpi-row tw:grid tw:grid-cols-2 lg:tw:grid-cols-3 tw:gap-3">
        <div class="kpi kpi--brand tw:bg-white tw:shadow-sm tw:rounded-lg">
            <span class="kpi__icon">👥</span>
            <span class="kpi__value tw:text-xl md:tw:text-2xl tw:font-bold">{{ $userCounts['total'] }}</span>
            <span class="kpi__label tw:text-xs tw:text-gray-500 tw:font-medium">Utilisateurs</span>
        </div>
        <div class="kpi kpi--brand tw:bg-white tw:shadow-sm tw:rounded-lg">
            <span class="kpi__icon">🗂</span>
            <span class="kpi__value tw:text-xl md:tw:text-2xl tw:font-bold">{{ $userCounts['agents'] }}</span>
            <span class="kpi__label tw:text-xs tw:text-gray-500 tw:font-medium">Agents</span>
        </div>
        <div class="kpi kpi--accent tw:bg-white tw:shadow-sm tw:rounded-lg">
            <span class="kpi__icon">📋</span>
            <span class="kpi__value tw:text-xl md:tw:text-2xl tw:font-bold">{{ $demandeCounts['total'] }}</span>
            <span class="kpi__label tw:text-xs tw:text-gray-500 tw:font-medium">Dossiers total</span>
        </div>
        <div class="kpi kpi--warn tw:bg-white tw:shadow-sm tw:rounded-lg">
            <span class="kpi__icon">⏳</span>
            <span class="kpi__value tw:text-xl md:tw:text-2xl tw:font-bold">{{ $demandeCounts['pending'] }}</span>
            <span class="kpi__label tw:text-xs tw:text-gray-500 tw:font-medium">En attente</span>
        </div>
        <div class="kpi kpi--success tw:bg-white tw:shadow-sm tw:rounded-lg">
            <span class="kpi__icon">✅</span>
            <span class="kpi__value tw:text-xl md:tw:text-2xl tw:font-bold">{{ $demandeCounts['completed'] }}</span>
            <span class="kpi__label tw:text-xs tw:text-gray-500 tw:font-medium">Finalisés</span>
        </div>
        <div class="kpi kpi--danger tw:bg-white tw:shadow-sm tw:rounded-lg">
            <span class="kpi__icon">✗</span>
            <span class="kpi__value tw:text-xl md:tw:text-2xl tw:font-bold">{{ $demandeCounts['rejected'] }}</span>
            <span class="kpi__label tw:text-xs tw:text-gray-500 tw:font-medium">Rejetés</span>
        </div>
    </div>

    <div class="sec-header tw:mb-4"><h2>Accès rapide</h2></div>
    <div class="quick-grid tw:grid tw:grid-cols-1 sm:tw:grid-cols-2 lg:tw:grid-cols-3 tw:gap-3">
        <a class="quick-card tw:rounded-2xl tw:border tw:border-emerald-200/60 tw:bg-white/95 tw:p-4" href="{{ route('portal.admin.agents') }}">
            <span class="quick-card__icon">👷</span>
            <span class="quick-card__label">Agents</span>
            <span class="quick-card__desc">Gérer et créer des agents</span>
        </a>
        <a class="quick-card tw:rounded-2xl tw:border tw:border-emerald-200/60 tw:bg-white/95 tw:p-4" href="{{ route('portal.admin.demandes') }}">
            <span class="quick-card__icon">📋</span>
            <span class="quick-card__label">Demandes</span>
            <span class="quick-card__desc">Tous les dossiers citoyens</span>
        </a>
        <a class="quick-card tw:rounded-2xl tw:border tw:border-emerald-200/60 tw:bg-white/95 tw:p-4" href="{{ route('portal.admin.citoyens') }}">
            <span class="quick-card__icon">👤</span>
            <span class="quick-card__label">Citoyens</span>
            <span class="quick-card__desc">Liste et export PDF</span>
        </a>
        <a class="quick-card tw:rounded-2xl tw:border tw:border-emerald-200/60 tw:bg-white/95 tw:p-4" href="{{ route('portal.admin.messages') }}">
            <span class="quick-card__icon">✉</span>
            <span class="quick-card__label">Messages</span>
            <span class="quick-card__desc">Tous les échanges du portail</span>
        </a>
        <a class="quick-card tw:rounded-2xl tw:border tw:border-emerald-200/60 tw:bg-white/95 tw:p-4" href="{{ route('portal.admin.stats') }}">
            <span class="quick-card__icon">📈</span>
            <span class="quick-card__label">Statistiques</span>
            <span class="quick-card__desc">Tableaux de bord analytiques</span>
        </a>
        <a class="quick-card tw:rounded-2xl tw:border tw:border-emerald-200/60 tw:bg-white/95 tw:p-4" href="{{ route('portal.admin.settings') }}">
            <span class="quick-card__icon">⚙</span>
            <span class="quick-card__label">Paramètres</span>
            <span class="quick-card__desc">Configuration du portail</span>
        </a>
        <a class="quick-card tw:rounded-2xl tw:border tw:border-emerald-200/60 tw:bg-white/95 tw:p-4" href="{{ route('portal.admin.profile') }}">
            <span class="quick-card__icon">🔑</span>
            <span class="quick-card__label">Mon profil</span>
            <span class="quick-card__desc">Mot de passe et informations</span>
        </a>
    </div>

    <div class="sec-header">
        <h2>Activité récente</h2>
    </div>
    <div class="panel">
        @if ($recentActivity->isEmpty())
            <p class="muted">Aucune activité enregistrée.</p>
        @else
            <ul class="list">
                @foreach ($recentActivity->take(8) as $activity)
                    <li>
                        <div class="split">
                            <span>
                                <strong>{{ $activity->action }}</strong>
                                @if ($activity->user) <span class="muted">par {{ $activity->user->name }}</span>@endif
                            </span>
                            <span class="muted tw:text-xs tw:whitespace-nowrap">{{ optional($activity->created_at)->format('d/m H:i') }}</span>
                        </div>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
</div>

{{-- ── Utilisateurs admin ──────────────── --}}
<div class="tab-pane" data-pane="utilisateurs">
    <div class="grid grid--2 tw:grid-cols-1 lg:tw:grid-cols-2 tw:gap-5 tw:items-start">
        <div class="panel tw:rounded-2xl tw:border tw:border-emerald-200/60 tw:bg-white/95">
            <span class="eyebrow">Créer un compte</span>
            <h3>Nouvel utilisateur</h3>
            <form action="{{ route('portal.admin.users.store') }}" method="post">
                @csrf
                <div class="form-grid tw:gap-4">
                    <div class="field">
                        <label>Prénom</label>
                        <input name="first_name" type="text" class="tw:rounded-xl tw:border-emerald-200 tw:bg-white tw:px-3 tw:py-2.5 focus:tw:ring-2 focus:tw:ring-emerald-400/40">
                    </div>
                    <div class="field">
                        <label>Nom</label>
                        <input name="last_name" type="text" class="tw:rounded-xl tw:border-emerald-200 tw:bg-white tw:px-3 tw:py-2.5 focus:tw:ring-2 focus:tw:ring-emerald-400/40">
                    </div>
                    <div class="field">
                        <label>Email</label>
                        <input name="email" type="email" required class="tw:rounded-xl tw:border-emerald-200 tw:bg-white tw:px-3 tw:py-2.5 focus:tw:ring-2 focus:tw:ring-emerald-400/40">
                    </div>
                    <div class="field">
                        <label>Rôle</label>
                        <select name="role" required class="tw:rounded-xl tw:border-emerald-200 tw:bg-white tw:px-3 tw:py-2.5 focus:tw:ring-2 focus:tw:ring-emerald-400/40">
                            <option value="citoyen">Citoyen</option>
                            <option value="agent">Agent</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                    <div class="field field--full">
                        <label>Mot de passe initial</label>
                        <input name="password" type="password" required class="tw:rounded-xl tw:border-emerald-200 tw:bg-white tw:px-3 tw:py-2.5 focus:tw:ring-2 focus:tw:ring-emerald-400/40">
                    </div>
                </div>
                <button class="button button--primary tw:rounded-full tw:px-5 tw:py-2.5" type="submit">Créer le compte</button>
            </form>
        </div>

        <div class="table-card tw:rounded-2xl tw:border tw:border-emerald-200/60 tw:bg-white/95 tw:overflow-auto">
            <div class="sec-header"><h3>Comptes récents</h3></div>
            <table>
                <thead>
                    <tr>
                        <th>Utilisateur</th>
                        <th>Rôle</th>
                        <th>État</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($recentUsers as $u)
                        <tr>
                            <td>
                                <strong>{{ $u->name }}</strong><br>
                                <span class="muted tw:text-xs">{{ $u->email }}</span>
                            </td>
                            <td>
                                <form action="{{ route('portal.admin.users.role', $u) }}" method="post" class="actions tw:gap-1.5">
                                    @csrf
                                    <select name="role" class="tw:rounded-lg tw:border-emerald-200 tw:bg-white tw:px-2.5 tw:py-1.5 tw:text-xs">
                                        <option value="citoyen" @selected($u->role === 'citoyen')>Citoyen</option>
                                        <option value="agent"   @selected($u->role === 'agent')>Agent</option>
                                        <option value="admin"   @selected($u->role === 'admin')>Admin</option>
                                    </select>
                                    <button class="button button--ghost tw:rounded-full tw:px-3 tw:py-1.5 tw:text-xs" type="submit">OK</button>
                                </form>
                            </td>
                            <td>
                                <span class="badge {{ $u->is_active ? 'completed' : 'rejected' }}">{{ $u->is_active ? 'Actif' : 'Inactif' }}</span>
                            </td>
                            <td>
                                <form action="{{ route('portal.admin.users.toggle', $u) }}" method="post">
                                    @csrf
                                    <button class="button button--ghost tw:rounded-full tw:px-3 tw:py-1.5 tw:text-xs" type="submit">
                                        {{ $u->is_active ? 'Désactiver' : 'Activer' }}
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- ── Messages admin ──────────────────── --}}
<div class="tab-pane" data-pane="messages-admin">
    <div class="sec-header"><h2>Messagerie</h2></div>
    <div class="panel tw:rounded-2xl tw:border tw:border-emerald-200/60 tw:bg-white/95 tw:mb-4">
        <h3 class="tw:mb-3">Nouveau message</h3>
        <form action="{{ route('portal.messages.store') }}" method="post">
            @csrf
            <div class="form-grid tw:gap-4">
                <div class="field">
                    <label>Destinataire</label>
                    <select name="receiver_id" required @disabled($adminMessageUsers->isEmpty()) class="tw:rounded-xl tw:border-emerald-200 tw:bg-white tw:px-3 tw:py-2.5 focus:tw:ring-2 focus:tw:ring-emerald-400/40">
                        <option value="">Choisir un utilisateur</option>
                        @foreach ($adminMessageUsers as $u)
                            <option value="{{ $u->id }}" @selected(old('receiver_id') == $u->id)>
                                {{ trim(($u->first_name ?? '') . ' ' . ($u->last_name ?? '')) ?: $u->name }}
                                ({{ ucfirst($u->role) }})
                            </option>
                        @endforeach
                    </select>
                    @error('receiver_id')<small class="tw:text-xs tw:text-red-600">{{ $message }}</small>@enderror
                </div>
                <div class="field field--full">
                    <label>Message</label>
                    <textarea name="body" rows="4" required @disabled($adminMessageUsers->isEmpty()) class="tw:rounded-xl tw:border-emerald-200 tw:bg-white tw:px-3 tw:py-2.5 focus:tw:ring-2 focus:tw:ring-emerald-400/40">{{ old('body') }}</textarea>
                    @error('body')<small class="tw:text-xs tw:text-red-600">{{ $message }}</small>@enderror
                </div>
            </div>
            @if ($adminMessageUsers->isEmpty())
                <p class="muted tw:m-0">Aucun utilisateur actif disponible.</p>
            @else
                <button class="button button--primary tw:rounded-full tw:px-5 tw:py-2.5" type="submit">Envoyer le message</button>
            @endif
        </form>
    </div>
    <div class="msg-switcher">
        <button class="msg-tab-btn active tw:text-sm tw:font-medium" data-msg-tab="recus" data-msg-group="adm">
            📥 Reçus <span class="msg-count">{{ $adminReceivedMessages->count() }}</span>
        </button>
        <button class="msg-tab-btn tw:text-sm tw:font-medium" data-msg-tab="envoyes" data-msg-group="adm">
            📤 Envoyés <span class="msg-count">{{ $adminSentMessages->count() }}</span>
        </button>
    </div>
    <div class="msg-pane active" data-msg-pane="recus" data-msg-group="adm">
        @if ($adminReceivedMessages->isEmpty())
            <p class="muted">Aucun message reçu.</p>
        @else
            <div class="msg-list">
                @foreach ($adminReceivedMessages as $msg)
                    <div class="msg-card tw:rounded-2xl tw:border tw:border-emerald-200/60 tw:bg-white/95 tw:p-4">
                        <div class="msg-card__header">
                            <span class="msg-card__from">{{ trim(($msg->sender->first_name ?? '') . ' ' . ($msg->sender->last_name ?? '')) ?: ($msg->sender->name ?? 'Utilisateur') }}<span class="muted tw:text-xs tw:ml-1.5">({{ ucfirst($msg->sender->role ?? '') }})</span></span>
                            <span class="msg-card__time">{{ optional($msg->created_at)->format('d/m/Y H:i') }}</span>
                        </div>
                        @if ($msg->demande)<span class="msg-card__ref">📁 Dossier {{ $msg->demande->reference }}</span>@endif
                        <p class="msg-card__body tw:text-slate-800">{{ $msg->body }}</p>
                        @if ($msg->demande)
                            <a class="button button--ghost tw:rounded-full tw:px-4 tw:py-2 tw:w-fit tw:text-sm" href="{{ route('portal.demandes.show', $msg->demande) }}">Ouvrir le dossier</a>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>
    <div class="msg-pane" data-msg-pane="envoyes" data-msg-group="adm">
        @if ($adminSentMessages->isEmpty())
            <p class="muted">Aucun message envoyé.</p>
        @else
            <div class="msg-list">
                @foreach ($adminSentMessages as $msg)
                    <div class="msg-card tw:rounded-2xl tw:border tw:border-emerald-200/60 tw:bg-white/95 tw:p-4">
                        <div class="msg-card__header">
                            <span class="msg-card__from">→ {{ trim(($msg->receiver->first_name ?? '') . ' ' . ($msg->receiver->last_name ?? '')) ?: ($msg->receiver->name ?? 'Utilisateur') }}<span class="muted tw:text-xs tw:ml-1.5">({{ ucfirst($msg->receiver->role ?? '') }})</span></span>
                            <span class="msg-card__time">{{ optional($msg->created_at)->format('d/m/Y H:i') }}</span>
                        </div>
                        @if ($msg->demande)<span class="msg-card__ref">📁 Dossier {{ $msg->demande->reference }}</span>@endif
                        <p class="msg-card__body tw:text-slate-800">{{ $msg->body }}</p>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

{{-- ── Derniers dossiers admin ─────────── --}}
<div class="tab-pane" data-pane="dossiers-admin">
    <div class="sec-header tw:flex tw:flex-wrap tw:items-center tw:justify-between tw:gap-3">
        <h2>Derniers dossiers</h2>
        <a class="button button--ghost tw:rounded-full tw:px-4 tw:py-2" href="{{ route('portal.admin.demandes') }}">Voir tous →</a>
    </div>
    <div class="table-card tw:rounded-2xl tw:border tw:border-emerald-200/60 tw:bg-white/95">
        <table>
            <thead>
                <tr>
                    <th>Référence</th>
                    <th>Usager</th>
                    <th>Agent</th>
                    <th>Statut</th>
                    <th>Date</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($recentDemandes as $demande)
                    <tr>
                        <td><strong>{{ $demande->reference }}</strong></td>
                        <td>{{ trim(($demande->citoyen->first_name ?? '') . ' ' . ($demande->citoyen->last_name ?? '')) ?: ($demande->citoyen->name ?? 'Usager') }}</td>
                        <td>{{ $demande->agent ? (trim(($demande->agent->first_name ?? '') . ' ' . ($demande->agent->last_name ?? '')) ?: $demande->agent->name) : '—' }}</td>
                        <td><span class="badge {{ $statusClassMap[$demande->status] ?? 'pending' }}">{{ $statusLabels[$demande->status] ?? $demande->status }}</span></td>
                        <td class="muted tw:text-xs">{{ optional($demande->created_at)->format('d/m/Y') }}</td>
                        <td><a class="button button--ghost tw:rounded-full tw:px-3 tw:py-1.5 tw:text-xs" href="{{ route('portal.demandes.show', $demande) }}">Voir</a></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@endif

<script>
(function () {
    'use strict';

    var STORAGE_KEY = 'portal_tab_' + '{{ $role }}';
    var activeTabClasses = ['tw:bg-amber-500', 'tw:text-white', 'tw:border-amber-600', 'tw:shadow-sm'];
    var inactiveTabClasses = ['tw:bg-transparent', 'tw:text-slate-600'];

    // ── Onglets principaux ───────────────────────────────────────────
    var tabBtns  = document.querySelectorAll('.tab-btn[data-tab]');
    var tabPanes = document.querySelectorAll('.tab-pane[data-pane]');

    function activateTab(name) {
        tabBtns.forEach(function (btn) {
            var isActive = btn.dataset.tab === name;
            btn.classList.toggle('active', isActive);
            activeTabClasses.forEach(function (cls) { btn.classList.toggle(cls, isActive); });
            inactiveTabClasses.forEach(function (cls) { btn.classList.toggle(cls, !isActive); });
        });
        tabPanes.forEach(function (pane) {
            pane.classList.toggle('active', pane.dataset.pane === name);
        });
        try { sessionStorage.setItem(STORAGE_KEY, name); } catch (e) {}
    }

    tabBtns.forEach(function (btn) {
        btn.addEventListener('click', function () { activateTab(this.dataset.tab); });
    });

    // Restaurer l'onglet sauvegardé ou activer le premier
    var saved = null;
    try { saved = sessionStorage.getItem(STORAGE_KEY); } catch (e) {}
    var first = tabBtns.length ? tabBtns[0].dataset.tab : null;
    var target = (saved && document.querySelector('.tab-btn[data-tab="' + saved + '"]')) ? saved : first;
    if (target) activateTab(target);

    // data-goto-tab (boutons de raccourci)
    document.addEventListener('click', function (e) {
        var btn = e.target.closest('[data-goto-tab]');
        if (btn) activateTab(btn.dataset.gotoTab);
    });

    // ── Sous-onglets messages ────────────────────────────────────────
    document.querySelectorAll('.msg-tab-btn[data-msg-tab]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var group  = this.dataset.msgGroup;
            var target = this.dataset.msgTab;
            document.querySelectorAll('.msg-tab-btn[data-msg-group="' + group + '"]').forEach(function (b) {
                var isActive = b.dataset.msgTab === target;
                b.classList.toggle('active', isActive);
                activeTabClasses.forEach(function (cls) { b.classList.toggle(cls, isActive); });
                inactiveTabClasses.forEach(function (cls) { b.classList.toggle(cls, !isActive); });
            });
            document.querySelectorAll('.msg-pane[data-msg-group="' + group + '"]').forEach(function (p) {
                p.classList.toggle('active', p.dataset.msgPane === target);
            });
        });
    });
})();
</script>
</section>
@endsection
