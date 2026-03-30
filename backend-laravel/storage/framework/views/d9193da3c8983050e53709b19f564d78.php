

<?php $__env->startSection('content'); ?>
    <?php echo $__env->make('portal.components.admin-nav', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <section class="hero">
        <div class="hero__panel">
            <span class="eyebrow">Administration</span>
            <h1>Messagerie</h1>
            <p class="muted">Consultez tous les messages echanges entre citoyens et agents.</p>
        </div>

        <div class="panel">
            <span class="eyebrow">Vue d'ensemble</span>
            <h2>Centre de communication</h2>
            <div class="fact-grid">
                <div class="fact-card">
                    <strong>Messages WordPress</strong>
                    <span><?php echo e($wpContactMessages->total()); ?> element(s) synchronises depuis le site public.</span>
                </div>
                <div class="fact-card">
                    <strong>Echanges internes</strong>
                    <span><?php echo e($messages->total()); ?> conversation(s) visibles dans l'espace d'administration.</span>
                </div>
                <div class="fact-card">
                    <strong>Envoi manuel</strong>
                    <span>Composez un message vers un utilisateur et rattachez-le si besoin a un dossier.</span>
                </div>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="section-stack">
        <div class="panel">
            <div class="panel-header">
                <div class="panel-header__title">
                    <span class="eyebrow">Messages WordPress</span>
                    <h2>Messages recus depuis le formulaire de contact</h2>
                </div>
                <span class="panel-header__meta"><?php echo e($wpContactMessages->total()); ?> message(s)</span>
            </div>

            <?php if($wpContactMessages->isEmpty()): ?>
                <p class="muted">Aucun message WordPress synchronise pour le moment.</p>
            <?php else: ?>
                <div class="message-list tw:mb-[18px]">
                    <?php $__currentLoopData = $wpContactMessages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $contact): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $replyBadgeClass = match ($contact->reply_status) {
                                'sent' => 'completed',
                                'failed' => 'rejected',
                                default => 'pending',
                            };
                            $replyLabel = match ($contact->reply_status) {
                                'sent' => 'Repondu',
                                'failed' => 'Echec envoi',
                                default => 'En attente',
                            };
                        ?>
                        <article class="message">
                            <div class="message__meta tw:items-start tw:gap-2.5 tw:flex-wrap">
                                <span>
                                    <strong><?php echo e($contact->sender_name); ?></strong>
                                    — <a href="mailto:<?php echo e($contact->sender_email); ?>"><?php echo e($contact->sender_email); ?></a>
                                    <span class="badge <?php echo e($replyBadgeClass); ?> tw:text-[0.75em] tw:ml-2"><?php echo e($replyLabel); ?></span>
                                </span>
                                <span><?php echo e(optional($contact->received_at ?? $contact->created_at)->format('d/m/Y H:i')); ?></span>
                            </div>

                            <p class="tw:mb-2"><strong>Objet:</strong> <?php echo e($contact->subject ?: 'Sans objet'); ?></p>
                            <div class="tw:whitespace-pre-wrap"><?php echo e($contact->message); ?></div>

                            <?php if($contact->reply_status === 'sent'): ?>
                                <p class="muted tw:mt-2.5 tw:mb-0">
                                    Derniere reponse envoyee le <?php echo e(optional($contact->replied_at)->format('d/m/Y H:i')); ?>

                                    <?php if($contact->repliedBy): ?>
                                        par <?php echo e(trim(($contact->repliedBy->first_name ?? '') . ' ' . ($contact->repliedBy->last_name ?? '')) ?: $contact->repliedBy->name); ?>

                                    <?php endif; ?>
                                </p>
                            <?php endif; ?>

                            <?php if($contact->reply_status === 'failed' && $contact->reply_error): ?>
                                <p class="tw:text-red-600 tw:mt-2.5 tw:mb-0">Erreur: <?php echo e($contact->reply_error); ?></p>
                            <?php endif; ?>

                            <form action="<?php echo e(route('portal.admin.messages.wordpress.reply', $contact)); ?>" method="post" class="tw:mt-3">
                                <?php echo csrf_field(); ?>
                                <div class="form-grid">
                                    <div class="field field--full">
                                        <label>Objet de la reponse</label>
                                        <input type="text" name="subject" value="Re: <?php echo e($contact->subject ?: 'Votre message'); ?>">
                                    </div>
                                    <div class="field field--full">
                                        <label>Votre reponse</label>
                                        <textarea name="body" rows="4" required>Bonjour <?php echo e($contact->sender_name); ?>,

Merci pour votre message.

Cordialement,
Service mairie</textarea>
                                    </div>
                                </div>
                                <button class="button button--accent" type="submit">Repondre par email</button>
                            </form>
                        </article>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>

                <div class="tw:mb-[18px]"><?php echo e($wpContactMessages->links()); ?></div>
            <?php endif; ?>
        </div>

        <div class="panel">
            <div class="panel-header">
                <div class="panel-header__title">
                    <span class="eyebrow">Nouveau message</span>
                    <h2>Composer un message</h2>
                </div>
            </div>
            <form action="<?php echo e(route('portal.messages.store')); ?>" method="post">
                <?php echo csrf_field(); ?>
                <div class="form-grid">
                    <div class="field">
                        <label>Destinataire</label>
                        <select name="receiver_id" required>
                            <option value="">Choisir un utilisateur</option>
                            <?php $__currentLoopData = $messageUsers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    $userName = trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? '')) ?: $user->name;
                                    $userRole = match ($user->role) {
                                        'admin' => 'Admin',
                                        'agent' => 'Agent',
                                        'superviseur' => 'Superviseur',
                                        default => 'Citoyen',
                                    };
                                ?>
                                <option value="<?php echo e($user->id); ?>" <?php if(old('receiver_id') == $user->id): echo 'selected'; endif; ?>><?php echo e($userName); ?> — <?php echo e($userRole); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <?php $__errorArgs = ['receiver_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><small class="tw:text-xs tw:text-red-600"><?php echo e($message); ?></small><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <div class="field">
                        <label>Dossier lié (optionnel)</label>
                        <select name="demande_id">
                            <option value="">Aucun dossier</option>
                            <?php $__currentLoopData = $messageDemandes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $demande): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    $citizenName = trim(($demande->citoyen->first_name ?? '') . ' ' . ($demande->citoyen->last_name ?? '')) ?: ($demande->citoyen->name ?? 'Citoyen');
                                ?>
                                <option value="<?php echo e($demande->id); ?>" <?php if(old('demande_id') == $demande->id): echo 'selected'; endif; ?>><?php echo e($demande->reference); ?> — <?php echo e($citizenName); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <?php $__errorArgs = ['demande_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><small class="tw:text-xs tw:text-red-600"><?php echo e($message); ?></small><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <div class="field field--full">
                        <label>Message</label>
                        <textarea name="body" rows="4" required><?php echo e(old('body')); ?></textarea>
                        <?php $__errorArgs = ['body'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><small class="tw:text-xs tw:text-red-600"><?php echo e($message); ?></small><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                </div>
                <button class="button button--primary" type="submit">Envoyer le message</button>
            </form>
        </div>

        <div class="panel">
            <div class="panel-header">
                <div class="panel-header__title">
                    <span class="eyebrow"><?php echo e($messages->total()); ?> message(s)</span>
                    <h2>Tous les echanges</h2>
                </div>
            </div>

            <?php if($messages->isEmpty()): ?>
                <p class="muted">Aucun message.</p>
            <?php else: ?>
                <div class="message-list">
                    <?php $__currentLoopData = $messages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $msg): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $senderName = trim(($msg->sender->first_name ?? '') . ' ' . ($msg->sender->last_name ?? '')) ?: ($msg->sender->name ?? 'Utilisateur');
                            $receiverName = trim(($msg->receiver->first_name ?? '') . ' ' . ($msg->receiver->last_name ?? '')) ?: ($msg->receiver->name ?? 'Utilisateur');
                            $roleLabel  = match ($msg->sender->role ?? '') {
                                'admin'   => 'Admin',
                                'agent'   => 'Agent',
                                'superviseur' => 'Superviseur',
                                default   => 'Citoyen',
                            };
                            $receiverRoleLabel = match ($msg->receiver->role ?? '') {
                                'admin'   => 'Admin',
                                'agent'   => 'Agent',
                                'superviseur' => 'Superviseur',
                                default   => 'Citoyen',
                            };
                        ?>
                        <article class="message">
                            <div class="message__meta">
                                <span>
                                    <strong><?php echo e($senderName); ?></strong>
                                    <span class="badge <?php echo e($msg->sender->role ?? 'pending'); ?> tw:text-[0.75em] tw:ml-1.5"><?php echo e($roleLabel); ?></span>
                                    →
                                    <strong><?php echo e($receiverName); ?></strong>
                                    <span class="badge <?php echo e($msg->receiver->role ?? 'pending'); ?> tw:text-[0.75em] tw:ml-1.5"><?php echo e($receiverRoleLabel); ?></span>
                                    <?php if($msg->demande): ?>
                                        — dossier <a href="<?php echo e(route('portal.demandes.show', $msg->demande)); ?>"><?php echo e($msg->demande->reference); ?></a>
                                    <?php endif; ?>
                                </span>
                                <span><?php echo e(optional($msg->created_at)->format('d/m/Y H:i')); ?></span>
                            </div>
                            <div><?php echo e($msg->body); ?></div>
                        </article>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>

                <div class="tw:mt-4"><?php echo e($messages->links()); ?></div>
            <?php endif; ?>
        </div>
        </div>
    </section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('portal.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mairie_wp\backend-laravel\resources\views/portal/admin-messages.blade.php ENDPATH**/ ?>