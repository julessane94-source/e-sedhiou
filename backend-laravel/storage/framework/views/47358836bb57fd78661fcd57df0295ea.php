

<?php $__env->startSection('content'); ?>
    <?php echo $__env->make('portal.components.admin-nav', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <section class="hero">
        <div class="hero__panel">
            <span class="eyebrow">Administration</span>
            <div class="tw:overflow-hidden tw:h-12 tw:flex tw:items-center">
                <h1 class="tw:font-semibold slide-animate">Statistiques</h1>
            </div>
            <p class="muted">Activite globale du portail.</p>
            <div class="hero__actions">
                <a class="button button--primary" href="<?php echo e(route('portal.admin.documents.architecture.download')); ?>">
                    Telecharger le document d'architecture (PDF)
                </a>
            </div>
            <p class="panel-note">Le PDF est genere puis le document source est supprime automatiquement apres ce telechargement.</p>
        </div>

        <div class="panel">
            <span class="eyebrow">Pilotage</span>
            <h2 class="tw:font-semibold">Vue d'ensemble</h2>
            <div class="fact-grid">
                <div class="fact-card">
                    <strong>Charge globale</strong>
                    <span><?php echo e($global['total']); ?> dossiers suivis sur le portail.</span>
                </div>
                <div class="fact-card">
                    <strong>Rythme de traitement</strong>
                    <span><?php echo e($global['completed']); ?> demandes bouclees et <?php echo e($global['pending']); ?> encore en attente.</span>
                </div>
                <div class="fact-card">
                    <strong>Population active</strong>
                    <span><?php echo e($global['citoyens']); ?> citoyens inscrits et un suivi RH centralise.</span>
                </div>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="metric-strip tw:mb-7">
            <?php $__currentLoopData = [
                ['label' => 'Demandes totales', 'val' => $global['total'],     'class' => 'assigned'],
                ['label' => 'Traitees',          'val' => $global['completed'], 'class' => 'completed'],
                ['label' => 'En attente',         'val' => $global['pending'],   'class' => 'pending'],
                ['label' => 'Citoyens inscrits',  'val' => $global['citoyens'], 'class' => 'processing'],
            ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kpi): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="metric-tile tw:bg-white tw:shadow-sm tw:rounded-lg">
                    <span class="metric-tile__label tw:text-xs tw:text-gray-500 tw:font-medium"><?php echo e($kpi['label']); ?></span>
                    <strong class="metric-tile__value tw:text-xl md:tw:text-2xl tw:font-bold"><?php echo e($kpi['val']); ?></strong>
                    <div class="metric-tile__meta">
                        <span class="badge <?php echo e($kpi['class']); ?>"><?php echo e($kpi['label']); ?></span>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        <div class="grid grid--2 tw:mb-[22px]">
            <div class="panel">
                <div class="panel-header">
                    <div class="panel-header__title">
                        <span class="eyebrow">Ressources humaines</span>
                        <h2 class="tw:font-semibold">Pointage des agents</h2>
                    </div>
                </div>
                <p class="muted">
                    La saisie du pointage est desormais reservee au superviseur.
                    Cette page permet uniquement le suivi global des presences.
                </p>
            </div>

            <div class="panel">
                <div class="panel-header">
                    <div class="panel-header__title">
                        <span class="eyebrow">Derniers pointages</span>
                        <h2 class="tw:font-semibold">Historique recent</h2>
                    </div>
                    <span class="panel-header__meta"><?php echo e($recentAttendances->count()); ?> entree(s)</span>
                </div>
                <?php if($recentAttendances->isEmpty()): ?>
                    <p class="muted">Aucun pointage enregistre.</p>
                <?php else: ?>
                    <div class="table-card admin-table">
                        <table>
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Agent</th>
                                    <th>Statut</th>
                                    <th>Plage horaire</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $recentAttendances; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php $agentName = trim(($row->agent->first_name ?? '') . ' ' . ($row->agent->last_name ?? '')) ?: ($row->agent->name ?? 'Agent'); ?>
                                    <tr>
                                        <td><?php echo e(optional($row->attendance_date)->format('d/m/Y')); ?></td>
                                        <td><?php echo e($agentName); ?></td>
                                        <td><?php echo e($attendanceStatusLabels[$row->status] ?? ucfirst($row->status)); ?></td>
                                        <td>
                                            <?php if($row->check_in_time || $row->check_out_time): ?>
                                                <?php echo e($row->check_in_time ?: '--:--'); ?> - <?php echo e($row->check_out_time ?: '--:--'); ?>

                                            <?php else: ?>
                                                --
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="panel tw:mb-6">
            <div class="panel-header">
                <div class="panel-header__title">
                    <span class="eyebrow">Performance agent</span>
                    <h2 class="tw:font-semibold">Suivi compare des agents (mois en cours)</h2>
                </div>
                <span class="panel-header__meta"><?php echo e($agentPerformanceRows->count()); ?> agent(s)</span>
            </div>
            <?php if($agentPerformanceRows->isEmpty()): ?>
                <p class="muted">Aucun agent disponible.</p>
            <?php else: ?>
                <div class="table-card admin-table">
                    <table>
                        <thead>
                            <tr>
                                <th>Agent</th>
                                <th>Dossiers assignes</th>
                                <th>Traites</th>
                                <th>Rejetes</th>
                                <th>Taux de reussite</th>
                                <th>Traites ce mois</th>
                                <th>Presences</th>
                                <th>Absences</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $agentPerformanceRows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td><?php echo e($row['name']); ?></td>
                                    <td><?php echo e($row['assigned_total']); ?></td>
                                    <td><?php echo e($row['completed']); ?></td>
                                    <td><?php echo e($row['rejected']); ?></td>
                                    <td><?php echo e($row['completion_rate']); ?> %</td>
                                    <td><?php echo e($row['monthly_completed']); ?></td>
                                    <td><?php echo e($row['present_days']); ?></td>
                                    <td><?php echo e($row['absent_days']); ?></td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>

        <div class="grid grid--2">
            
            <div class="panel">
                <div class="panel-header">
                    <div class="panel-header__title">
                        <span class="eyebrow">Repartition</span>
                        <h2 class="tw:font-semibold">Demandes par type</h2>
                    </div>
                </div>
                <?php if($byType->isEmpty()): ?>
                    <p class="muted">Aucune donnee.</p>
                <?php else: ?>
                    <div class="bar-list">
                    <?php $__currentLoopData = $byType; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php $label = $requestTypes[$row->request_type] ?? $row->request_type; ?>
                        <div class="bar-row">
                            <div class="bar-row__meta">
                                <span><?php echo e($label); ?></span>
                                <strong><?php echo e($row->total); ?></strong>
                            </div>
                            <?php if($global['total'] > 0): ?>
                                <div class="bar-row__track">
                                    <div class="bar-row__fill" data-width="<?php echo e(round($row->total / $global['total'] * 100)); ?>"></div>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="panel">
                <div class="panel-header">
                    <div class="panel-header__title">
                        <span class="eyebrow">Evolution</span>
                        <h2 class="tw:font-semibold">Demandes par mois (12 mois)</h2>
                    </div>
                </div>
                <?php if($byMonth->isEmpty()): ?>
                    <p class="muted">Aucune donnee.</p>
                <?php else: ?>
                    <?php $maxMonth = $byMonth->max('total') ?: 1; ?>
                    <div class="bar-list">
                    <?php $__currentLoopData = $byMonth; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="bar-row">
                            <div class="bar-row__meta">
                                <span><?php echo e($row->month); ?></span>
                                <strong><?php echo e($row->total); ?></strong>
                            </div>
                            <div class="bar-row__track">
                                <div class="bar-row__fill bar-row__fill--accent" data-width="<?php echo e(round($row->total / $maxMonth * 100)); ?>"></div>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="panel">
                <div class="panel-header">
                    <div class="panel-header__title">
                        <span class="eyebrow">Agents</span>
                        <h2 class="tw:font-semibold">Demandes traitees par agent</h2>
                    </div>
                </div>
                <?php if($byAgent->isEmpty()): ?>
                    <p class="muted">Aucun agent.</p>
                <?php else: ?>
                    <?php $maxAgent = $byAgent->max('total') ?: 1; ?>
                    <div class="bar-list">
                    <?php $__currentLoopData = $byAgent; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ag): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php $agName = trim(($ag->first_name ?? '') . ' ' . ($ag->last_name ?? '')) ?: $ag->name; ?>
                        <div class="bar-row">
                            <div class="bar-row__meta">
                                <span><?php echo e($agName); ?></span>
                                <strong><?php echo e($ag->total); ?></strong>
                            </div>
                            <div class="bar-row__track">
                                <div class="bar-row__fill bar-row__fill--dark" data-width="<?php echo e(round($ag->total / $maxAgent * 100)); ?>"></div>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="panel">
                <div class="panel-header">
                    <div class="panel-header__title">
                        <span class="eyebrow">Synthese</span>
                        <h2 class="tw:font-semibold">Taux de traitement</h2>
                    </div>
                </div>
                <?php
                    $total   = $global['total'] ?: 1;
                    $txOk    = round($global['completed'] / $total * 100);
                    $txKo    = round($global['rejected']  / $total * 100);
                    $txPend  = round($global['pending']   / $total * 100);
                ?>
                <div class="bar-list">
                <?php $__currentLoopData = [
                    ['label' => 'Traitees', 'pct' => $txOk,   'class' => 'bar-row__fill--success'],
                    ['label' => 'Rejetees', 'pct' => $txKo,   'class' => 'bar-row__fill--danger'],
                    ['label' => 'En attente', 'pct' => $txPend,'class' => 'bar-row__fill--warning'],
                ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="bar-row">
                        <div class="bar-row__meta">
                            <span><?php echo e($row['label']); ?></span>
                            <strong><?php echo e($row['pct']); ?> %</strong>
                        </div>
                        <div class="bar-row__track">
                            <div class="bar-row__fill <?php echo e($row['class']); ?>" data-width="<?php echo e($row['pct']); ?>"></div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        </div>
    </section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('portal.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mairie_wp\backend-laravel\resources\views/portal/admin-stats.blade.php ENDPATH**/ ?>