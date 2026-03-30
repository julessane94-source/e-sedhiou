@extends('portal.layout')

@section('content')
    <section class="auth-grid hero tw:gap-6 lg:tw:gap-8">
        <div class="auth-stage tw:space-y-5">
            <div class="hero__panel auth-showcase tw:rounded-3xl tw:border tw:border-emerald-200/60 tw:bg-gradient-to-b tw:from-white tw:to-emerald-50/80 tw:shadow-xl tw:p-6 lg:tw:p-8">
                <h1 class="tw:text-3xl md:tw:text-4xl tw:font-extrabold tw:tracking-tight">{{ $pageTitle }}</h1>
                <p class="tw:mt-3 tw:text-lg tw:text-gray-700">{{ $pageDescription }}</p>
                
                <div class="tw:mt-6 tw:space-y-4">
                    <div class="tw:flex tw:items-start tw:gap-3">
                        <i class="bi bi-check-circle-fill tw:text-emerald-500 tw:text-xl tw:flex-shrink-0 tw:mt-0.5"></i>
                        <div>
                            <h3 class="tw:font-semibold tw:text-emerald-900">Enregistrement simple</h3>
                            <p class="tw:text-sm tw:text-gray-600">Remplissez le formulaire avec vos informations civiles essentielles.</p>
                        </div>
                    </div>
                    <div class="tw:flex tw:items-start tw:gap-3">
                        <i class="bi bi-check-circle-fill tw:text-emerald-500 tw:text-xl tw:flex-shrink-0 tw:mt-0.5"></i>
                        <div>
                            <h3 class="tw:font-semibold tw:text-emerald-900">Confirmation par email</h3>
                            <p class="tw:text-sm tw:text-gray-600">Vous recevrez une confirmation et pourrez accéder aux services de la mairie.</p>
                        </div>
                    </div>
                    <div class="tw:flex tw:items-start tw:gap-3">
                        <i class="bi bi-check-circle-fill tw:text-emerald-500 tw:text-xl tw:flex-shrink-0 tw:mt-0.5"></i>
                        <div>
                            <h3 class="tw:font-semibold tw:text-emerald-900">Données sécurisées</h3>
                            <p class="tw:text-sm tw:text-gray-600">Vos informations sont protégées et utilisées uniquement par la mairie.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="auth-forms section tw:space-y-5">
            <div class="panel auth-card tw:rounded-3xl tw:border tw:border-emerald-200/60 tw:bg-gradient-to-b tw:from-white tw:to-emerald-50/70 tw:p-6 lg:tw:p-7">
                <span class="eyebrow"><i class="bi bi-person-check me-1"></i>Formulaire de Recensement</span>
                <h2>Me faire connaître de la mairie</h2>
                <p class="auth-card__intro">Enregistrez vos informations personnelles pour être reconnu(e) par la mairie.</p>

                <form action="{{ route('portal.citoyen.recensement.store') }}" method="post" enctype="multipart/form-data" class="tw:space-y-1">
                    @csrf
                    
                    {{-- SECTION 1: INFORMATIONS CIVILES --}}
                    <div class="tw:mb-6 tw:pb-6 tw:border-b tw:border-emerald-200/40">
                        <h3 class="tw:text-lg tw:font-bold tw:text-emerald-900 tw:mb-4"><i class="bi bi-person-card me-2"></i>1. Informations Civiles</h3>
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="recens-first-name" class="form-label"><i class="bi bi-person me-1"></i>Prénom *</label>
                                <input id="recens-first-name" name="first_name" type="text" value="{{ old('first_name') }}" required class="form-control tw:rounded-xl tw:border-emerald-200 tw:bg-white tw:px-3 tw:py-2.5 focus:tw:ring-2 focus:tw:ring-emerald-400/40">
                                @error('first_name')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="recens-last-name" class="form-label"><i class="bi bi-person me-1"></i>Nom *</label>
                                <input id="recens-last-name" name="last_name" type="text" value="{{ old('last_name') }}" required class="form-control tw:rounded-xl tw:border-emerald-200 tw:bg-white tw:px-3 tw:py-2.5 focus:tw:ring-2 focus:tw:ring-emerald-400/40">
                                @error('last_name')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>

                        <div class="row g-3 tw:mt-3">
                            <div class="col-12">
                                <label for="recens-email" class="form-label"><i class="bi bi-envelope me-1"></i>Email *</label>
                                <input id="recens-email" name="email" type="email" value="{{ old('email') }}" required class="form-control tw:rounded-xl tw:border-emerald-200 tw:bg-white tw:px-3 tw:py-2.5 focus:tw:ring-2 focus:tw:ring-emerald-400/40">
                                @error('email')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="recens-phone" class="form-label"><i class="bi bi-telephone me-1"></i>Téléphone *</label>
                                <input id="recens-phone" name="phone" type="tel" value="{{ old('phone') }}" required class="form-control tw:rounded-xl tw:border-emerald-200 tw:bg-white tw:px-3 tw:py-2.5 focus:tw:ring-2 focus:tw:ring-emerald-400/40">
                                @error('phone')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="recens-register-number" class="form-label"><i class="bi bi-card-text me-1"></i>Numéro de registre *</label>
                                <input id="recens-register-number" name="register_number" type="text" value="{{ old('register_number') }}" required class="form-control tw:rounded-xl tw:border-emerald-200 tw:bg-white tw:px-3 tw:py-2.5 focus:tw:ring-2 focus:tw:ring-emerald-400/40">
                                @error('register_number')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>

                        <div class="row g-3 tw:mt-3">
                            <div class="col-md-6">
                                <label for="recens-birth-date" class="form-label"><i class="bi bi-calendar me-1"></i>Date de naissance *</label>
                                <input id="recens-birth-date" name="birth_date" type="date" value="{{ old('birth_date') }}" required class="form-control tw:rounded-xl tw:border-emerald-200 tw:bg-white tw:px-3 tw:py-2.5 focus:tw:ring-2 focus:tw:ring-emerald-400/40">
                                @error('birth_date')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="recens-birth-place" class="form-label"><i class="bi bi-geo-alt me-1"></i>Lieu de naissance *</label>
                                <input id="recens-birth-place" name="birth_place" type="text" value="{{ old('birth_place') }}" required class="form-control tw:rounded-xl tw:border-emerald-200 tw:bg-white tw:px-3 tw:py-2.5 focus:tw:ring-2 focus:tw:ring-emerald-400/40">
                                @error('birth_place')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>

                        <div class="row g-3 tw:mt-3">
                            <div class="col-12">
                                <label for="recens-address" class="form-label"><i class="bi bi-house-door me-1"></i>Adresse *</label>
                                <textarea id="recens-address" name="address" required class="form-control tw:min-h-[90px] tw:rounded-xl tw:border-emerald-200 tw:bg-white tw:px-3 tw:py-2.5 focus:tw:ring-2 focus:tw:ring-emerald-400/40" placeholder="Votre adresse complète">{{ old('address') }}</textarea>
                                @error('address')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- SECTION 2: INFORMATIONS PROFESSIONNELLES --}}
                    <div class="tw:mb-6 tw:pb-6 tw:border-b tw:border-emerald-200/40">
                        <h3 class="tw:text-lg tw:font-bold tw:text-emerald-900 tw:mb-4"><i class="bi bi-briefcase me-2"></i>2. Informations Professionnelles</h3>
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="recens-profession-sector" class="form-label"><i class="bi bi-diagram-3 me-1"></i>Secteur d'activité *</label>
                                <select id="recens-profession-sector" name="profession_sector" required class="form-select tw:rounded-xl tw:border-emerald-200 tw:bg-white tw:px-3 tw:py-2.5 focus:tw:ring-2 focus:tw:ring-emerald-400/40">
                                    <option value="">-- Sélectionnez un secteur --</option>
                                    <option value="Agriculture" {{ old('profession_sector') === 'Agriculture' ? 'selected' : '' }}>🌾 Agriculture</option>
                                    <option value="Élevage" {{ old('profession_sector') === 'Élevage' ? 'selected' : '' }}>🐄 Élevage</option>
                                    <option value="Informatique/Digital" {{ old('profession_sector') === 'Informatique/Digital' ? 'selected' : '' }}>💻 Informatique/Digital</option>
                                    <option value="BTP/Construction" {{ old('profession_sector') === 'BTP/Construction' ? 'selected' : '' }}>🏗️ BTP/Construction</option>
                                    <option value="Commerce" {{ old('profession_sector') === 'Commerce' ? 'selected' : '' }}>🏪 Commerce</option>
                                    <option value="Transport" {{ old('profession_sector') === 'Transport' ? 'selected' : '' }}>🚚 Transport</option>
                                    <option value="Artisanat" {{ old('profession_sector') === 'Artisanat' ? 'selected' : '' }}>🔨 Artisanat</option>
                                    <option value="Santé" {{ old('profession_sector') === 'Santé' ? 'selected' : '' }}>⚕️ Santé</option>
                                    <option value="Éducation" {{ old('profession_sector') === 'Éducation' ? 'selected' : '' }}>📚 Éducation</option>
                                    <option value="Autre" {{ old('profession_sector') === 'Autre' ? 'selected' : '' }}>❓ Autre</option>
                                </select>
                                @error('profession_sector')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="recens-profession-title" class="form-label"><i class="bi bi-card-text me-1"></i>Métier précis *</label>
                                <input id="recens-profession-title" name="profession_title" type="text" value="{{ old('profession_title') }}" required placeholder="Ex: Développeur Laravel, Menuisier, etc." class="form-control tw:rounded-xl tw:border-emerald-200 tw:bg-white tw:px-3 tw:py-2.5 focus:tw:ring-2 focus:tw:ring-emerald-400/40">
                                @error('profession_title')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>

                        <div class="row g-3 tw:mt-3">
                            <div class="col-md-6">
                                <label for="recens-education-level" class="form-label"><i class="bi bi-mortarboard me-1"></i>Niveau d'études / Diplôme *</label>
                                <select id="recens-education-level" name="education_level" required class="form-select tw:rounded-xl tw:border-emerald-200 tw:bg-white tw:px-3 tw:py-2.5 focus:tw:ring-2 focus:tw:ring-emerald-400/40">
                                    <option value="">-- Sélectionnez un niveau --</option>
                                    <option value="Aucun" {{ old('education_level') === 'Aucun' ? 'selected' : '' }}>Aucun</option>
                                    <option value="CFEE" {{ old('education_level') === 'CFEE' ? 'selected' : '' }}>CFEE</option>
                                    <option value="BFEM" {{ old('education_level') === 'BFEM' ? 'selected' : '' }}>BFEM</option>
                                    <option value="BAC" {{ old('education_level') === 'BAC' ? 'selected' : '' }}>BAC</option>
                                    <option value="Diplôme technique (CAP/BT)" {{ old('education_level') === 'Diplôme technique (CAP/BT)' ? 'selected' : '' }}>Diplôme technique (CAP/BT)</option>
                                    <option value="BTS/Licence" {{ old('education_level') === 'BTS/Licence' ? 'selected' : '' }}>BTS/Licence</option>
                                    <option value="Master" {{ old('education_level') === 'Master' ? 'selected' : '' }}>Master</option>
                                    <option value="Doctorat" {{ old('education_level') === 'Doctorat' ? 'selected' : '' }}>Doctorat</option>
                                </select>
                                @error('education_level')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="recens-years-experience" class="form-label"><i class="bi bi-clock me-1"></i>Années d'expérience *</label>
                                <input id="recens-years-experience" name="years_experience" type="number" min="0" max="100" value="{{ old('years_experience') }}" required placeholder="Ex: 5" class="form-control tw:rounded-xl tw:border-emerald-200 tw:bg-white tw:px-3 tw:py-2.5 focus:tw:ring-2 focus:tw:ring-emerald-400/40">
                                @error('years_experience')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- SECTION 3: COMPÉTENCES & DISPONIBILITÉ --}}
                    <div class="tw:mb-6 tw:pb-6 tw:border-b tw:border-emerald-200/40">
                        <h3 class="tw:text-lg tw:font-bold tw:text-emerald-900 tw:mb-4"><i class="bi bi-star me-2"></i>3. Compétences & Disponibilité</h3>
                        
                        <div class="row g-3">
                            <div class="col-12">
                                <label for="recens-skills" class="form-label"><i class="bi bi-tag me-1"></i>Compétences clés *</label>
                                <textarea id="recens-skills" name="skills" required placeholder="Séparez par des virgules: Soudure à l'arc, Gestion d'équipe, Maintenance informatique, etc." class="form-control tw:min-h-[80px] tw:rounded-xl tw:border-emerald-200 tw:bg-white tw:px-3 tw:py-2.5 focus:tw:ring-2 focus:tw:ring-emerald-400/40">{{ old('skills') }}</textarea>
                                <small class="text-muted">Exemple: Soudure à l'arc, Gestion d'équipe, Maintenance informatique</small>
                                @error('skills')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>

                        <div class="row g-3 tw:mt-3">
                            <div class="col-md-6">
                                <label for="recens-current-status" class="form-label"><i class="bi bi-briefcase-fill me-1"></i>Statut actuel *</label>
                                <select id="recens-current-status" name="current_status" required class="form-select tw:rounded-xl tw:border-emerald-200 tw:bg-white tw:px-3 tw:py-2.5 focus:tw:ring-2 focus:tw:ring-emerald-400/40">
                                    <option value="">-- Sélectionnez un statut --</option>
                                    <option value="Étudiant" {{ old('current_status') === 'Étudiant' ? 'selected' : '' }}>📖 Étudiant</option>
                                    <option value="En recherche d'emploi" {{ old('current_status') === 'En recherche d\'emploi' ? 'selected' : '' }}>🔍 En recherche d'emploi</option>
                                    <option value="Indépendant/Auto-entrepreneur" {{ old('current_status') === 'Indépendant/Auto-entrepreneur' ? 'selected' : '' }}>💼 Indépendant/Auto-entrepreneur</option>
                                    <option value="Salarié" {{ old('current_status') === 'Salarié' ? 'selected' : '' }}>👔 Salarié</option>
                                </select>
                                @error('current_status')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <div class="tw:mt-7">
                                    <div class="form-check">
                                        <input id="recens-available" type="checkbox" name="available_for_municipality" value="1" {{ old('available_for_municipality') ? 'checked' : '' }} class="form-check-input tw:rounded tw:border-emerald-300 tw:text-emerald-600 focus:tw:ring-emerald-500/50">
                                        <label for="recens-available" class="form-check-label tw:ml-2">
                                            <i class="bi bi-hand-thumbs-up me-1"></i>Je souhaite être contacté(e) pour des projets/missions locales
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- SECTION 4: JUSTIFICATIFS (OPTIONNEL) --}}
                    <div class="tw:mb-6">
                        <h3 class="tw:text-lg tw:font-bold tw:text-emerald-900 tw:mb-4"><i class="bi bi-file-earmark-pdf me-2"></i>4. Justificatifs (Optionnel)</h3>
                        <p class="tw:text-sm tw:text-gray-600 tw:mb-4">Augmentez la crédibilité de votre profil en partageant votre CV et un lien vers votre portfolio.</p>
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="recens-cv-file" class="form-label"><i class="bi bi-file-pdf me-1"></i>Télécharger votre CV (PDF)</label>
                                <input id="recens-cv-file" name="cv_file" type="file" accept=".pdf" class="form-control tw:rounded-xl tw:border-emerald-200 tw:bg-white tw:px-3 tw:py-2.5 focus:tw:ring-2 focus:tw:ring-emerald-400/40">
                                <small class="text-muted">Maximum 5 MB, format PDF uniquement</small>
                                @error('cv_file')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="recens-portfolio-url" class="form-label"><i class="bi bi-link-45deg me-1"></i>Lien Portfolio / Réseaux Pro</label>
                                <input id="recens-portfolio-url" name="portfolio_url" type="url" value="{{ old('portfolio_url') }}" placeholder="https://linkedin.com/in/... ou votre site" class="form-control tw:rounded-xl tw:border-emerald-200 tw:bg-white tw:px-3 tw:py-2.5 focus:tw:ring-2 focus:tw:ring-emerald-400/40">
                                <small class="text-muted">LinkedIn, site personnel, GitHub, etc.</small>
                                @error('portfolio_url')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <button class="btn btn-success w-100 rounded-pill py-2 tw:mt-4 tw:w-full tw:rounded-full tw:py-2.5 tw:font-semibold tw:text-white tw:bg-emerald-600 hover:tw:bg-emerald-700" type="submit">
                        <i class="bi bi-check-circle me-2"></i> Me faire connaître de la mairie
                    </button>

                    <p class="tw:mt-4 tw:text-center tw:text-sm tw:text-gray-600">
                        Vous avez déjà un compte? 
                        <a href="{{ route('portal.auth') }}" class="tw:text-emerald-600 tw:font-semibold hover:tw:underline">Se connecter</a>
                    </p>
                </form>
            </div>
        </div>
    </section>
@endsection
