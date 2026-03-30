@extends('portal.layout')

@section('content')
    @include('portal.components.admin-nav')

    <section class="hero">
        <div class="hero__panel">
            <span class="eyebrow">Administration</span>
            <div class="tw:overflow-hidden tw:h-12 tw:flex tw:items-center">
                <h1 class="slide-animate">Parametres du site</h1>
            </div>
            <p class="muted">Configurez les informations et options du portail.</p>
        </div>

        <div class="panel">
            <span class="eyebrow">Etat actuel</span>
            <h2>Resume de configuration</h2>
            <div class="fact-grid">
                <div class="fact-card">
                    <strong>Inscriptions</strong>
                    <span>{{ ($settings['allow_register'] ?? 1) ? 'Ouvertes aux nouveaux citoyens.' : 'Desactivees pour le moment.' }}</span>
                </div>
                <div class="fact-card">
                    <strong>Maintenance</strong>
                    <span>{{ ($settings['maintenance_mode'] ?? 0) ? 'Mode maintenance actif.' : 'Portail accessible.' }}</span>
                </div>
                <div class="fact-card">
                    <strong>Signature du maire</strong>
                    <span>{{ $hasMayorSignature ? 'Fichier charge et utilisable pour le traitement en ligne.' : 'Aucune signature numerique disponible.' }}</span>
                </div>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="panel tw:max-w-[720px]">
            <div class="panel-header">
                <div class="panel-header__title">
                    <span class="eyebrow">Configuration</span>
                    <h2>Informations generales</h2>
                </div>
            </div>

            <form action="{{ route('portal.admin.settings.save') }}" method="post" enctype="multipart/form-data">
                @csrf

                <div class="form-grid">
                    <div class="field">
                        <label for="site_name">Nom du site <span class="muted">*</span></label>
                        <input id="site_name" type="text" name="site_name"
                               value="{{ old('site_name', $settings['site_name'] ?? '') }}" required>
                    </div>

                    <div class="field">
                        <label for="contact_email">Email de contact <span class="muted">*</span></label>
                        <input id="contact_email" type="email" name="contact_email"
                               value="{{ old('contact_email', $settings['contact_email'] ?? '') }}" required>
                    </div>

                    <div class="field">
                        <label for="contact_phone">Telephone</label>
                        <input id="contact_phone" type="text" name="contact_phone"
                               value="{{ old('contact_phone', $settings['contact_phone'] ?? '') }}"
                               placeholder="+221 33 000 00 00">
                    </div>
                </div>

                <div class="field">
                    <label for="site_description">Description courte</label>
                    <textarea id="site_description" name="site_description" rows="2">{{ old('site_description', $settings['site_description'] ?? '') }}</textarea>
                </div>

                <div class="field">
                    <label for="contact_address">Adresse physique</label>
                    <textarea id="contact_address" name="contact_address" rows="2">{{ old('contact_address', $settings['contact_address'] ?? '') }}</textarea>
                </div>

                <div class="form-grid tw:mt-2.5">
                    <div class="field">
                        <label class="tw:flex tw:items-center tw:gap-2 tw:cursor-pointer">
                            <input type="hidden" name="allow_register" value="0">
                            <input type="checkbox" name="allow_register" value="1"
                                   {{ ($settings['allow_register'] ?? 1) ? 'checked' : '' }}>
                            Autoriser les nouvelles inscriptions
                        </label>
                    </div>
                    <div class="field">
                        <label class="tw:flex tw:items-center tw:gap-2 tw:cursor-pointer">
                            <input type="hidden" name="maintenance_mode" value="0">
                            <input type="checkbox" name="maintenance_mode" value="1"
                                   {{ ($settings['maintenance_mode'] ?? 0) ? 'checked' : '' }}>
                            Mode maintenance (portail inaccessible aux citoyens)
                        </label>
                    </div>
                </div>

                <hr class="divider">

                <span class="eyebrow">Demandes</span>
                <h2 class="tw:mt-1.5">Tarifs par type</h2>
                <div class="form-grid">
                    @foreach ($requestTypes as $typeKey => $typeLabel)
                        <div class="field">
                            <label for="fee-{{ $typeKey }}">{{ $typeLabel }} (FCFA)</label>
                            <input
                                id="fee-{{ $typeKey }}"
                                type="number"
                                min="0"
                                step="100"
                                name="request_fees[{{ $typeKey }}]"
                                value="{{ old('request_fees.' . $typeKey, $requestFees[$typeKey] ?? 0) }}"
                                required
                            >
                        </div>
                    @endforeach
                </div>

                <hr class="divider">

                <span class="eyebrow">Validation en ligne</span>
                <h2 class="tw:mt-1.5">Signature numerique du maire</h2>
                <div class="field">
                    <label for="mayor_signature">Televerser la signature (PNG/JPG)</label>
                    <input id="mayor_signature" type="file" name="mayor_signature" accept=".png,.jpg,.jpeg">
                    @if ($hasMayorSignature)
                        <small class="muted">Signature actuelle: {{ $settings['mayor_signature_name'] ?? 'signature-chargee' }}</small>
                    @else
                        <small class="muted">Aucune signature configuree pour le traitement en ligne.</small>
                    @endif
                </div>

                <button class="button button--primary tw:mt-5" type="submit">
                    Enregistrer les parametres
                </button>
            </form>
        </div>
    </section>
@endsection
