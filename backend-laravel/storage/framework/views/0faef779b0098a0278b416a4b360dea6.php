

<?php $__env->startSection('content'); ?>
    <?php
        $statusClassMap = [
            'pending' => 'pending',
            'assigned' => 'assigned',
            'processing' => 'processing',
            'completed' => 'completed',
            'rejected' => 'rejected',
        ];

        $ownerName = trim(($demande->citoyen->first_name ?? '') . ' ' . ($demande->citoyen->last_name ?? '')) ?: ($demande->citoyen->name ?? 'Usager');
        $agentName = trim(($demande->agent->first_name ?? '') . ' ' . ($demande->agent->last_name ?? '')) ?: ($demande->agent->name ?? 'Non assigne');
        $backUrl = $currentUser->isAdmin() ? route('portal.admin') : ($currentUser->isAgent() ? route('portal.agent') : route('portal.citizen'));
        $processingChannel = $demande->processing_channel ?? 'counter';
        $hasProcessedDocument = ! empty($demande->processed_document_path);
    ?>

    <section class="hero">
        <div class="hero__panel">
            <span class="eyebrow">Dossier</span>
            <h1><?php echo e($demande->reference); ?></h1>
            <p class="muted"><?php echo e($requestTypes[$demande->request_type] ?? $demande->request_type); ?> - ouvert le <?php echo e(optional($demande->created_at)->format('d/m/Y H:i')); ?></p>
            <div class="actions tw:mt-[18px]">
                <a class="button button--ghost" href="<?php echo e($backUrl); ?>">Retour a mon espace</a>
                <span class="badge <?php echo e($statusClassMap[$demande->status] ?? 'pending'); ?>"><?php echo e($statusLabels[$demande->status] ?? $demande->status); ?></span>
            </div>
        </div>

        <div class="panel">
            <span class="eyebrow">Rattachement</span>
            <h2>Acteurs du dossier</h2>
            <ul class="list">
                <li><strong>Citoyen</strong><br><span class="muted"><?php echo e($ownerName); ?> - <?php echo e($demande->email); ?></span></li>
                <?php if($demande->citoyen?->citizen_number): ?>
                    <li><strong>N°CIT</strong><br><span class="muted"><?php echo e($demande->citoyen->citizen_number); ?></span></li>
                <?php endif; ?>
                <li><strong>Agent</strong><br><span class="muted"><?php echo e($agentName); ?></span></li>
                <li><strong>Source</strong><br><span class="muted"><?php echo e($demande->source); ?></span></li>
            </ul>
        </div>
    </section>

    <section class="grid grid--2 section">
        <div class="panel">
            <span class="eyebrow">Informations demandees</span>
            <h2>Details du dossier</h2>
            <ul class="list">
                <li><strong>Date de naissance</strong><br><span class="muted"><?php echo e(optional($demande->birth_date)->format('d/m/Y')); ?></span></li>
                <li><strong>Lieu de naissance</strong><br><span class="muted"><?php echo e($demande->birth_place); ?></span></li>
                <li><strong>Numero de registre</strong><br><span class="muted"><?php echo e($demande->register_number); ?></span></li>
                <li><strong>Adresse</strong><br><span class="muted"><?php echo e($demande->address); ?></span></li>
                <li><strong>Paiement</strong><br><span class="muted"><?php echo e($demande->payment_status === 'paid' ? 'Regle' : 'En attente'); ?> - <?php echo e(number_format($paymentAmount, 0, ',', ' ')); ?> FCFA</span></li>
                <?php if($demande->payment_status === 'paid'): ?>
                    <li><strong>Reference paiement</strong><br><span class="muted"><?php echo e($demande->payment_reference); ?> <?php if($demande->paid_at): ?> le <?php echo e($demande->paid_at->format('d/m/Y H:i')); ?> <?php endif; ?></span></li>
                <?php endif; ?>
                <li><strong>Parent 1</strong><br><span class="muted"><?php echo e($demande->parent_one_first_name); ?> <?php echo e($demande->parent_one_last_name); ?></span></li>
                <li><strong>Parent 2</strong><br><span class="muted"><?php echo e($demande->parent_two_first_name); ?> <?php echo e($demande->parent_two_last_name); ?></span></li>
                <?php if($demande->details): ?>
                    <li><strong>Precisions</strong><br><span class="muted"><?php echo e($demande->details); ?></span></li>
                <?php endif; ?>
                <?php if($demande->agent_notes): ?>
                    <li><strong>Notes de traitement</strong><br><span class="muted"><?php echo e($demande->agent_notes); ?></span></li>
                <?php endif; ?>
                <li><strong>Mode de traitement</strong><br><span class="muted"><?php echo e($processingChannel === 'online' ? 'En ligne' : 'Au guichet'); ?></span></li>
                <?php if($hasProcessedDocument && $demande->status === 'completed' && $processingChannel === 'online'): ?>
                    <li>
                        <strong>Document traite</strong><br>
                        <a class="button button--ghost" href="<?php echo e(route('portal.demandes.document.download', $demande)); ?>">Telecharger le document final</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>

        <div class="panel">
            <span class="eyebrow">Messagerie</span>
            <h2>Echanges sur le dossier</h2>
            <?php if($demande->messages->isEmpty()): ?>
                <p class="muted">Aucun message pour le moment.</p>
            <?php else: ?>
                <div class="message-list">
                    <?php $__currentLoopData = $demande->messages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $message): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <article class="message">
                            <div class="message__meta">
                                <strong><?php echo e(trim(($message->sender->first_name ?? '') . ' ' . ($message->sender->last_name ?? '')) ?: ($message->sender->name ?? 'Utilisateur')); ?></strong>
                                <span><?php echo e(optional($message->created_at)->format('d/m/Y H:i')); ?></span>
                            </div>
                            <div><?php echo e($message->body); ?></div>
                        </article>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            <?php endif; ?>

            <form action="<?php echo e(route('portal.demandes.messages.store', $demande)); ?>" method="post" class="tw:mt-[18px]">
                <?php echo csrf_field(); ?>
                <div class="field">
                    <label for="message-body">Nouveau message</label>
                    <textarea id="message-body" name="body" required><?php echo e(old('body')); ?></textarea>
                </div>
                <button class="button button--primary" type="submit">Envoyer</button>
            </form>

            <?php if($currentUser->isCitoyen() && $demande->payment_status !== 'paid'): ?>
                <form action="<?php echo e(route('portal.citizen.demandes.pay', $demande)); ?>" method="post" class="tw:mt-3">
                    <?php echo csrf_field(); ?>
                    <button class="button button--accent" type="submit">Payer <?php echo e(number_format($paymentAmount, 0, ',', ' ')); ?> FCFA</button>
                </form>
            <?php endif; ?>

            <?php if($currentUser->isCitoyen() && $demande->status === 'completed' && $processingChannel === 'online' && $hasProcessedDocument): ?>
                <div class="tw:mt-3">
                    <a class="button button--primary" href="<?php echo e(route('portal.demandes.document.download', $demande)); ?>">Telecharger mon document traite</a>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <?php if($currentUser->isAgent() || $currentUser->isAdmin()): ?>
        <section class="grid grid--2 section">
            <div class="panel">
                <span class="eyebrow">Affectation</span>
                <h2>Prise en charge</h2>
                <form action="<?php echo e(route('portal.demandes.assign', $demande)); ?>" method="post">
                    <?php echo csrf_field(); ?>
                    <?php if($currentUser->isAdmin()): ?>
                        <div class="field">
                            <label for="agent_id">Assigner a un agent</label>
                            <select id="agent_id" name="agent_id" required>
                                <?php $__currentLoopData = $agentOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $agentOption): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php
                                        $agentOptionName = trim(($agentOption->first_name ?? '') . ' ' . ($agentOption->last_name ?? '')) ?: $agentOption->name;
                                    ?>
                                    <option value="<?php echo e($agentOption->id); ?>" <?php if((int) $demande->agent_id === (int) $agentOption->id): echo 'selected'; endif; ?>><?php echo e($agentOptionName); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                    <?php else: ?>
                        <p class="muted">Attribuez ce dossier a votre compte pour le sortir de la file d attente.</p>
                    <?php endif; ?>
                    <button class="button button--accent" type="submit">Assigner le dossier</button>
                </form>
            </div>

            <div class="panel">
                <span class="eyebrow">Traitement</span>
                <h2>Mettre a jour le statut</h2>
                <form action="<?php echo e(route('portal.demandes.process', $demande)); ?>" method="post" enctype="multipart/form-data">
                    <?php echo csrf_field(); ?>
                    <div class="field">
                        <label for="demande-status">Statut</label>
                        <select id="demande-status" name="status" required>
                            <option value="processing" <?php if($demande->status === 'processing'): echo 'selected'; endif; ?>>En cours</option>
                            <option value="completed" <?php if($demande->status === 'completed'): echo 'selected'; endif; ?>>Traitee</option>
                            <option value="rejected" <?php if($demande->status === 'rejected'): echo 'selected'; endif; ?>>Rejetee</option>
                        </select>
                    </div>
                    <div class="field">
                        <label for="processing_channel">Mode de traitement</label>
                        <select id="processing_channel" name="processing_channel" required>
                            <option value="counter" <?php if(old('processing_channel', $processingChannel) === 'counter'): echo 'selected'; endif; ?>>Traitement au guichet</option>
                            <option value="online" <?php if(old('processing_channel', $processingChannel) === 'online'): echo 'selected'; endif; ?>>Traitement en ligne (telechargement citoyen)</option>
                        </select>
                        <small class="muted">Le traitement en ligne necessite une signature numerique du maire configuree dans Parametres.</small>
                    </div>
                    <div class="field">
                        <label for="processed_document">Document final traite (PDF ou Word)</label>
                        <input id="processed_document" type="file" name="processed_document" accept=".pdf,.doc,.docx">
                        <?php if($hasProcessedDocument): ?>
                            <small class="muted">Un document est deja disponible: <a href="<?php echo e(route('portal.demandes.document.download', $demande)); ?>">telecharger l actuel</a></small>
                        <?php endif; ?>
                    </div>
                    <?php if(! $hasMayorSignature): ?>
                        <p class="muted">Aucune signature numerique du maire n est configuree pour le traitement en ligne.</p>
                    <?php endif; ?>
                    <div class="field">
                        <label for="agent_notes">Notes agent</label>
                        <textarea id="agent_notes" name="agent_notes"><?php echo e(old('agent_notes', $demande->agent_notes)); ?></textarea>
                    </div>
                    <button class="button button--primary" type="submit">Enregistrer</button>
                </form>
            </div>
        </section>
    <?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('portal.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mairie_wp\backend-laravel\resources\views/portal/demande.blade.php ENDPATH**/ ?>