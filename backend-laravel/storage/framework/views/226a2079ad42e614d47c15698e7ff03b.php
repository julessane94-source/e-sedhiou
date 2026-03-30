

<?php $__env->startSection('content'); ?>
    <?php echo $__env->make('portal.components.admin-nav', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <section class="hero">
        <div class="hero__panel">
            <span class="eyebrow">Administration - Citoyenneté</span>
            <h1 class="tw:font-semibold"><?php echo e($pageTitle); ?></h1>
            <p class="muted">Liste des citoyens qui ont consulté ce cours</p>
            <div class="actions tw:mt-3">
                <a class="button button--ghost" href="<?php echo e(route('portal.admin.civic_courses')); ?>">← Retour aux cours</a>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="panel tw:rounded-2xl tw:border tw:border-emerald-200/60 tw:bg-white/95 tw:p-6">
            <div class="tw:mb-6">
                <div class="tw:flex tw:items-center tw:gap-3">
                    <span class="tw:text-4xl"><?php echo e($course->icon_emoji ?? '📚'); ?></span>
                    <div>
                        <h2 class="tw:text-lg tw:font-semibold"><?php echo e($course->title); ?></h2>
                        <p class="tw:text-sm tw:text-gray-600">
                            📝 <?php echo e($course->description); ?>

                        </p>
                    </div>
                </div>
            </div>

            <?php if($viewers->count() > 0): ?>
                <div class="tw:overflow-x-auto">
                    <table class="tw:w-full tw:text-sm">
                        <thead class="tw:bg-emerald-50 tw:border-b tw:border-emerald-200">
                            <tr>
                                <th class="tw:text-left tw:px-4 tw:py-3 tw:font-semibold">Nom</th>
                                <th class="tw:text-left tw:px-4 tw:py-3 tw:font-semibold">Email</th>
                                <th class="tw:text-left tw:px-4 tw:py-3 tw:font-semibold">Téléphone</th>
                                <th class="tw:text-left tw:px-4 tw:py-3 tw:font-semibold">Première consultation</th>
                                <th class="tw:text-left tw:px-4 tw:py-3 tw:font-semibold">Dernière consultation</th>
                                <th class="tw:text-center tw:px-4 tw:py-3 tw:font-semibold">Nombre de vues</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $viewers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $view): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr class="tw:border-b tw:border-gray-200 tw:hover:tw:bg-gray-50">
                                    <td class="tw:px-4 tw:py-3 tw:font-medium">
                                        <?php echo e($view->first_name); ?> <?php echo e($view->last_name); ?>

                                    </td>
                                    <td class="tw:px-4 tw:py-3">
                                        <a href="mailto:<?php echo e($view->email); ?>" class="tw:text-emerald-600 tw:hover:tw:underline">
                                            <?php echo e($view->email); ?>

                                        </a>
                                    </td>
                                    <td class="tw:px-4 tw:py-3">
                                        <?php if($view->phone): ?>
                                            <a href="tel:<?php echo e($view->phone); ?>" class="tw:text-emerald-600 tw:hover:tw:underline">
                                                <?php echo e($view->phone); ?>

                                            </a>
                                        <?php else: ?>
                                            <span class="tw:text-gray-400">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="tw:px-4 tw:py-3">
                                        <?php if($view->created_at): ?>
                                            <?php echo e(\Carbon\Carbon::parse($view->created_at)->format('d/m/Y H:i')); ?>

                                        <?php else: ?>
                                            <span class="tw:text-gray-400">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="tw:px-4 tw:py-3">
                                        <?php if($view->viewed_at): ?>
                                            <div class="tw:flex tw:items-center tw:gap-1">
                                                <i class="bi bi-calendar3 tw:text-emerald-600"></i>
                                                <?php echo e(\Carbon\Carbon::parse($view->viewed_at)->format('d/m/Y H:i')); ?>

                                            </div>
                                        <?php else: ?>
                                            <span class="tw:text-gray-400">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="tw:text-center">
                                        <span class="tw:inline-block tw:px-3 tw:py-1 tw:bg-emerald-100 tw:text-emerald-800 tw:rounded-full tw:text-xs tw:font-medium">
                                            <?php echo e($view->view_count); ?> 👁
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>

                <div class="tw:mt-6 tw:p-4 tw:bg-emerald-50 tw:rounded-lg tw:border tw:border-emerald-200">
                    <p class="tw:text-sm tw:text-emerald-900">
                        <strong>Nombre de citoyens qui ont consulté:</strong> <?php echo e($viewers->count()); ?>

                    </p>
                    <p class="tw:text-sm tw:text-emerald-900 tw:mt-1">
                        <strong>Nombre total de consultations:</strong> <?php echo e($viewers->sum('view_count')); ?>

                    </p>
                </div>
            <?php else: ?>
                <div class="panel tw:rounded-2xl tw:border tw:border-amber-200/60 tw:bg-amber-50/50 tw:p-6">
                    <p class="muted tw:text-center">
                        <i class="bi bi-info-circle me-2"></i>
                        Aucun citoyen n'a consulté ce cours pour le moment.
                    </p>
                </div>
            <?php endif; ?>
        </div>
    </section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('portal.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mairie_wp\backend-laravel\resources\views/portal/admin-civic-course-readers.blade.php ENDPATH**/ ?>