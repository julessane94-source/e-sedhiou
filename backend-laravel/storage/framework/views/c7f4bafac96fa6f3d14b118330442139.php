

<?php $__env->startSection('content'); ?>
    <section class="auth-grid hero tw:gap-6 lg:tw:gap-8">
        <div class="auth-stage tw:space-y-5">
            <div class="hero__panel auth-showcase tw:rounded-3xl tw:border tw:border-emerald-200/60 tw:bg-gradient-to-b tw:from-white tw:to-emerald-50/80 tw:shadow-xl tw:p-6 lg:tw:p-8">
                <h1 class="tw:text-3xl md:tw:text-4xl tw:font-extrabold tw:tracking-tight"><?php echo e($pageTitle); ?></h1>

                <?php if($maintenanceMode): ?>
                    <div class="notice notice--warning tw:mt-5 tw:rounded-2xl tw:border tw:border-amber-300/50 tw:bg-amber-50/70">
                        <strong>Maintenance active</strong>
                        <p class="muted tw:mt-2 tw:mb-0">L espace citoyen est temporairement suspendu. Les comptes agents et administrateurs peuvent continuer a se connecter.</p>
                    </div>
                <?php endif; ?>

                

                
            </div>

            
        </div>

        <div class="auth-forms section tw:space-y-5">
            <div class="panel auth-card tw:rounded-3xl tw:border tw:border-emerald-200/60 tw:bg-gradient-to-b tw:from-white tw:to-emerald-50/70 tw:p-6 lg:tw:p-7">
                <span class="eyebrow"><i class="bi bi-lock-fill me-1"></i>Connexion</span>
                <h2>Acceder a votre espace</h2>
                <p class="auth-card__intro">Entrez vos identifiants pour ouvrir votre tableau de bord et reprendre vos demarches.</p>

                <form action="<?php echo e(route('portal.login')); ?>" method="post">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="redirect_to" value="<?php echo e($redirectTo); ?>">
                    <div class="mb-3 tw:space-y-1.5">
                        <label for="portal-email" class="form-label"><i class="bi bi-envelope me-1"></i>Email</label>
                        <input id="portal-email" name="email" type="email" value="<?php echo e(old('email')); ?>" required class="form-control tw:rounded-xl tw:border-emerald-200 tw:bg-white tw:px-3 tw:py-2.5 focus:tw:ring-2 focus:tw:ring-emerald-400/40">
                    </div>
                    <div class="mb-4 tw:space-y-1.5">
                        <label for="portal-password" class="form-label"><i class="bi bi-key me-1"></i>Mot de passe</label>
                        <input id="portal-password" name="password" type="password" required class="form-control tw:rounded-xl tw:border-emerald-200 tw:bg-white tw:px-3 tw:py-2.5 focus:tw:ring-2 focus:tw:ring-emerald-400/40">
                    </div>
                    <button class="btn btn-success w-100 rounded-pill py-2 tw:w-full tw:rounded-full tw:py-2.5 tw:font-semibold" type="submit">
                        <i class="bi bi-box-arrow-in-right me-1"></i> Se connecter
                    </button>
                </form>
            </div>

            <div class="panel auth-card tw:rounded-3xl tw:border tw:border-emerald-200/60 tw:bg-gradient-to-b tw:from-white tw:to-emerald-50/70 tw:p-6 lg:tw:p-7">
                <span class="eyebrow"><i class="bi bi-person-plus me-1"></i>Inscription citoyenne</span>
                <h2><?php echo e($registrationOpen ? 'Creer un compte usager' : 'Inscriptions temporairement indisponibles'); ?></h2>
                <p class="auth-card__intro">
                    <?php echo e($registrationOpen
                        ? 'Renseignez votre identite civile pour creer votre compte et demarrer vos demandes en ligne.'
                        : 'Le portail garde l acces en consultation, mais la creation de nouveaux comptes est suspendue pour le moment.'); ?>

                </p>

                <?php if($registrationOpen): ?>
                    <form action="<?php echo e(route('portal.register')); ?>" method="post" class="tw:space-y-1">
                        <?php echo csrf_field(); ?>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="register-first-name" class="form-label"><i class="bi bi-person me-1"></i>Prenom</label>
                                <input id="register-first-name" name="first_name" type="text" value="<?php echo e(old('first_name')); ?>" class="form-control tw:rounded-xl tw:border-emerald-200 tw:bg-white tw:px-3 tw:py-2.5 focus:tw:ring-2 focus:tw:ring-emerald-400/40">
                            </div>
                            <div class="col-md-6">
                                <label for="register-last-name" class="form-label"><i class="bi bi-person me-1"></i>Nom</label>
                                <input id="register-last-name" name="last_name" type="text" value="<?php echo e(old('last_name')); ?>" class="form-control tw:rounded-xl tw:border-emerald-200 tw:bg-white tw:px-3 tw:py-2.5 focus:tw:ring-2 focus:tw:ring-emerald-400/40">
                            </div>
                            <div class="col-12">
                                <label for="register-email" class="form-label"><i class="bi bi-envelope me-1"></i>Email</label>
                                <input id="register-email" name="email" type="email" value="<?php echo e(old('email')); ?>" required class="form-control tw:rounded-xl tw:border-emerald-200 tw:bg-white tw:px-3 tw:py-2.5 focus:tw:ring-2 focus:tw:ring-emerald-400/40">
                            </div>
                            <div class="col-md-6">
                                <label for="register-phone" class="form-label"><i class="bi bi-telephone me-1"></i>Telephone</label>
                                <input id="register-phone" name="phone" type="text" value="<?php echo e(old('phone')); ?>" required class="form-control tw:rounded-xl tw:border-emerald-200 tw:bg-white tw:px-3 tw:py-2.5 focus:tw:ring-2 focus:tw:ring-emerald-400/40">
                            </div>
                            <div class="col-md-6">
                                <label for="register-register-number" class="form-label"><i class="bi bi-card-text me-1"></i>Numero de registre</label>
                                <input id="register-register-number" name="register_number" type="text" value="<?php echo e(old('register_number')); ?>" required class="form-control tw:rounded-xl tw:border-emerald-200 tw:bg-white tw:px-3 tw:py-2.5 focus:tw:ring-2 focus:tw:ring-emerald-400/40">
                            </div>
                            <div class="col-md-6">
                                <label for="register-birth-date" class="form-label"><i class="bi bi-calendar me-1"></i>Date de naissance</label>
                                <input id="register-birth-date" name="birth_date" type="date" value="<?php echo e(old('birth_date')); ?>" required class="form-control tw:rounded-xl tw:border-emerald-200 tw:bg-white tw:px-3 tw:py-2.5 focus:tw:ring-2 focus:tw:ring-emerald-400/40">
                            </div>
                            <div class="col-md-6">
                                <label for="register-birth-place" class="form-label"><i class="bi bi-geo-alt me-1"></i>Lieu de naissance</label>
                                <input id="register-birth-place" name="birth_place" type="text" value="<?php echo e(old('birth_place')); ?>" required class="form-control tw:rounded-xl tw:border-emerald-200 tw:bg-white tw:px-3 tw:py-2.5 focus:tw:ring-2 focus:tw:ring-emerald-400/40">
                            </div>
                            <div class="col-12">
                                <label for="register-address" class="form-label"><i class="bi bi-house-door me-1"></i>Adresse</label>
                                <textarea id="register-address" name="address" required class="form-control tw:min-h-[90px] tw:rounded-xl tw:border-emerald-200 tw:bg-white tw:px-3 tw:py-2.5 focus:tw:ring-2 focus:tw:ring-emerald-400/40"><?php echo e(old('address')); ?></textarea>
                            </div>
                            <div class="col-md-6">
                                <label for="register-password" class="form-label"><i class="bi bi-key me-1"></i>Mot de passe</label>
                                <input id="register-password" name="password" type="password" required class="form-control tw:rounded-xl tw:border-emerald-200 tw:bg-white tw:px-3 tw:py-2.5 focus:tw:ring-2 focus:tw:ring-emerald-400/40">
                            </div>
                            <div class="col-md-6">
                                <label for="register-password-confirmation" class="form-label"><i class="bi bi-key-fill me-1"></i>Confirmation</label>
                                <input id="register-password-confirmation" name="password_confirmation" type="password" required class="form-control tw:rounded-xl tw:border-emerald-200 tw:bg-white tw:px-3 tw:py-2.5 focus:tw:ring-2 focus:tw:ring-emerald-400/40">
                            </div>
                            <div class="col-12 mt-1">
                                <button class="btn btn-success w-100 rounded-pill py-2 tw:w-full tw:rounded-full tw:py-2.5 tw:font-semibold" type="submit">
                                    <i class="bi bi-person-check me-1"></i> Creer mon compte
                                </button>
                            </div>
                        </div>
                    </form>
                <?php else: ?>
                    <div class="notice notice--warning tw:rounded-2xl tw:border tw:border-amber-300/50 tw:bg-amber-50/70 tw:m-0">
                        <strong><?php echo e($maintenanceMode ? 'Portail en maintenance' : 'Inscriptions fermees'); ?></strong>
                        <p class="muted tw:mt-2 tw:mb-0">
                            <?php echo e($maintenanceMode
                                ? 'Les demandes citoyennes et la creation de compte sont momentanement suspendues.'
                                : 'La creation de nouveaux comptes citoyens a ete desactivee depuis l administration.'); ?>

                        </p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('portal.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mairie_wp\backend-laravel\resources\views/portal/auth.blade.php ENDPATH**/ ?>