

<?php $__env->startSection('content'); ?>
    <?php echo $__env->make('portal.components.admin-nav', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <?php
        $adminName = trim(($currentUser->first_name ?? '') . ' ' . ($currentUser->last_name ?? '')) ?: $currentUser->name;
    ?>

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
            <h2><?php echo e($adminName); ?></h2>
            <div class="fact-grid">
                <div class="fact-card">
                    <strong>Email</strong>
                    <span><?php echo e($currentUser->email); ?></span>
                </div>
                <div class="fact-card">
                    <strong>Role</strong>
                    <span><span class="badge assigned">Administrateur</span></span>
                </div>
                <div class="fact-card">
                    <strong>Derniere connexion</strong>
                    <span><?php echo e(optional($currentUser->last_login_at)->format('d/m/Y H:i') ?? '—'); ?></span>
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

                <form action="<?php echo e(route('portal.admin.profile.save')); ?>" method="post">
                    <?php echo csrf_field(); ?>
                    <div class="field">
                        <label for="p-fn">Prenom</label>
                        <input id="p-fn" type="text" name="first_name"
                               value="<?php echo e(old('first_name', $currentUser->first_name)); ?>">
                    </div>
                    <div class="field">
                        <label for="p-ln">Nom</label>
                        <input id="p-ln" type="text" name="last_name"
                               value="<?php echo e(old('last_name', $currentUser->last_name)); ?>">
                    </div>
                    <div class="field">
                        <label for="p-email">Email <span class="muted">*</span></label>
                        <input id="p-email" type="email" name="email"
                               value="<?php echo e(old('email', $currentUser->email)); ?>" required>
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

                <form action="<?php echo e(route('portal.admin.profile.save')); ?>" method="post">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="email" value="<?php echo e($currentUser->email); ?>">
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
<?php $__env->stopSection(); ?>

<?php echo $__env->make('portal.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mairie_wp\backend-laravel\resources\views/portal/admin-profil.blade.php ENDPATH**/ ?>