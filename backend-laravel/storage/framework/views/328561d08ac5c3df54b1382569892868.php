

<?php $__env->startSection('content'); ?>
    <?php echo $__env->make('portal.components.admin-nav', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <section class="hero">
        <div class="hero__panel">
            <span class="eyebrow">Administration - Citoyenneté</span>
            <h1 class="tw:font-semibold"><?php echo e($pageTitle); ?></h1>
            <p class="muted">Liste des citoyens inscrits à cette activité</p>
            <div class="actions tw:mt-3">
                <a class="button button--ghost" href="<?php echo e(route('portal.admin.civic_activities')); ?>">← Retour aux activités</a>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="panel tw:rounded-2xl tw:border tw:border-emerald-200/60 tw:bg-white/95 tw:p-6">
            <div class="tw:mb-6">
                <h2 class="tw:text-lg tw:font-semibold tw:mb-2"><?php echo e($activity->title); ?></h2>
                <p class="tw:text-sm tw:text-gray-600">
                    📅 <?php echo e($activity->event_date?->format('d/m/Y')); ?>

                    <?php if($activity->event_start_time && $activity->event_end_time): ?>
                        • ⏰ <?php echo e($activity->event_start_time); ?> - <?php echo e($activity->event_end_time); ?>

                    <?php endif; ?>
                    <?php if($activity->location): ?>
                        • 📍 <?php echo e($activity->location); ?>

                    <?php endif; ?>
                </p>
            </div>

            <?php if($registrations->count() > 0): ?>
                <div class="tw:overflow-x-auto">
                    <table class="tw:w-full tw:text-sm">
                        <thead class="tw:bg-emerald-50 tw:border-b tw:border-emerald-200">
                            <tr>
                                <th class="tw:text-left tw:px-4 tw:py-3 tw:font-semibold">Nom</th>
                                <th class="tw:text-left tw:px-4 tw:py-3 tw:font-semibold">Email</th>
                                <th class="tw:text-left tw:px-4 tw:py-3 tw:font-semibold">Téléphone</th>
                                <th class="tw:text-left tw:px-4 tw:py-3 tw:font-semibold">Date d'inscription</th>
                                <th class="tw:text-center tw:px-4 tw:py-3 tw:font-semibold">Statut</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $registrations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $reg): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr class="tw:border-b tw:border-gray-200 tw:hover:tw:bg-gray-50">
                                    <td class="tw:px-4 tw:py-3 tw:font-medium">
                                        <?php echo e($reg->first_name); ?> <?php echo e($reg->last_name); ?>

                                    </td>
                                    <td class="tw:px-4 tw:py-3">
                                        <a href="mailto:<?php echo e($reg->email); ?>" class="tw:text-emerald-600 tw:hover:tw:underline">
                                            <?php echo e($reg->email); ?>

                                        </a>
                                    </td>
                                    <td class="tw:px-4 tw:py-3">
                                        <?php if($reg->phone): ?>
                                            <a href="tel:<?php echo e($reg->phone); ?>" class="tw:text-emerald-600 tw:hover:tw:underline">
                                                <?php echo e($reg->phone); ?>

                                            </a>
                                        <?php else: ?>
                                            <span class="tw:text-gray-400">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="tw:px-4 tw:py-3">
                                        <?php if($reg->registered_at): ?>
                                            <?php echo e(\Carbon\Carbon::parse($reg->registered_at)->format('d/m/Y H:i')); ?>

                                        <?php else: ?>
                                            <span class="tw:text-gray-400">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="tw:px-4 tw:py-3 tw:text-center">
                                        <?php if($reg->status === 'attended'): ?>
                                            <span class="tw:inline-block tw:px-3 tw:py-1 tw:bg-green-100 tw:text-green-800 tw:rounded-full tw:text-xs tw:font-medium">
                                                ✓ Participé
                                            </span>
                                        <?php elseif($reg->status === 'cancelled'): ?>
                                            <span class="tw:inline-block tw:px-3 tw:py-1 tw:bg-red-100 tw:text-red-800 tw:rounded-full tw:text-xs tw:font-medium">
                                                ✗ Annulé
                                            </span>
                                        <?php else: ?>
                                            <span class="tw:inline-block tw:px-3 tw:py-1 tw:bg-blue-100 tw:text-blue-800 tw:rounded-full tw:text-xs tw:font-medium">
                                                📋 Inscrit
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>

                <div class="tw:mt-6 tw:p-4 tw:bg-emerald-50 tw:rounded-lg tw:border tw:border-emerald-200">
                    <p class="tw:text-sm tw:text-emerald-900">
                        <strong>Total inscriptions:</strong> <?php echo e($registrations->count()); ?>

                        <?php if($activity->max_participants): ?>
                            / <?php echo e($activity->max_participants); ?>

                        <?php endif; ?>
                    </p>
                </div>
            <?php else: ?>
                <div class="panel tw:rounded-2xl tw:border tw:border-amber-200/60 tw:bg-amber-50/50 tw:p-6">
                    <p class="muted tw:text-center">
                        <i class="bi bi-info-circle me-2"></i>
                        Aucune inscription pour cette activité.
                    </p>
                </div>
            <?php endif; ?>
        </div>
    </section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('portal.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mairie_wp\backend-laravel\resources\views/portal/admin-civic-activity-registrations.blade.php ENDPATH**/ ?>