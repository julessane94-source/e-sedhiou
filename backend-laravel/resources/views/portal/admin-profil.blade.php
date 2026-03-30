@extends('portal.layout')

@section('content')
    @include('portal.components.admin-nav')

    @php
        $adminName = trim(($currentUser->first_name ?? '') . ' ' . ($currentUser->last_name ?? '')) ?: $currentUser->name;
    @endphp

    <section class="hero">
        <div class="hero__panel">
            <span class="eyebrow">Administration</span>
            <div class="tw:overflow-hidden tw:h-12 tw:flex tw:items-center">
                <h1 class="slide-animate">Mon profil</h1>
            </div>
            <p class="muted">Modifiez vos informations personnelles et votre mot de passe.</p>
        </div>

        <div class="panel">
            <span class="eyebrow">Compte</span>
            <h2>{{ $adminName }}</h2>
            <div class="fact-grid">
                <div class="fact-card">
                    <strong>Email</strong>
                    <span>{{ $currentUser->email }}</span>
                </div>
                <div class="fact-card">
                    <strong>Role</strong>
                    <span><span class="badge assigned">Administrateur</span></span>
                </div>
                <div class="fact-card">
                    <strong>Derniere connexion</strong>
                    <span>{{ optional($currentUser->last_login_at)->format('d/m/Y H:i') ?? '—' }}</span>
                </div>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="grid grid--2">
            <div class="panel">
                <div class="panel-header">
                    <div class="panel-header__title">
                        <span class="eyebrow">Modifier</span>
                        <h2>Informations personnelles</h2>
                    </div>
                </div>

                <form action="{{ route('portal.admin.profile.save') }}" method="post">
                    @csrf
                    <div class="field">
                        <label for="p-fn">Prenom</label>
                        <input id="p-fn" type="text" name="first_name"
                               value="{{ old('first_name', $currentUser->first_name) }}">
                    </div>
                    <div class="field">
                        <label for="p-ln">Nom</label>
                        <input id="p-ln" type="text" name="last_name"
                               value="{{ old('last_name', $currentUser->last_name) }}">
                    </div>
                    <div class="field">
                        <label for="p-email">Email <span class="muted">*</span></label>
                        <input id="p-email" type="email" name="email"
                               value="{{ old('email', $currentUser->email) }}" required>
                    </div>
                    <button class="button button--primary tw:mt-3" type="submit">
                        Enregistrer
                    </button>
                </form>
            </div>

            <div class="panel">
                <div class="panel-header">
                    <div class="panel-header__title">
                        <span class="eyebrow">Securite</span>
                        <h2>Changer le mot de passe</h2>
                    </div>
                </div>
                <p class="panel-note">Utilisez un mot de passe long et unique. Cette action conserve la logique de validation existante.</p>

                <form action="{{ route('portal.admin.profile.save') }}" method="post">
                    @csrf
                    <input type="hidden" name="email" value="{{ $currentUser->email }}">
                    <div class="field">
                        <label for="p-cpw">Mot de passe actuel <span class="muted">*</span></label>
                        <input id="p-cpw" type="password" name="current_password" required autocomplete="current-password">
                    </div>
                    <div class="field">
                        <label for="p-npw">Nouveau mot de passe <span class="muted">*</span></label>
                        <input id="p-npw" type="password" name="password" required autocomplete="new-password">
                    </div>
                    <div class="field">
                        <label for="p-cpw2">Confirmer <span class="muted">*</span></label>
                        <input id="p-cpw2" type="password" name="password_confirmation" required autocomplete="new-password">
                    </div>
                    <button class="button button--accent tw:mt-3" type="submit">
                        Changer le mot de passe
                    </button>
                </form>
            </div>
        </div>
    </section>
@endsection
