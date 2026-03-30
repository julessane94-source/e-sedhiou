

<?php $__env->startSection('content'); ?>
    <?php echo $__env->make('portal.components.admin-nav', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <?php
        $activeFilters = (request('status') ? 1 : 0) + (request('type') ? 1 : 0);
    ?>

    <section class="hero">
        <div class="hero__panel">
            <span class="eyebrow">Administration</span>
            <h1 class="tw:font-semibold">Suivi des demandes</h1>
            <p class="muted">Filtrez, consultez et pilotez toutes les demandes du portail.</p>
        </div>

        <div class="panel">
            <div class="panel-header">
                <div class="panel-header__title">
                    <span class="eyebrow">Filtres</span>
                    <h2 class="tw:font-semibold">Recherche</h2>
                </div>
                <span class="panel-header__meta">Affichage transversal des dossiers</span>
            </div>
            <form method="get" action="<?php echo e(route('portal.admin.demandes')); ?>" class="admin-toolbar">
                <div class="field">
                    <label for="f-status">Statut</label>
                    <select id="f-status" name="status">
                        <option value="">Tous</option>
                        <?php $__currentLoopData = $statusLabels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($key); ?>" <?php if(request('status') === $key): echo 'selected'; endif; ?>><?php echo e($label); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="field">
                    <label for="f-type">Type</label>
                    <select id="f-type" name="type">
                        <option value="">Tous</option>
                        <?php $__currentLoopData = $requestTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($key); ?>" <?php if(request('type') === $key): echo 'selected'; endif; ?>><?php echo e($label); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="actions">
                    <button class="button button--accent" type="submit">Filtrer</button>
                    <a class="button button--ghost" href="<?php echo e(route('portal.admin.demandes')); ?>">Reinitialiser</a>
                </div>
            </form>
        </div>
    </section>

    <section class="section">
        <div class="metric-strip tw:mb-6">
            <?php $__currentLoopData = [['label'=>'Total','val'=>$counts['total'],'class'=>'assigned'],['label'=>'En attente','val'=>$counts['pending'],'class'=>'pending'],['label'=>'En cours','val'=>$counts['processing'],'class'=>'processing'],['label'=>'Traitees','val'=>$counts['completed'],'class'=>'completed']]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $card): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="metric-tile tw:bg-white tw:shadow-sm tw:rounded-lg">
                    <span class="metric-tile__label tw:text-xs tw:text-gray-500 tw:font-medium"><?php echo e($card['label']); ?></span>
                    <strong class="metric-tile__value tw:text-xl md:tw:text-2xl tw:font-bold"><?php echo e($card['val']); ?></strong>
                    <div class="metric-tile__meta">
                        <span class="badge <?php echo e($card['class']); ?>"><?php echo e($card['label']); ?></span>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        <div class="panel">
            <div class="panel-header">
                <div class="panel-header__title">
                    <span class="eyebrow"><?php echo e($demandes->total()); ?> dossier(s)</span>
                    <h2 class="tw:font-semibold">Demandes</h2>
                </div>
                <span class="panel-header__meta">Filtres actifs: <?php echo e($activeFilters); ?></span>
            </div>

            <?php if($demandes->isEmpty()): ?>
                <p class="muted">Aucune demande.</p>
            <?php else: ?>
                <div class="table-card admin-table">
                <table class="tw:text-[0.92em]">
                    <thead>
                        <tr>
                            <th>Reference</th>
                            <th>Citoyen</th>
                            <th>Type</th>
                            <th>Agent</th>
                            <th>Statut</th>
                            <th>Date</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $demandes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $cName = trim(($d->citoyen->first_name ?? '') . ' ' . ($d->citoyen->last_name ?? '')) ?: ($d->citoyen->name ?? '—');
                                $aName = $d->agent ? (trim(($d->agent->first_name ?? '') . ' ' . ($d->agent->last_name ?? '')) ?: $d->agent->name) : '—';
                            ?>
                            <tr>
                                <td><strong><?php echo e($d->reference); ?></strong></td>
                                <td><?php echo e($cName); ?></td>
                                <td><?php echo e($requestTypes[$d->request_type] ?? $d->request_type); ?></td>
                                <td><?php echo e($aName); ?></td>
                                <td><span class="badge <?php echo e($d->status); ?>"><?php echo e($statusLabels[$d->status] ?? $d->status); ?></span></td>
                                <td><?php echo e(optional($d->created_at)->format('d/m/Y')); ?></td>
                                <td><a class="button button--ghost" href="<?php echo e(route('portal.demandes.show', $d)); ?>">Voir</a></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
                </div>

                <div class="tw:mt-4"><?php echo e($demandes->withQueryString()->links()); ?></div>
            <?php endif; ?>
        </div>
    </section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('portal.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mairie_wp\backend-laravel\resources\views/portal/admin-demandes.blade.php ENDPATH**/ ?>