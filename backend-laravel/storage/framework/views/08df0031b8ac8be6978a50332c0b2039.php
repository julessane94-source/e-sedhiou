

<?php $__env->startSection('content'); ?>
    <?php echo $__env->make('portal.components.admin-nav', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <section class="hero">
        <div class="hero__panel">
            <span class="eyebrow">Administration</span>
            <h1 class="tw:font-semibold">Gestion des agents et superviseurs</h1>
            <p class="muted">Inscrivez, modifiez et activez les agents et superviseurs du portail.</p>
        </div>

        <div class="panel">
            <div class="panel-header">
                <div class="panel-header__title">
                    <span class="eyebrow">Nouveau compte interne</span>
                    <h2 class="tw:font-semibold">Inscrire un agent ou un superviseur</h2>
                </div>
            </div>
            <form action="<?php echo e(route('portal.admin.agents.store')); ?>" method="post">
                <?php echo csrf_field(); ?>
                <div class="form-grid">
                    <div class="field">
                        <label for="ag-role">Role</label>
                        <select id="ag-role" name="role" required>
                            <option value="agent" <?php if(old('role', 'agent') === 'agent'): echo 'selected'; endif; ?>>Agent</option>
                            <option value="superviseur" <?php if(old('role') === 'superviseur'): echo 'selected'; endif; ?>>Superviseur</option>
                        </select>
                    </div>
                    <div class="field">
                        <label for="ag-fn">Prenom</label>
                        <input id="ag-fn" type="text" name="first_name" value="<?php echo e(old('first_name')); ?>" placeholder="Prenom">
                    </div>
                    <div class="field">
                        <label for="ag-ln">Nom</label>
                        <input id="ag-ln" type="text" name="last_name" value="<?php echo e(old('last_name')); ?>" placeholder="Nom">
                    </div>
                    <div class="field">
                        <label for="ag-email">Email <span class="muted">*</span></label>
                        <input id="ag-email" type="email" name="email" value="<?php echo e(old('email')); ?>" required>
                    </div>
                    <div class="field">
                        <label for="ag-pw">Mot de passe <span class="muted">*</span></label>
                        <input id="ag-pw" type="password" name="password" required autocomplete="new-password">
                    </div>
                </div>
                <button class="button button--primary tw:mt-3" type="submit">Inscrire le compte</button>
            </form>
        </div>
    </section>

    <section class="section">
        <div class="metric-strip tw:mb-5">
            <?php
                $agentsCount = $agents->where('role', 'agent')->count();
                $superviseursCount = $agents->where('role', 'superviseur')->count();
                $activeCount = $agents->where('is_active', true)->count();
                $inactiveCount = $agents->where('is_active', false)->count();
            ?>
            <?php $__currentLoopData = [
                ['label' => 'Comptes internes', 'val' => $agents->count(), 'class' => 'assigned'],
                ['label' => 'Agents', 'val' => $agentsCount, 'class' => 'processing'],
                ['label' => 'Superviseurs', 'val' => $superviseursCount, 'class' => 'pending'],
                ['label' => 'Actifs', 'val' => $activeCount . ' / ' . $inactiveCount . ' inactifs', 'class' => 'completed'],
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
                    <span class="eyebrow"><?php echo e($agents->count()); ?> compte(s)</span>
                    <h2 class="tw:font-semibold">Liste des agents et superviseurs</h2>
                </div>
                <span class="panel-header__meta">Cliquer sur un compte pour modifier ses parametres</span>
            </div>

            <?php if($agents->isEmpty()): ?>
                <p class="muted">Aucun compte interne enregistre.</p>
            <?php else: ?>
                <?php $__currentLoopData = $agents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $agent): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php $agentName = trim(($agent->first_name ?? '') . ' ' . ($agent->last_name ?? '')) ?: $agent->name; ?>
                    <details class="details-card">
                        <summary>
                            <span>
                                <?php echo e($agentName); ?> — <span class="muted"><?php echo e($agent->email); ?></span>
                            </span>
                            <span class="details-card__summary-meta">
                                <span class="badge assigned"><?php echo e($agent->role === 'superviseur' ? 'Superviseur' : 'Agent'); ?></span>
                                <span class="badge <?php echo e($agent->is_active ? 'completed' : 'rejected'); ?>">
                                    <?php echo e($agent->is_active ? 'Actif' : 'Inactif'); ?>

                                </span>
                            </span>
                        </summary>

                        <div class="details-card__content">
                            <form action="<?php echo e(route('portal.admin.agents.update', $agent)); ?>" method="post">
                                <?php echo csrf_field(); ?>
                                <div class="form-grid">
                                    <div class="field">
                                        <label>Prenom</label>
                                        <input type="text" name="first_name" value="<?php echo e($agent->first_name); ?>">
                                    </div>
                                    <div class="field">
                                        <label>Nom</label>
                                        <input type="text" name="last_name" value="<?php echo e($agent->last_name); ?>">
                                    </div>
                                    <div class="field">
                                        <label>Email</label>
                                        <input type="email" name="email" value="<?php echo e($agent->email); ?>" required>
                                    </div>
                                    <div class="field">
                                        <label>Nouveau mot de passe <span class="muted">(laisser vide)</span></label>
                                        <input type="password" name="password" autocomplete="new-password">
                                    </div>
                                </div>
                                <div class="field tw:mt-2.5">
                                    <label class="tw:flex tw:items-center tw:gap-2 tw:cursor-pointer">
                                        <input type="hidden" name="is_active" value="0">
                                        <input type="checkbox" name="is_active" value="1" <?php echo e($agent->is_active ? 'checked' : ''); ?>>
                                        Compte actif
                                    </label>
                                </div>
                                <div class="actions tw:mt-3">
                                    <button class="button button--accent" type="submit">Enregistrer</button>
                                </div>
                            </form>

                                <form action="<?php echo e(route('portal.admin.agents.delete', $agent)); ?>" method="post" class="tw:mt-2"
                                  onsubmit="return confirm('Supprimer cet agent ?');">
                                <?php echo csrf_field(); ?>
                                <button class="button button--danger" type="submit">Supprimer</button>
                            </form>
                        </div>
                    </details>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php endif; ?>
        </div>
    </section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('portal.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mairie_wp\backend-laravel\resources\views/portal/admin-agents.blade.php ENDPATH**/ ?>