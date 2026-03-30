

<?php $__env->startSection('content'); ?>
    <?php echo $__env->make('portal.components.admin-nav', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <section class="hero">
        <div class="hero__panel">
            <span class="eyebrow">Administration - Citoyenneté</span>
            <h1 class="tw:font-semibold">Gestion des activités communautaires</h1>
            <p class="muted">Créez, modifiez et gérez les activités et événements organisés par la mairie.</p>
        </div>

        <div class="panel">
            <div class="panel-header">
                <div class="panel-header__title">
                    <span class="eyebrow">Nouvelle activité</span>
                    <h2 class="tw:font-semibold">Ajouter une activité</h2>
                </div>
            </div>
            <div class="tw:flex tw:justify-end">
                <a href="<?php echo e(route('portal.admin.civic_activities.create')); ?>" class="button button--primary tw:rounded-full tw:px-5 tw:py-2.5">
                    <i class="bi bi-plus-circle me-1"></i>Créer une activité
                </a>
            </div>
        </div>
    </section>

    <section class="section">
        <?php if($activities->isEmpty()): ?>
            <div class="panel tw:text-center tw:rounded-2xl tw:border tw:border-emerald-200/60 tw:bg-white/90 tw:p-8">
                <div class="tw:text-4xl tw:mb-3">🎯</div>
                <p class="muted tw:mb-4">Aucune activité créée pour le moment.</p>
                <a href="<?php echo e(route('portal.admin.civic_activities.create')); ?>" class="button button--primary tw:rounded-full tw:px-5 tw:py-2.5">
                    Créer la première activité
                </a>
            </div>
        <?php else: ?>
            <div class="grid tw:gap-3">
                <?php $__currentLoopData = $activities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $activity): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="panel tw:rounded-2xl tw:border tw:border-emerald-200/60 tw:bg-white/95 tw:p-5">
                        <div class="tw:flex tw:items-start tw:justify-between tw:gap-4">
                            <div class="tw:flex-1">
                                <div class="tw:flex tw:items-center tw:gap-3 tw:mb-2">
                                    <span class="tw:text-3xl"><?php echo e($activity->icon_emoji); ?></span>
                                    <div>
                                        <h3 class="tw:font-bold tw:text-lg"><?php echo e($activity->title); ?></h3>
                                        <p class="muted tw:text-xs"><?php echo e($activity->slug); ?></p>
                                    </div>
                                </div>
                                <p class="tw:text-sm tw:text-gray-700 tw:mb-3"><?php echo e(Str::limit($activity->description, 100)); ?></p>
                                <div class="tw:flex tw:flex-wrap tw:gap-2 tw:items-center tw:text-xs">
                                    <?php if($activity->event_date): ?>
                                        <span class="tw:px-2 tw:py-1 tw:rounded-full tw:bg-amber-100 tw:text-amber-800">
                                            📅 <?php echo e($activity->event_date->format('d/m/Y')); ?>

                                        </span>
                                    <?php endif; ?>
                                    <span class="tw:px-2 tw:py-1 tw:rounded-full tw:bg-blue-100 tw:text-blue-800">
                                        <?php echo e($activity->getTypeLabel()); ?>

                                    </span>
                                    <span class="tw:px-2 tw:py-1 tw:rounded-full tw:bg-purple-100 tw:text-purple-800">
                                        <?php echo e($activity->getFrequencyLabel()); ?>

                                    </span>
                                    <span class="tw:px-2 tw:py-1 tw:rounded-full 
                                        <?php echo e(match($activity->status) {
                                            'upcoming' => 'tw:bg-green-100 tw:text-green-800',
                                            'ongoing' => 'tw:bg-blue-100 tw:text-blue-800',
                                            'completed' => 'tw:bg-gray-100 tw:text-gray-800',
                                            'cancelled' => 'tw:bg-red-100 tw:text-red-800',
                                            default => 'tw:bg-gray-100 tw:text-gray-800',
                                        }); ?>">
                                        <?php echo e($activity->getStatusLabel()); ?>

                                    </span>
                                    <span class="tw:ml-auto tw:px-2 tw:py-1 tw:rounded-full <?php echo e($activity->is_active ? 'tw:bg-green-100 tw:text-green-800' : 'tw:bg-red-100 tw:text-red-800'); ?>">
                                        <?php echo e($activity->is_active ? '✓ Actif' : '✕ Inactif'); ?>

                                    </span>
                                </div>
                                <p class="tw:text-xs tw:text-gray-600 tw:mt-2">📍 <?php echo e($activity->location); ?></p>
                            </div>
                            <div class="tw:flex tw:gap-2">
                                <a href="<?php echo e(route('portal.admin.civic_activities.edit', $activity)); ?>" class="button button--ghost tw:rounded-full tw:px-4 tw:py-2 tw:text-sm">
                                    <i class="bi bi-pencil me-1"></i>Modifier
                                </a>
                                <a href="<?php echo e(route('portal.admin.civic_activities.registrations', $activity)); ?>" class="button button--primary tw:rounded-full tw:px-4 tw:py-2 tw:text-sm">
                                    <i class="bi bi-people me-1"></i>Inscriptions
                                </a>
                                <form action="<?php echo e(route('portal.admin.civic_activities.destroy', $activity)); ?>" method="post" style="display:inline;">
                                    <?php echo csrf_field(); ?>
                                    <button class="button button--danger tw:rounded-full tw:px-4 tw:py-2 tw:text-sm" type="submit" onclick="return confirm('Êtes-vous sûr?')">
                                        <i class="bi bi-trash me-1"></i>Supprimer
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        <?php endif; ?>
    </section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('portal.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mairie_wp\backend-laravel\resources\views/portal/admin-civic-activities.blade.php ENDPATH**/ ?>