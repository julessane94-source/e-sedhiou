

<?php $__env->startSection('content'); ?>
    <?php echo $__env->make('portal.components.admin-nav', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

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
                    <span><?php echo e(($settings['allow_register'] ?? 1) ? 'Ouvertes aux nouveaux citoyens.' : 'Desactivees pour le moment.'); ?></span>
                </div>
                <div class="fact-card">
                    <strong>Maintenance</strong>
                    <span><?php echo e(($settings['maintenance_mode'] ?? 0) ? 'Mode maintenance actif.' : 'Portail accessible.'); ?></span>
                </div>
                <div class="fact-card">
                    <strong>Signature du maire</strong>
                    <span><?php echo e($hasMayorSignature ? 'Fichier charge et utilisable pour le traitement en ligne.' : 'Aucune signature numerique disponible.'); ?></span>
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

            <form action="<?php echo e(route('portal.admin.settings.save')); ?>" method="post" enctype="multipart/form-data">
                <?php echo csrf_field(); ?>

                <div class="form-grid">
                    <div class="field">
                        <label for="site_name">Nom du site <span class="muted">*</span></label>
                        <input id="site_name" type="text" name="site_name"
                               value="<?php echo e(old('site_name', $settings['site_name'] ?? '')); ?>" required>
                    </div>

                    <div class="field">
                        <label for="contact_email">Email de contact <span class="muted">*</span></label>
                        <input id="contact_email" type="email" name="contact_email"
                               value="<?php echo e(old('contact_email', $settings['contact_email'] ?? '')); ?>" required>
                    </div>

                    <div class="field">
                        <label for="contact_phone">Telephone</label>
                        <input id="contact_phone" type="text" name="contact_phone"
                               value="<?php echo e(old('contact_phone', $settings['contact_phone'] ?? '')); ?>"
                               placeholder="+221 33 000 00 00">
                    </div>
                </div>

                <div class="field">
                    <label for="site_description">Description courte</label>
                    <textarea id="site_description" name="site_description" rows="2"><?php echo e(old('site_description', $settings['site_description'] ?? '')); ?></textarea>
                </div>

                <div class="field">
                    <label for="contact_address">Adresse physique</label>
                    <textarea id="contact_address" name="contact_address" rows="2"><?php echo e(old('contact_address', $settings['contact_address'] ?? '')); ?></textarea>
                </div>

                <div class="form-grid tw:mt-2.5">
                    <div class="field">
                        <label class="tw:flex tw:items-center tw:gap-2 tw:cursor-pointer">
                            <input type="hidden" name="allow_register" value="0">
                            <input type="checkbox" name="allow_register" value="1"
                                   <?php echo e(($settings['allow_register'] ?? 1) ? 'checked' : ''); ?>>
                            Autoriser les nouvelles inscriptions
                        </label>
                    </div>
                    <div class="field">
                        <label class="tw:flex tw:items-center tw:gap-2 tw:cursor-pointer">
                            <input type="hidden" name="maintenance_mode" value="0">
                            <input type="checkbox" name="maintenance_mode" value="1"
                                   <?php echo e(($settings['maintenance_mode'] ?? 0) ? 'checked' : ''); ?>>
                            Mode maintenance (portail inaccessible aux citoyens)
                        </label>
                    </div>
                </div>

                <hr class="divider">

                <span class="eyebrow">Demandes</span>
                <h2 class="tw:mt-1.5">Tarifs par type</h2>
                <div class="form-grid">
                    <?php $__currentLoopData = $requestTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $typeKey => $typeLabel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="field">
                            <label for="fee-<?php echo e($typeKey); ?>"><?php echo e($typeLabel); ?> (FCFA)</label>
                            <input
                                id="fee-<?php echo e($typeKey); ?>"
                                type="number"
                                min="0"
                                step="100"
                                name="request_fees[<?php echo e($typeKey); ?>]"
                                value="<?php echo e(old('request_fees.' . $typeKey, $requestFees[$typeKey] ?? 0)); ?>"
                                required
                            >
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>

                <hr class="divider">

                <span class="eyebrow">Validation en ligne</span>
                <h2 class="tw:mt-1.5">Signature numerique du maire</h2>
                <div class="field">
                    <label for="mayor_signature">Televerser la signature (PNG/JPG)</label>
                    <input id="mayor_signature" type="file" name="mayor_signature" accept=".png,.jpg,.jpeg">
                    <?php if($hasMayorSignature): ?>
                        <small class="muted">Signature actuelle: <?php echo e($settings['mayor_signature_name'] ?? 'signature-chargee'); ?></small>
                    <?php else: ?>
                        <small class="muted">Aucune signature configuree pour le traitement en ligne.</small>
                    <?php endif; ?>
                </div>

                <button class="button button--primary tw:mt-5" type="submit">
                    Enregistrer les parametres
                </button>
            </form>
        </div>
    </section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('portal.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mairie_wp\backend-laravel\resources\views/portal/admin-settings.blade.php ENDPATH**/ ?>