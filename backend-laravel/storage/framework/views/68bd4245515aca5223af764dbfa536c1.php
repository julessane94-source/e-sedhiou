

<?php $__env->startSection('content'); ?>
    <?php echo $__env->make('portal.components.admin-nav', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <section class="hero">
        <div class="hero__panel">
            <span class="eyebrow">Administration</span>
            <h1 class="tw:font-semibold">Liste des citoyens</h1>
            <p class="muted">Consultez et exportez la liste des citoyens inscrits.</p>
        </div>

        <div class="panel">
            <div class="panel-header">
                <div class="panel-header__title">
                    <span class="eyebrow">Filtre &amp; Export</span>
                    <h2 class="tw:font-semibold">Options</h2>
                </div>
            </div>

            <form method="get" action="<?php echo e(route('portal.admin.citoyens')); ?>" class="admin-toolbar tw:mb-4 tw:flex tw:gap-4">
                <div class="field tw:min-w-[180px]">
                    <label for="f-type">Type de demande</label>
                    <select id="f-type" name="type">
                        <option value="">Tous</option>
                        <?php $__currentLoopData = $requestTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($key); ?>" <?php if($filterType === $key): echo 'selected'; endif; ?>><?php echo e($label); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="field tw:min-w-[180px]">
                    <label for="f-category">Catégorie</label>
                    <select id="f-category" name="category">
                        <option value="">Toutes</option>
                        <option value="etudiant" <?php if(request('category')==='etudiant'): echo 'selected'; endif; ?>>Étudiant</option>
                        <option value="fonctionnaire" <?php if(request('category')==='fonctionnaire'): echo 'selected'; endif; ?>>Fonctionnaire</option>
                        <option value="autre" <?php if(request('category')==='autre'): echo 'selected'; endif; ?>>Autre</option>
                    </select>
                </div>
                <div class="field tw:min-w-[180px]">
                    <label for="f-location">Localisation</label>
                    <input id="f-location" name="location" type="text" value="<?php echo e(request('location')); ?>" placeholder="Sédhiou ou autre" class="tw-w-full tw-border tw-rounded tw-px-3 tw-py-2">
                </div>
                <div class="actions tw-flex tw-items-end tw-gap-2">
                    <button class="button button--accent" type="submit">Filtrer</button>
                    <a class="button button--ghost" href="<?php echo e(route('portal.admin.citoyens')); ?>">Tous</a>
                </div>
            </form>

            <div class="actions">
                <a class="button button--primary"
                   href="<?php echo e(route('portal.admin.citoyens.export', ['type' => $filterType, 'format' => 'csv'])); ?>">
                    Exporter Excel / CSV
                </a>
                <a class="button button--accent"
                   href="<?php echo e(route('portal.admin.citoyens.export', ['type' => $filterType, 'format' => 'pdf'])); ?>">
                    Exporter PDF
                </a>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="metric-strip tw:mb-5">
            <?php
                $activeCitoyens = $citoyens->getCollection()->where('is_active', true)->count();
                $inactiveCitoyens = $citoyens->getCollection()->where('is_active', false)->count();
                $totalDemandesCitoyens = (int) $citoyens->getCollection()->sum('demandes_count');
                $avgDemandes = $citoyens->count() > 0 ? round($totalDemandesCitoyens / $citoyens->count(), 1) : 0;
            ?>
            <?php $__currentLoopData = [
                ['label' => 'Resultats affiches', 'val' => $citoyens->count(), 'class' => 'assigned'],
                ['label' => 'Comptes actifs', 'val' => $activeCitoyens, 'class' => 'completed'],
                ['label' => 'Comptes inactifs', 'val' => $inactiveCitoyens, 'class' => 'rejected'],
                ['label' => 'Moyenne dossiers', 'val' => $avgDemandes, 'class' => 'processing'],
            ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $card): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
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
                    <span class="eyebrow"><?php echo e($citoyens->total()); ?> citoyen(s)</span>
                    <h2 class="tw:font-semibold">
                        Citoyens
                        <?php if($filterType): ?>
                            <span class="muted tw:font-normal tw:text-[0.85em]">— <?php echo e($requestTypes[$filterType] ?? $filterType); ?></span>
                        <?php endif; ?>
                    </h2>
                </div>
            </div>

            <?php if($citoyens->isEmpty()): ?>
                <p class="muted">Aucun citoyen trouve.</p>
            <?php else: ?>
                <div class="table-card admin-table">
                <table class="tw:text-[0.92em]">
                    <thead>
                        <tr>
                            <th>Nom</th>
                            <th>Email</th>
                            <th>Telephone</th>
                            <th>Adresse</th>
                            <th>Date naiss.</th>
                            <th>Lieu naiss.</th>
                            <th>N registre</th>
                            <th>N citoyen</th>
                            <th>Dossiers</th>
                            <th>Actif</th>
                            <th>Inscrit le</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $citoyens; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><strong><?php echo e(trim(($c->first_name ?? '') . ' ' . ($c->last_name ?? '')) ?: $c->name); ?></strong></td>
                                <td><?php echo e($c->email); ?></td>
                                <td><?php echo e($c->phone ?? '—'); ?></td>
                                <td class="wrap-pre tw:max-w-[220px]"><?php echo e($c->address ?? '—'); ?></td>
                                <td class="nowrap"><?php echo e(optional($c->birth_date)->format('d/m/Y') ?? '—'); ?></td>
                                <td><?php echo e($c->birth_place ?? '—'); ?></td>
                                <td><?php echo e($c->register_number ?? '—'); ?></td>
                                <td class="nowrap tw:text-[0.82em]"><?php echo e($c->citizen_number ?? '—'); ?></td>
                                <td><?php echo e($c->demandes_count); ?></td>
                                <td>
                                    <span class="badge <?php echo e($c->is_active ? 'completed' : 'rejected'); ?>"><?php echo e($c->is_active ? 'Oui' : 'Non'); ?></span>
                                </td>
                                <td class="nowrap"><?php echo e(optional($c->created_at)->format('d/m/Y')); ?></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
                </div>

                <div class="tw:mt-4"><?php echo e($citoyens->withQueryString()->links()); ?></div>
            <?php endif; ?>
        </div>
    </section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('portal.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mairie_wp\backend-laravel\resources\views/portal/admin-citoyens.blade.php ENDPATH**/ ?>