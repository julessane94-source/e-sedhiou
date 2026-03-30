

<?php $__env->startSection('content'); ?>
<?php
    $statusClassMap = [
        'pending'    => 'pending',
        'assigned'   => 'assigned',
        'processing' => 'processing',
        'completed'  => 'completed',
        'rejected'   => 'rejected',
    ];

    if ($role === 'citoyen') {
        $roleTitle = 'Espace citoyen';
        $roleLead = '';
        $dashboardHighlights = [
            ['label' => 'Dossiers', 'value' => $stats['total'], 'meta' => 'Total de demandes deposees'],
            ['label' => 'Messages recus', 'value' => $receivedMessages->count(), 'meta' => 'Echanges en attente de lecture'],
            ['label' => 'Paiements', 'value' => $paymentStats['unpaid'], 'meta' => 'Dossiers restant a regler'],
        ];
    } elseif ($role === 'agent') {
        $roleTitle = 'Espace agent';
        $roleLead = '';
        $dashboardHighlights = [
            ['label' => 'A traiter', 'value' => $pendingDemandes->count(), 'meta' => 'Dossiers encore sans prise en charge'],
            ['label' => 'Paiements', 'value' => $paymentValidationStats['pending'], 'meta' => 'Validations en attente'],
            ['label' => 'Performance', 'value' => $agentPerformance['completion_rate'] . '%', 'meta' => 'Taux global de traitement'],
        ];
    } else {
        $roleTitle = 'Administration';
        $roleLead = '';
        $dashboardHighlights = [
            ['label' => 'Utilisateurs', 'value' => $userCounts['total'], 'meta' => 'Comptes actifs sur le portail'],
            ['label' => 'Demandes', 'value' => $demandeCounts['total'], 'meta' => 'Volume global de dossiers'],
            ['label' => 'Messages recus', 'value' => $adminReceivedMessages->count(), 'meta' => 'Conversations a suivre'],
        ];
    }
?>


<section class="dashboard-hero panel tw:rounded-3xl tw:border tw:border-emerald-800 tw:bg-emerald-700 tw:text-white tw:shadow-xl tw:p-5 lg:tw:p-7">
    <div class="welcome-bar welcome-bar--hero tw:flex tw:flex-col md:tw:flex-row tw:justify-between tw:gap-4">
        <div>
            <div class="welcome-bar__date tw:text-emerald-100"><?php echo e(now()->translatedFormat('l d F Y')); ?></div>
            <h1 class="welcome-bar__name tw:text-sm tw:font-medium tw:text-white">Bonjour, <?php echo e($currentUser->first_name ?: ($currentUser->name ?: $currentUser->email)); ?></h1>
            <div class="welcome-bar__meta tw:flex tw:flex-wrap tw:items-center tw:gap-2">
                <span class="role-badge role-badge--<?php echo e($role); ?>"><?php echo e($roleTitle); ?></span>
                <span class="muted tw:text-xs md:tw:text-sm tw:text-emerald-100"><?php echo e($currentUser->email); ?></span>
                <span class="pill tw:rounded-full tw:border tw:border-amber-300 tw:bg-amber-100 tw:px-3 tw:py-1 tw:font-semibold">Tableau de bord securise</span>
            </div>
        </div>
        <div class="welcome-bar__actions tw:flex tw:flex-wrap tw:items-center tw:gap-2">
            <a class="button button--ghost tw:inline-flex tw:items-center tw:justify-center tw:px-4 tw:py-2.5 tw:rounded-full" href="<?php echo e($wordpressUrl); ?>/">Retour site mairie</a>
            <form action="<?php echo e(route('portal.logout')); ?>" method="post">
                <?php echo csrf_field(); ?>
                <button class="button button--danger tw:inline-flex tw:items-center tw:justify-center tw:px-4 tw:py-2.5 tw:rounded-full" type="submit">Déconnexion</button>
            </form>
        </div>
    </div>
    <div class="dashboard-hero__foot">
        
        <div class="dashboard-summary tw:grid tw:grid-cols-1 md:tw:grid-cols-3 tw:gap-3 tw:mt-4">
            <?php $__currentLoopData = $dashboardHighlights; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $highlight): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <article class="dashboard-summary__card tw:rounded-lg tw:border tw:border-emerald-600 tw:bg-emerald-50 tw:shadow-sm tw:p-4">
                    <span class="dashboard-summary__label tw:text-xs tw:text-emerald-800 tw:font-semibold"><?php echo e($highlight['label']); ?></span>
                    <strong class="tw:text-xl md:tw:text-2xl tw:font-bold tw:text-emerald-900"><?php echo e($highlight['value']); ?></strong>
                    
                </article>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
</section>


<section class="dashboard-stage tw:mt-5 tw:rounded-3xl tw:border tw:border-emerald-200/60 tw:bg-gray-50 tw:p-4 lg:tw:p-6">


<?php if($role === 'citoyen'): ?>

    
    <div class="tw:mb-4 tw:flex tw:justify-end">
        <a href="<?php echo e(route('portal.citoyen.recensement')); ?>" class="button button--success tw:rounded-full tw:px-5 tw:py-2 tw:bg-success tw:text-white tw:font-semibold hover:tw:bg-success" title="Enregistrez-vous ou aidez un citoyen à s'enregistrer auprès de la mairie">
            📝 Me faire connaître de la mairie
        </a>
    </div>

<nav class="tab-nav tw:flex tw:flex-wrap tw:gap-2 tw:mb-5 tw:bg-gray-50">
    <button class="tab-btn tw:rounded-full tw:px-4 tw:py-2 tw:text-sm tw:font-medium" data-tab="accueil">🏠 Accueil</button>
    <button class="tab-btn tw:rounded-full tw:px-4 tw:py-2 tw:text-sm tw:font-medium" data-tab="dossiers">
        📁 Mes dossiers
        <?php if($stats['total'] > 0): ?><span class="tab-badge"><?php echo e($stats['total']); ?></span><?php endif; ?>
    </button>
    <button class="tab-btn tw:rounded-full tw:px-4 tw:py-2 tw:text-sm tw:font-medium" data-tab="nouvelle-demande">✚ Nouvelle demande</button>
    <button class="tab-btn tw:rounded-full tw:px-4 tw:py-2 tw:text-sm tw:font-medium" data-tab="citoyennete">🏛️ Citoyenneté</button>
    <button class="tab-btn tw:rounded-full tw:px-4 tw:py-2 tw:text-sm tw:font-medium" data-tab="messages">
        ✉ Messages
        <?php if($receivedMessages->count() > 0): ?><span class="tab-badge"><?php echo e($receivedMessages->count()); ?></span><?php endif; ?>
    </button>
    <button class="tab-btn tw:rounded-full tw:px-4 tw:py-2 tw:text-sm tw:font-medium" data-tab="profil">👤 Mon profil</button>
</nav>


<div class="tab-pane" data-pane="accueil">
    <div class="kpi-row tw:grid tw:grid-cols-2 lg:tw:grid-cols-3 tw:gap-3">
        <div class="kpi kpi--brand tw:bg-white tw:shadow-sm tw:rounded-lg">
            <span class="kpi__icon">📁</span>
            <span class="kpi__value tw:text-xl md:tw:text-2xl tw:font-bold"><?php echo e($stats['total']); ?></span>
            <span class="kpi__label tw:text-xs tw:text-gray-500 tw:font-medium">Dossiers déposés</span>
        </div>
        <div class="kpi kpi--warn tw:bg-white tw:shadow-sm tw:rounded-lg">
            <span class="kpi__icon">⏳</span>
            <span class="kpi__value tw:text-xl md:tw:text-2xl tw:font-bold"><?php echo e($stats['pending']); ?></span>
            <span class="kpi__label tw:text-xs tw:text-gray-500 tw:font-medium">En attente</span>
        </div>
        <div class="kpi kpi--accent tw:bg-white tw:shadow-sm tw:rounded-lg">
            <span class="kpi__icon">⚡</span>
            <span class="kpi__value tw:text-xl md:tw:text-2xl tw:font-bold"><?php echo e($stats['processing']); ?></span>
            <span class="kpi__label tw:text-xs tw:text-gray-500 tw:font-medium">En traitement</span>
        </div>
        <div class="kpi kpi--success tw:bg-white tw:shadow-sm tw:rounded-lg">
            <span class="kpi__icon">✅</span>
            <span class="kpi__value tw:text-xl md:tw:text-2xl tw:font-bold"><?php echo e($stats['completed']); ?></span>
            <span class="kpi__label tw:text-xs tw:text-gray-500 tw:font-medium">Finalisés</span>
        </div>
        <div class="kpi kpi--danger tw:bg-white tw:shadow-sm tw:rounded-lg">
            <span class="kpi__icon">💳</span>
            <span class="kpi__value tw:text-xl md:tw:text-2xl tw:font-bold"><?php echo e($paymentStats['unpaid']); ?></span>
            <span class="kpi__label tw:text-xs tw:text-gray-500 tw:font-medium">À payer</span>
        </div>
        <div class="kpi kpi--success tw:bg-white tw:shadow-sm tw:rounded-lg">
            <span class="kpi__icon">✔</span>
            <span class="kpi__value tw:text-xl md:tw:text-2xl tw:font-bold"><?php echo e($paymentStats['paid']); ?></span>
            <span class="kpi__label tw:text-xs tw:text-gray-500 tw:font-medium">Paiements réglés</span>
        </div>
    </div>

    <div class="sec-header tw:flex tw:flex-wrap tw:items-center tw:justify-between tw:gap-3 tw:mt-5">
        <h2>Derniers dossiers</h2>
        <button class="button button--ghost tw:rounded-full tw:px-4 tw:py-2" data-goto-tab="dossiers">Voir tout →</button>
    </div>

    <?php if($demandes->isEmpty()): ?>
        <div class="panel tw:text-center tw:rounded-2xl tw:border tw:border-emerald-200/60 tw:bg-white/90 tw:p-8">
            <div class="tw:text-4xl tw:mb-3">📋</div>
            <p class="muted tw:mb-4">Aucun dossier pour le moment.</p>
            <button class="button button--primary tw:rounded-full tw:px-5 tw:py-2.5" data-goto-tab="nouvelle-demande">Déposer mon premier dossier →</button>
        </div>
    <?php else: ?>
        <div class="grid grid--2 tw:grid-cols-1 md:tw:grid-cols-2 tw:gap-3">
            <?php $__currentLoopData = $demandes->take(6); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $demande): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="panel tw:rounded-2xl tw:border tw:border-emerald-200/60 tw:bg-white/95 tw:p-4">
                    <div class="split">
                        <div>
                            <strong><?php echo e($demande->reference); ?></strong><br>
                            <span class="muted tw:text-sm"><?php echo e($requestTypes[$demande->request_type] ?? $demande->request_type); ?> · <?php echo e(optional($demande->created_at)->format('d/m/Y')); ?></span>
                        </div>
                        <span class="badge <?php echo e($statusClassMap[$demande->status] ?? 'pending'); ?>"><?php echo e($statusLabels[$demande->status] ?? $demande->status); ?></span>
                    </div>
                    <div class="actions tw:mt-2.5 tw:flex tw:flex-wrap tw:gap-2">
                        <a class="button button--ghost tw:rounded-full tw:px-4 tw:py-2" href="<?php echo e(route('portal.demandes.show', $demande)); ?>">Voir</a>
                        <?php if($demande->payment_status !== 'paid'): ?>
                            <form action="<?php echo e(route('portal.citizen.demandes.pay', $demande)); ?>" method="post">
                                <?php echo csrf_field(); ?>
                                <button class="button button--accent tw:rounded-full tw:px-4 tw:py-2" type="submit">Payer</button>
                            </form>
                        <?php else: ?>
                            <span class="badge completed">Payé ✓</span>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    <?php endif; ?>
</div>


<div class="tab-pane" data-pane="dossiers">
    <div class="sec-header tw:flex tw:flex-wrap tw:items-center tw:justify-between tw:gap-3">
        <h2>Tous mes dossiers</h2>
        <button class="button button--primary tw:rounded-full tw:px-4 tw:py-2" data-goto-tab="nouvelle-demande">+ Nouvelle demande</button>
    </div>
    <?php if($demandes->isEmpty()): ?>
        <p class="muted">Aucun dossier déposé.</p>
    <?php else: ?>
        <div class="grid tw:gap-3">
            <?php $__currentLoopData = $demandes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $demande): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="panel tw:rounded-2xl tw:border tw:border-emerald-200/60 tw:bg-white/95 tw:p-4">
                    <div class="split">
                        <div>
                            <strong><?php echo e($demande->reference); ?></strong>
                            <span class="muted tw:ml-2 tw:text-sm"><?php echo e($requestTypes[$demande->request_type] ?? $demande->request_type); ?></span><br>
                            <span class="muted tw:text-sm">
                                Déposé le <?php echo e(optional($demande->created_at)->format('d/m/Y')); ?>

                                <?php if($demande->agent): ?> · Agent : <?php echo e(trim(($demande->agent->first_name ?? '') . ' ' . ($demande->agent->last_name ?? '')) ?: $demande->agent->name); ?><?php endif; ?>
                            </span>
                        </div>
                        <span class="badge <?php echo e($statusClassMap[$demande->status] ?? 'pending'); ?>"><?php echo e($statusLabels[$demande->status] ?? $demande->status); ?></span>
                    </div>
                    <div class="actions tw:mt-3 tw:flex tw:flex-wrap tw:gap-2">
                        <a class="button button--ghost tw:rounded-full tw:px-4 tw:py-2" href="<?php echo e(route('portal.demandes.show', $demande)); ?>">Voir le dossier</a>
                        <?php if($demande->payment_status !== 'paid'): ?>
                            <form action="<?php echo e(route('portal.citizen.demandes.pay', $demande)); ?>" method="post">
                                <?php echo csrf_field(); ?>
                                <button class="button button--accent tw:rounded-full tw:px-4 tw:py-2" type="submit">Payer</button>
                            </form>
                        <?php else: ?>
                            <span class="badge completed">Paiement confirmé</span>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    <?php endif; ?>
</div>


<div class="tab-pane" data-pane="nouvelle-demande">
    <div class="sec-header"><h2>Déposer un nouveau dossier</h2></div>
    <div class="panel tw:rounded-2xl tw:border tw:border-emerald-200/60 tw:bg-white/95 tw:max-w-3xl">
        <form action="<?php echo e(route('portal.citizen.demandes.store')); ?>" method="post" enctype="multipart/form-data" class="tw:space-y-2">
            <?php echo csrf_field(); ?>
            <div class="form-grid tw:gap-4">
                <div class="field">
                    <label for="request_type">Type de demande</label>
                    <select id="request_type" name="request_type" required class="tw:rounded-xl tw:border-emerald-200 tw:bg-white tw:px-3 tw:py-2.5 focus:tw:ring-2 focus:tw:ring-emerald-400/40">
                        <option value="">Sélectionner…</option>
                        <?php $__currentLoopData = $requestTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($k); ?>" <?php if(old('request_type') === $k): echo 'selected'; endif; ?>><?php echo e($v); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="field">
                    <label for="for_other_person">Cette demande concerne</label>
                    <select id="for_other_person" name="for_other_person" class="tw:rounded-xl tw:border-emerald-200 tw:bg-white tw:px-3 tw:py-2.5 focus:tw:ring-2 focus:tw:ring-emerald-400/40">
                        <option value="0" <?php if((string) old('for_other_person', '0') === '0'): echo 'selected'; endif; ?>>Moi-meme</option>
                        <option value="1" <?php if((string) old('for_other_person') === '1'): echo 'selected'; endif; ?>>Une autre personne</option>
                    </select>
                    <?php $__errorArgs = ['for_other_person'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><small class="tw:text-xs tw:text-red-600"><?php echo e($message); ?></small><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div class="field">
                    <label for="representative_link">Lien avec la personne (si tiers)</label>
                    <input id="representative_link" name="representative_link" type="text" value="<?php echo e(old('representative_link')); ?>" placeholder="Ex: pere, mere, conjoint, tuteur" class="tw:rounded-xl tw:border-emerald-200 tw:bg-white tw:px-3 tw:py-2.5 focus:tw:ring-2 focus:tw:ring-emerald-400/40">
                    <?php $__errorArgs = ['representative_link'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><small class="tw:text-xs tw:text-red-600"><?php echo e($message); ?></small><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div class="field">
                    <label>Email</label>
                    <input name="email" type="email" value="<?php echo e(old('email', $currentUser->email)); ?>" required class="tw:rounded-xl tw:border-emerald-200 tw:bg-white tw:px-3 tw:py-2.5 focus:tw:ring-2 focus:tw:ring-emerald-400/40">
                </div>
                <div class="field">
                    <label>Prénom</label>
                    <input name="first_name" type="text" value="<?php echo e(old('first_name', $currentUser->first_name)); ?>" required class="tw:rounded-xl tw:border-emerald-200 tw:bg-white tw:px-3 tw:py-2.5 focus:tw:ring-2 focus:tw:ring-emerald-400/40">
                </div>
                <div class="field">
                    <label>Nom</label>
                    <input name="last_name" type="text" value="<?php echo e(old('last_name', $currentUser->last_name)); ?>" required class="tw:rounded-xl tw:border-emerald-200 tw:bg-white tw:px-3 tw:py-2.5 focus:tw:ring-2 focus:tw:ring-emerald-400/40">
                </div>
                <div class="field">
                    <label>Date de naissance</label>
                    <input name="birth_date" type="date" value="<?php echo e(old('birth_date', optional($currentUser->birth_date)->format('Y-m-d'))); ?>" required class="tw:rounded-xl tw:border-emerald-200 tw:bg-white tw:px-3 tw:py-2.5 focus:tw:ring-2 focus:tw:ring-emerald-400/40">
                </div>
                <div class="field">
                    <label>Lieu de naissance</label>
                    <input name="birth_place" type="text" value="<?php echo e(old('birth_place')); ?>" required class="tw:rounded-xl tw:border-emerald-200 tw:bg-white tw:px-3 tw:py-2.5 focus:tw:ring-2 focus:tw:ring-emerald-400/40">
                </div>
                <div class="field">
                    <label>N° de registre</label>
                    <input name="register_number" type="text" value="<?php echo e(old('register_number', $currentUser->register_number)); ?>" required class="tw:rounded-xl tw:border-emerald-200 tw:bg-white tw:px-3 tw:py-2.5 focus:tw:ring-2 focus:tw:ring-emerald-400/40">
                </div>
                <div class="field field--full">
                    <label>Adresse</label>
                    <textarea name="address" required class="tw:rounded-xl tw:border-emerald-200 tw:bg-white tw:px-3 tw:py-2.5 focus:tw:ring-2 focus:tw:ring-emerald-400/40"><?php echo e(old('address', $currentUser->address)); ?></textarea>
                </div>
                <div class="field">
                    <label>👨 Prénom du père</label>
                    <input name="parent_one_first_name" type="text" value="<?php echo e(old('parent_one_first_name')); ?>" required class="tw:rounded-xl tw:border-emerald-200 tw:bg-white tw:px-3 tw:py-2.5 focus:tw:ring-2 focus:tw:ring-emerald-400/40">
                </div>
                <div class="field">
                    <label>👨 Nom du père</label>
                    <input name="parent_one_last_name" type="text" value="<?php echo e(old('parent_one_last_name')); ?>" required class="tw:rounded-xl tw:border-emerald-200 tw:bg-white tw:px-3 tw:py-2.5 focus:tw:ring-2 focus:tw:ring-emerald-400/40">
                </div>
                <div class="field">
                    <label>👩 Prénom de la mère</label>
                    <input name="parent_two_first_name" type="text" value="<?php echo e(old('parent_two_first_name')); ?>" required class="tw:rounded-xl tw:border-emerald-200 tw:bg-white tw:px-3 tw:py-2.5 focus:tw:ring-2 focus:tw:ring-emerald-400/40">
                </div>
                <div class="field">
                    <label>👩 Nom de la mère</label>
                    <input name="parent_two_last_name" type="text" value="<?php echo e(old('parent_two_last_name')); ?>" required class="tw:rounded-xl tw:border-emerald-200 tw:bg-white tw:px-3 tw:py-2.5 focus:tw:ring-2 focus:tw:ring-emerald-400/40">
                </div>
                <div class="field field--full">
                    <label>Précisions supplémentaires</label>
                    <textarea name="details" class="tw:rounded-xl tw:border-emerald-200 tw:bg-white tw:px-3 tw:py-2.5 focus:tw:ring-2 focus:tw:ring-emerald-400/40"><?php echo e(old('details')); ?></textarea>
                </div>
                <div class="field field--full">
                    <label><i class="bi bi-file-earmark-pdf me-1"></i>📎 Fichiers/Pièces jointes (optionnel)</label>
                    <input name="attachment" type="file" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" class="tw:rounded-xl tw:border-emerald-200 tw:bg-white tw:px-3 tw:py-2.5 focus:tw:ring-2 focus:tw:ring-emerald-400/40">
                    <small class="tw:text-xs tw:text-gray-600">Formats acceptés: PDF, Word, Images. Maximum 10 MB</small>
                    <?php $__errorArgs = ['attachment'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><small class="tw:text-xs tw:text-red-600"><?php echo e($message); ?></small><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
            </div>
            <button class="button button--primary tw:rounded-full tw:px-5 tw:py-2.5" type="submit">Envoyer le dossier</button>
        </form>
    </div>
</div>


<div class="tab-pane" data-pane="citoyennete">
    <div class="sec-header tw:mb-5">
        <h2>🏛️ Citoyenneté et Patriotisme</h2>
        <p class="muted tw:mt-2">Développez vos connaissances civiques et participez aux activités de la mairie</p>
        <?php if($role === 'admin'): ?>
            <a href="<?php echo e(route('portal.admin.civic_courses')); ?>" class="button button--primary tw:text-xs tw:mt-3 tw:inline-flex tw:items-center">
                <i class="bi bi-gear me-1"></i>Gérer les cours
            </a>
        <?php endif; ?>
    </div>

    
    <h3 class="tw:font-bold tw:text-lg tw:mb-4">📚 Cours de citoyenneté</h3>
    <?php if($civicCourses && $civicCourses->count() > 0): ?>
        <div class="grid tw:grid-cols-1 lg:tw:grid-cols-2 tw:gap-4 tw:mb-6">
            <?php $__currentLoopData = $civicCourses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $course): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="panel tw:rounded-2xl tw:border tw:border-emerald-200/60 tw:bg-gradient-to-br tw:from-emerald-50 tw:to-white tw:shadow-sm tw:p-6 hover:tw:shadow-md tw:transition-all">
                    <div class="tw:flex tw:items-start tw:justify-between tw:mb-3">
                        <div class="tw:text-4xl"><?php echo e($course->icon_emoji ?? '📖'); ?></div>
                        <span class="badge tw:rounded-full tw:bg-emerald-100 tw:text-emerald-800 tw:text-xs tw:font-medium tw:px-2 tw:py-1">
                            <?php if($course->course_type === 'online'): ?> En ligne
                            <?php elseif($course->course_type === 'hybrid'): ?> Hybride
                            <?php else: ?> Présentiel
                            <?php endif; ?>
                        </span>
                    </div>
                    <h3 class="tw:font-bold tw:text-lg tw:mb-2"><?php echo e($course->title); ?></h3>
                    <p class="muted tw:text-sm tw:mb-4"><?php echo e($course->description); ?></p>
                    
                    <?php if($course->topicsArray() && count($course->topicsArray()) > 0): ?>
                        <ul class="tw:space-y-2 tw:text-sm tw:mb-4 tw:text-gray-700">
                            <?php $__currentLoopData = $course->topicsArray(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $topic): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <li>✓ <?php echo e($topic); ?></li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ul>
                    <?php endif; ?>
                    
                    <div class="tw:flex tw:flex-wrap tw:gap-2">
                        <a href="<?php echo e(route('portal.citizen.course.view', $course)); ?>" class="button button--primary tw:rounded-full tw:px-4 tw:py-2 tw:text-sm tw:inline-flex tw:items-center" title="Accédez au cours">
                            <i class="bi bi-play-circle me-1"></i>Accéder au cours
                        </a>
                        <span class="tw:text-xs tw:text-gray-500 tw:self-center">⏱ ~<?php echo e($course->duration_minutes ?? 30); ?> minutes</span>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    <?php else: ?>
        <div class="panel tw:rounded-2xl tw:border tw:border-amber-200/60 tw:bg-amber-50/50 tw:p-6 tw:mb-6">
            <p class="muted tw:text-center tw:mb-3">Aucun cours disponible actuellement.</p>
            <?php if($role === 'admin'): ?>
                <div class="tw:text-center">
                    <a href="<?php echo e(route('portal.admin.civic_courses.create')); ?>" class="button button--primary tw:text-xs tw:inline-flex tw:items-center">
                        <i class="bi bi-plus-circle me-1"></i>Ajouter un cours
                    </a>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    
    <div class="sec-header tw:mb-5 tw:mt-8">
        <h2 class="tw:text-lg">📅 Activités organisées par la mairie</h2>
        <p class="muted tw:text-sm tw:mt-2">Participez à nos événements et ateliers citoyens</p>
        <?php if($role === 'admin'): ?>
            <a href="<?php echo e(route('portal.admin.civic_activities')); ?>" class="button button--primary tw:text-xs tw:mt-3 tw:inline-flex tw:items-center">
                <i class="bi bi-gear me-1"></i>Gérer les activités
            </a>
        <?php endif; ?>
    </div>

    <?php if($civicActivities && $civicActivities->count() > 0): ?>
        <div class="grid tw:grid-cols-1 lg:tw:grid-cols-2 tw:gap-4">
            <?php $__currentLoopData = $civicActivities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $activity): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="panel tw:rounded-2xl tw:border tw:border-blue-200/60 tw:bg-gradient-to-br tw:from-blue-50 tw:to-white tw:shadow-sm tw:p-6">
                    <div class="tw:flex tw:items-start tw:justify-between tw:mb-3">
                        <div class="tw:text-4xl"><?php echo e($activity->icon_emoji ?? '🎯'); ?></div>
                        <span class="badge tw:rounded-full <?php if($activity->status === 'upcoming'): ?> tw:bg-green-100 tw:text-green-800 <?php elseif($activity->status === 'ongoing'): ?> tw:bg-blue-100 tw:text-blue-800 <?php elseif($activity->status === 'completed'): ?> tw:bg-gray-100 tw:text-gray-800 <?php else: ?> tw:bg-red-100 tw:text-red-800 <?php endif; ?> tw:text-xs tw:font-medium tw:px-2 tw:py-1">
                            <?php echo e($activity->getStatusLabel()); ?>

                        </span>
                    </div>
                    <h3 class="tw:font-bold tw:text-lg tw:mb-2"><?php echo e($activity->title); ?></h3>
                    <p class="muted tw:text-sm tw:mb-3"><?php echo e($activity->description); ?></p>
                    
                    <ul class="tw:space-y-2 tw:text-sm tw:mb-4 tw:text-gray-700">
                        <?php if($activity->event_date): ?>
                            <li>📅 <?php echo e(\Carbon\Carbon::parse($activity->event_date)->translatedFormat('d F Y')); ?></li>
                        <?php endif; ?>
                        <?php if($activity->event_start_time && $activity->event_end_time): ?>
                            <li>⏰ <?php echo e($activity->event_start_time); ?> - <?php echo e($activity->event_end_time); ?></li>
                        <?php endif; ?>
                        <?php if($activity->location): ?>
                            <li>📍 <?php echo e($activity->location); ?></li>
                        <?php endif; ?>
                        <?php if($activity->target_audience): ?>
                            <li>👥 <?php echo e($activity->target_audience); ?></li>
                        <?php endif; ?>
                    </ul>

                    <!-- Fichiers joints -->
                    <?php if($activity->image_path || $activity->document_path): ?>
                        <div class="tw:flex tw:flex-wrap tw:gap-2 tw:mb-4">
                            <?php if($activity->image_path): ?>
                                <a href="<?php echo e(asset('storage/' . $activity->image_path)); ?>" target="_blank" class="tw:inline-flex tw:items-center tw:gap-1 tw:text-xs tw:text-emerald-700 tw:bg-emerald-50 tw:px-2 tw:py-1 tw:rounded" title="Voir l'image">
                                    <i class="bi bi-image"></i>Image
                                </a>
                            <?php endif; ?>
                            <?php if($activity->document_path): ?>
                                <a href="<?php echo e(asset('storage/' . $activity->document_path)); ?>" target="_blank" download class="tw:inline-flex tw:items-center tw:gap-1 tw:text-xs tw:text-emerald-700 tw:bg-emerald-50 tw:px-2 tw:py-1 tw:rounded" title="Télécharger le document">
                                    <i class="bi bi-file-earmark"></i>Document
                                </a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                    
                    <form action="<?php echo e(route('portal.citizen.activity.register', $activity)); ?>" method="POST" class="tw:inline">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="button button--accent tw:rounded-full tw:px-4 tw:py-2 tw:text-sm tw:inline-flex tw:items-center">
                            <i class="bi bi-clipboard-check me-1"></i>M'inscrire
                        </button>
                    </form>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    <?php else: ?>
        <div class="panel tw:rounded-2xl tw:border tw:border-amber-200/60 tw:bg-amber-50/50 tw:p-6">
            <p class="muted tw:text-center tw:mb-3">Aucune activité organisée pour le moment.</p>
            <?php if($role === 'admin'): ?>
                <div class="tw:text-center">
                    <a href="<?php echo e(route('portal.admin.civic_activities.create')); ?>" class="button button--primary tw:text-xs tw:inline-flex tw:items-center">
                        <i class="bi bi-plus-circle me-1"></i>Créer une activité
                    </a>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    
    <div class="panel tw:rounded-2xl tw:border tw:border-emerald-200/60 tw:bg-white/95 tw:mt-6 tw:p-6">
        <h3 class="tw:font-bold tw:text-lg tw:mb-3">📋 Pourquoi participer?</h3>
        <div class="tw:grid tw:grid-cols-2 lg:tw:grid-cols-4 tw:gap-4">
            <div class="tw:text-center">
                <div class="tw:text-2xl tw:mb-2">🤝</div>
                <strong class="tw:text-sm tw:block">Connexion</strong>
                <p class="tw:text-xs tw:text-gray-600">Rencontrez vos concitoyens</p>
            </div>
            <div class="tw:text-center">
                <div class="tw:text-2xl tw:mb-2">📚</div>
                <strong class="tw:text-sm tw:block">Apprentissage</strong>
                <p class="tw:text-xs tw:text-gray-600">Développez vos connaissances</p>
            </div>
            <div class="tw:text-center">
                <div class="tw:text-2xl tw:mb-2">🗣️</div>
                <strong class="tw:text-sm tw:block">Voix</strong>
                <p class="tw:text-xs tw:text-gray-600">Faites entendre votre avis</p>
            </div>
            <div class="tw:text-center">
                <div class="tw:text-2xl tw:mb-2">🌟</div>
                <strong class="tw:text-sm tw:block">Impact</strong>
                <p class="tw:text-xs tw:text-gray-600">Changez votre communauté</p>
            </div>
        </div>
    </div>
</div>


<div class="tab-pane" data-pane="messages">
    <div class="sec-header"><h2>Messagerie</h2></div>
    <div class="panel tw:rounded-2xl tw:border tw:border-emerald-200/60 tw:bg-white/95 tw:mb-4">
        <h3 class="tw:mb-3">Nouveau message</h3>
        <form action="<?php echo e(route('portal.messages.store')); ?>" method="post">
            <?php echo csrf_field(); ?>
            <div class="form-grid tw:gap-4">
                <div class="field">
                    <label>Dossier</label>
                    <select name="demande_id" required <?php if($citizenMessageDemandes->isEmpty()): echo 'disabled'; endif; ?> class="tw:rounded-xl tw:border-emerald-200 tw:bg-white tw:px-3 tw:py-2.5 focus:tw:ring-2 focus:tw:ring-emerald-400/40">
                        <option value="">Choisir un dossier</option>
                        <?php $__currentLoopData = $citizenMessageDemandes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $demande): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($demande->id); ?>" <?php if(old('demande_id') == $demande->id): echo 'selected'; endif; ?>>
                                <?php echo e($demande->reference); ?> — <?php echo e($requestTypes[$demande->request_type] ?? $demande->request_type); ?>

                            </option>
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
                    <textarea name="body" rows="4" required <?php if($citizenMessageDemandes->isEmpty()): echo 'disabled'; endif; ?> class="tw:rounded-xl tw:border-emerald-200 tw:bg-white tw:px-3 tw:py-2.5 focus:tw:ring-2 focus:tw:ring-emerald-400/40"><?php echo e(old('body')); ?></textarea>
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
            <?php if($citizenMessageDemandes->isEmpty()): ?>
                <p class="muted tw:m-0">Vous devez créer un dossier avant d'envoyer un message.</p>
            <?php else: ?>
                <button class="button button--primary tw:rounded-full tw:px-5 tw:py-2.5" type="submit">Envoyer le message</button>
            <?php endif; ?>
        </form>
    </div>
    <div class="msg-switcher">
        <button class="msg-tab-btn active tw:text-sm tw:font-medium" data-msg-tab="recus" data-msg-group="c">
            📥 Reçus <span class="msg-count"><?php echo e($receivedMessages->count()); ?></span>
        </button>
        <button class="msg-tab-btn tw:text-sm tw:font-medium" data-msg-tab="envoyes" data-msg-group="c">
            📤 Envoyés <span class="msg-count"><?php echo e($sentMessages->count()); ?></span>
        </button>
    </div>
    <div class="msg-pane active" data-msg-pane="recus" data-msg-group="c">
        <?php if($receivedMessages->isEmpty()): ?>
            <p class="muted">Aucun message reçu pour le moment.</p>
        <?php else: ?>
            <div class="msg-list">
                <?php $__currentLoopData = $receivedMessages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $msg): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="msg-card tw:rounded-2xl tw:border tw:border-emerald-200/60 tw:bg-white/95 tw:p-4">
                        <div class="msg-card__header">
                            <span class="msg-card__from"><?php echo e(trim(($msg->sender->first_name ?? '') . ' ' . ($msg->sender->last_name ?? '')) ?: ($msg->sender->name ?? 'Utilisateur')); ?><span class="muted tw:text-xs tw:ml-1.5">(<?php echo e($msg->sender?->role === 'admin' ? 'Administrateur' : ($msg->sender?->role === 'agent' ? 'Agent' : 'Citoyen')); ?>)</span></span>
                            <span class="msg-card__time"><?php echo e(optional($msg->created_at)->format('d/m/Y H:i')); ?></span>
                        </div>
                        <?php if($msg->demande): ?><span class="msg-card__ref">📁 Dossier <?php echo e($msg->demande->reference); ?></span><?php endif; ?>
                        <p class="msg-card__body tw:text-slate-800"><?php echo e($msg->body); ?></p>
                        <?php if($msg->demande): ?>
                            <a class="button button--ghost tw:rounded-full tw:px-4 tw:py-2 tw:w-fit tw:text-sm" href="<?php echo e(route('portal.demandes.show', $msg->demande)); ?>">Ouvrir le dossier</a>
                        <?php endif; ?>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        <?php endif; ?>
    </div>
    <div class="msg-pane" data-msg-pane="envoyes" data-msg-group="c">
        <?php if($sentMessages->isEmpty()): ?>
            <p class="muted">Aucun message envoyé.</p>
        <?php else: ?>
            <div class="msg-list">
                <?php $__currentLoopData = $sentMessages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $msg): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="msg-card tw:rounded-2xl tw:border tw:border-emerald-200/60 tw:bg-white/95 tw:p-4">
                        <div class="msg-card__header">
                            <span class="msg-card__from">→ <?php echo e(trim(($msg->receiver->first_name ?? '') . ' ' . ($msg->receiver->last_name ?? '')) ?: ($msg->receiver->name ?? 'Utilisateur')); ?><span class="muted tw:text-xs tw:ml-1.5">(<?php echo e($msg->receiver?->role === 'admin' ? 'Administrateur' : ($msg->receiver?->role === 'agent' ? 'Agent' : 'Citoyen')); ?>)</span></span>
                            <span class="msg-card__time"><?php echo e(optional($msg->created_at)->format('d/m/Y H:i')); ?></span>
                        </div>
                        <?php if($msg->demande): ?><span class="msg-card__ref">📁 Dossier <?php echo e($msg->demande->reference); ?></span><?php endif; ?>
                        <p class="msg-card__body tw:text-slate-800"><?php echo e($msg->body); ?></p>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        <?php endif; ?>
    </div>
</div>


<div class="tab-pane" data-pane="profil">
    <div class="sec-header"><h2>Mon profil citoyen</h2></div>
    <div class="grid grid--2 tw:grid-cols-1 lg:tw:grid-cols-2 tw:gap-5 tw:items-start">
        <div class="panel tw:rounded-2xl tw:border tw:border-emerald-200/60 tw:bg-white/95">
            <span class="eyebrow">Identité</span>
            <h3>Informations enregistrées</h3>
            <ul class="list">
                <li><strong>N°CIT</strong><br><span class="muted"><?php echo e($currentUser->citizen_number ?: '—'); ?></span></li>
                <li><strong>Email</strong><br><span class="muted"><?php echo e($currentUser->email); ?></span></li>
                <li><strong>Téléphone</strong><br><span class="muted"><?php echo e($currentUser->phone ?: 'Non renseigné'); ?></span></li>
                <li><strong>N° de registre</strong><br><span class="muted"><?php echo e($currentUser->register_number ?: 'Non renseigné'); ?></span></li>
                <li><strong>Dernière connexion</strong><br><span class="muted"><?php echo e(optional($currentUser->last_login_at)->format('d/m/Y H:i') ?: 'Première connexion'); ?></span></li>
            </ul>
        </div>
        <div class="panel tw:rounded-2xl tw:border tw:border-emerald-200/60 tw:bg-white/95">
            <span class="eyebrow">Modifier</span>
            <h3>Mettre à jour</h3>
            <form action="<?php echo e(route('portal.citizen.profile.save')); ?>" method="post">
                <?php echo csrf_field(); ?>
                <div class="form-grid tw:gap-4">
                    <div class="field">
                        <label>Prénom</label>
                        <input name="first_name" type="text" value="<?php echo e(old('first_name', $currentUser->first_name)); ?>" class="tw:rounded-xl tw:border-emerald-200 tw:bg-white tw:px-3 tw:py-2.5 focus:tw:ring-2 focus:tw:ring-emerald-400/40">
                    </div>
                    <div class="field">
                        <label>Nom</label>
                        <input name="last_name" type="text" value="<?php echo e(old('last_name', $currentUser->last_name)); ?>" class="tw:rounded-xl tw:border-emerald-200 tw:bg-white tw:px-3 tw:py-2.5 focus:tw:ring-2 focus:tw:ring-emerald-400/40">
                    </div>
                    <div class="field">
                        <label>Email</label>
                        <input name="email" type="email" value="<?php echo e(old('email', $currentUser->email)); ?>" required class="tw:rounded-xl tw:border-emerald-200 tw:bg-white tw:px-3 tw:py-2.5 focus:tw:ring-2 focus:tw:ring-emerald-400/40">
                    </div>
                    <div class="field">
                        <label>Téléphone</label>
                        <input name="phone" type="text" value="<?php echo e(old('phone', $currentUser->phone)); ?>" class="tw:rounded-xl tw:border-emerald-200 tw:bg-white tw:px-3 tw:py-2.5 focus:tw:ring-2 focus:tw:ring-emerald-400/40">
                    </div>
                    <div class="field">
                        <label>Date de naissance</label>
                        <input name="birth_date" type="date" value="<?php echo e(old('birth_date', optional($currentUser->birth_date)->format('Y-m-d'))); ?>" class="tw:rounded-xl tw:border-emerald-200 tw:bg-white tw:px-3 tw:py-2.5 focus:tw:ring-2 focus:tw:ring-emerald-400/40">
                    </div>
                    <div class="field">
                        <label>Lieu de naissance</label>
                        <input name="birth_place" type="text" value="<?php echo e(old('birth_place', $currentUser->birth_place)); ?>" class="tw:rounded-xl tw:border-emerald-200 tw:bg-white tw:px-3 tw:py-2.5 focus:tw:ring-2 focus:tw:ring-emerald-400/40">
                    </div>
                    <div class="field">
                        <label>N° de registre</label>
                        <input name="register_number" type="text" value="<?php echo e(old('register_number', $currentUser->register_number)); ?>" class="tw:rounded-xl tw:border-emerald-200 tw:bg-white tw:px-3 tw:py-2.5 focus:tw:ring-2 focus:tw:ring-emerald-400/40">
                    </div>
                    <div class="field field--full">
                        <label>Adresse</label>
                        <textarea name="address" class="tw:rounded-xl tw:border-emerald-200 tw:bg-white tw:px-3 tw:py-2.5 focus:tw:ring-2 focus:tw:ring-emerald-400/40"><?php echo e(old('address', $currentUser->address)); ?></textarea>
                    </div>
                </div>
                <button class="button button--primary tw:rounded-full tw:px-5 tw:py-2.5" type="submit">Enregistrer</button>
            </form>
        </div>
    </div>
</div>


<?php elseif($role === 'agent'): ?>

<nav class="tab-nav tw:flex tw:flex-wrap tw:gap-2 tw:mb-5 tw:bg-gray-50">
    <button class="tab-btn tw:rounded-full tw:px-4 tw:py-2 tw:text-sm tw:font-medium" data-tab="accueil">📊 Vue d'ensemble</button>
    <button class="tab-btn tw:rounded-full tw:px-4 tw:py-2 tw:text-sm tw:font-medium" data-tab="file-attente">
        📥 File d'attente
        <?php if($pendingDemandes->count() > 0): ?><span class="tab-badge"><?php echo e($pendingDemandes->count()); ?></span><?php endif; ?>
    </button>
    <button class="tab-btn tw:rounded-full tw:px-4 tw:py-2 tw:text-sm tw:font-medium" data-tab="mes-dossiers">
        🗂 Mes dossiers
        <?php if($stats['total'] > 0): ?><span class="tab-badge"><?php echo e($stats['total']); ?></span><?php endif; ?>
    </button>
    <button class="tab-btn tw:rounded-full tw:px-4 tw:py-2 tw:text-sm tw:font-medium" data-tab="messages">
        ✉ Messages
        <?php if($agentMessagesReceived->count() > 0): ?><span class="tab-badge"><?php echo e($agentMessagesReceived->count()); ?></span><?php endif; ?>
    </button>
    <button class="tab-btn tw:rounded-full tw:px-4 tw:py-2 tw:text-sm tw:font-medium" data-tab="publipostage">📨 Publipostage</button>
    <button class="tab-btn tw:rounded-full tw:px-4 tw:py-2 tw:text-sm tw:font-medium" data-tab="profil">👤 Mon profil</button>
</nav>


<div class="tab-pane" data-pane="accueil">
    <div class="kpi-row tw:grid tw:grid-cols-2 lg:tw:grid-cols-3 tw:gap-3">
        <div class="kpi kpi--brand tw:bg-white tw:shadow-sm tw:rounded-lg">
            <span class="kpi__icon">🗂</span>
            <span class="kpi__value tw:text-xl md:tw:text-2xl tw:font-bold"><?php echo e($stats['total']); ?></span>
            <span class="kpi__label tw:text-xs tw:text-gray-500 tw:font-medium">Dossiers assignés</span>
        </div>
        <div class="kpi kpi--warn tw:bg-white tw:shadow-sm tw:rounded-lg">
            <span class="kpi__icon">⏳</span>
            <span class="kpi__value tw:text-xl md:tw:text-2xl tw:font-bold"><?php echo e($stats['assigned'] + $stats['processing']); ?></span>
            <span class="kpi__label tw:text-xs tw:text-gray-500 tw:font-medium">En cours</span>
        </div>
        <div class="kpi kpi--success tw:bg-white tw:shadow-sm tw:rounded-lg">
            <span class="kpi__icon">✅</span>
            <span class="kpi__value tw:text-xl md:tw:text-2xl tw:font-bold"><?php echo e($agentPerformance['completed']); ?></span>
            <span class="kpi__label tw:text-xs tw:text-gray-500 tw:font-medium">Finalisés (total)</span>
        </div>
        <div class="kpi kpi--brand tw:bg-white tw:shadow-sm tw:rounded-lg">
            <span class="kpi__icon">📅</span>
            <span class="kpi__value tw:text-xl md:tw:text-2xl tw:font-bold"><?php echo e($agentPerformance['monthly_completed']); ?></span>
            <span class="kpi__label tw:text-xs tw:text-gray-500 tw:font-medium">Traités ce mois</span>
        </div>
        <div class="kpi kpi--accent tw:bg-white tw:shadow-sm tw:rounded-lg">
            <span class="kpi__icon">⚡</span>
            <span class="kpi__value tw:text-xl md:tw:text-2xl tw:font-bold"><?php echo e($agentPerformance['completion_rate']); ?>%</span>
            <span class="kpi__label tw:text-xs tw:text-gray-500 tw:font-medium">Taux de traitement</span>
        </div>
        <div class="kpi kpi--warn tw:bg-white tw:shadow-sm tw:rounded-lg">
            <span class="kpi__icon">💳</span>
            <span class="kpi__value tw:text-xl md:tw:text-2xl tw:font-bold"><?php echo e($paymentValidationStats['pending']); ?></span>
            <span class="kpi__label tw:text-xs tw:text-gray-500 tw:font-medium">Paiements à valider</span>
        </div>
    </div>

    <?php if($agentPerformance['completion_rate'] > 0): ?>
        <div class="panel tw:rounded-2xl tw:border tw:border-emerald-200/60 tw:bg-white/95 tw:px-5 tw:py-4 tw:mb-5">
            <div class="split tw:mb-2">
                <span class="eyebrow">Taux de traitement global</span>
                <strong><?php echo e($agentPerformance['completion_rate']); ?>%</strong>
            </div>
            <div class="progress-bar">
                <div class="progress-bar__fill" data-width="<?php echo e($agentPerformance['completion_rate']); ?>"></div>
            </div>
        </div>
    <?php endif; ?>

    <div class="grid grid--2 tw:grid-cols-1 lg:tw:grid-cols-2 tw:gap-5">
        <div class="panel tw:rounded-2xl tw:border tw:border-emerald-200/60 tw:bg-white/95">
            <span class="eyebrow">Performance</span>
            <h3>Indicateurs détaillés</h3>
            <ul class="list">
                <li class="split"><span>Dossiers finalisés</span><strong><?php echo e($agentPerformance['completed']); ?></strong></li>
                <li class="split"><span>Dossiers rejetés</span><strong><?php echo e($agentPerformance['rejected']); ?></strong></li>
                <li class="split"><span>Délai moyen</span><strong><?php echo e($agentPerformance['avg_processing_hours'] !== null ? $agentPerformance['avg_processing_hours'] . ' h' : '—'); ?></strong></li>
                <li class="split"><span>Paiements validés</span><strong><?php echo e($paymentValidationStats['validated']); ?></strong></li>
            </ul>
        </div>
        <div class="panel tw:rounded-2xl tw:border tw:border-emerald-200/60 tw:bg-white/95">
            <span class="eyebrow">Actions rapides</span>
            <h3>Accès direct</h3>
            <div class="tw:grid tw:gap-2.5">
                <button class="button button--primary tw:w-full tw:justify-start tw:rounded-xl tw:px-4 tw:py-2.5" data-goto-tab="file-attente">
                    📥 File d'attente (<?php echo e($pendingDemandes->count()); ?> dossier(s))
                </button>
                <?php if($paymentValidationStats['pending'] > 0): ?>
                    <button class="button button--accent tw:w-full tw:justify-start tw:rounded-xl tw:px-4 tw:py-2.5" data-goto-tab="mes-dossiers">
                        💳 Valider paiements (<?php echo e($paymentValidationStats['pending']); ?>)
                    </button>
                <?php endif; ?>
                <button class="button button--ghost tw:w-full tw:justify-start tw:rounded-xl tw:px-4 tw:py-2.5" data-goto-tab="messages">
                    ✉ Messagerie (<?php echo e($agentMessagesReceived->count()); ?> reçu(s))
                </button>
            </div>
        </div>
    </div>
</div>


<div class="tab-pane" data-pane="file-attente">
    <div class="sec-header"><h2>File d'attente — dossiers non assignés</h2></div>
    <?php if($pendingDemandes->isEmpty()): ?>
        <div class="panel tw:text-center tw:rounded-2xl tw:border tw:border-emerald-200/60 tw:bg-white/95 tw:p-8">
            <div class="tw:text-4xl tw:mb-3">🎉</div>
            <p class="muted">Aucun dossier en attente d'assignation.</p>
        </div>
    <?php else: ?>
        <div class="grid tw:gap-3">
            <?php $__currentLoopData = $pendingDemandes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $demande): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="panel tw:rounded-2xl tw:border tw:border-emerald-200/60 tw:bg-white/95 tw:p-4">
                    <div class="split">
                        <div>
                            <strong><?php echo e($demande->reference); ?></strong>
                            <span class="muted tw:ml-2 tw:text-sm"><?php echo e($requestTypes[$demande->request_type] ?? $demande->request_type); ?></span><br>
                            <span class="muted tw:text-sm">
                                <?php echo e(trim(($demande->citoyen->first_name ?? '') . ' ' . ($demande->citoyen->last_name ?? '')) ?: ($demande->citoyen->name ?? 'Usager')); ?>

                                · Déposé le <?php echo e(optional($demande->created_at)->format('d/m/Y')); ?>

                            </span>
                        </div>
                        <span class="badge pending">En attente</span>
                    </div>
                    <div class="actions tw:mt-2 tw:flex tw:flex-wrap tw:gap-2">
                        <a class="button button--ghost tw:rounded-full tw:px-4 tw:py-2" href="<?php echo e(route('portal.demandes.show', $demande)); ?>">Analyser</a>
                        <form action="<?php echo e(route('portal.demandes.assign', $demande)); ?>" method="post">
                            <?php echo csrf_field(); ?>
                            <button class="button button--primary tw:rounded-full tw:px-4 tw:py-2" type="submit">Prendre en charge</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    <?php endif; ?>
</div>


<div class="tab-pane" data-pane="mes-dossiers">
    <div class="sec-header"><h2>Mon portefeuille de dossiers</h2></div>
    <?php if($assignedDemandes->isEmpty()): ?>
        <p class="muted">Aucun dossier assigné à votre compte.</p>
    <?php else: ?>
        <div class="grid tw:gap-3">
            <?php $__currentLoopData = $assignedDemandes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $demande): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="panel tw:rounded-2xl tw:border tw:border-emerald-200/60 tw:bg-white/95 tw:p-4">
                    <div class="split">
                        <div>
                            <strong><?php echo e($demande->reference); ?></strong>
                            <span class="muted tw:ml-2 tw:text-sm"><?php echo e($requestTypes[$demande->request_type] ?? $demande->request_type); ?></span><br>
                            <span class="muted tw:text-sm">
                                <?php echo e(trim(($demande->citoyen->first_name ?? '') . ' ' . ($demande->citoyen->last_name ?? '')) ?: ($demande->citoyen->name ?? 'Usager')); ?>

                                · <?php echo e(optional($demande->created_at)->format('d/m/Y')); ?>

                            </span>
                        </div>
                        <div class="tw:flex tw:flex-col tw:items-end tw:gap-1.5">
                            <span class="badge <?php echo e($statusClassMap[$demande->status] ?? 'pending'); ?>"><?php echo e($statusLabels[$demande->status] ?? $demande->status); ?></span>
                            <?php if($demande->payment_status === 'paid_pending'): ?>
                                <span class="badge tw:bg-amber-100 tw:text-amber-700 tw:border-amber-200">💳 À valider</span>
                            <?php elseif($demande->payment_status === 'paid'): ?>
                                <span class="badge completed">✓ Payé</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="actions tw:mt-2 tw:flex tw:flex-wrap tw:gap-2">
                        <a class="button button--ghost tw:rounded-full tw:px-4 tw:py-2" href="<?php echo e(route('portal.demandes.show', $demande)); ?>">Ouvrir</a>
                        <?php if($demande->payment_status === 'paid_pending'): ?>
                            <form action="<?php echo e(route('portal.agent.demandes.payment.validate', $demande)); ?>" method="post">
                                <?php echo csrf_field(); ?>
                                <button class="button button--primary tw:rounded-full tw:px-4 tw:py-2" type="submit">Valider paiement</button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    <?php endif; ?>
</div>


<div class="tab-pane" data-pane="messages">
    <div class="sec-header"><h2>Messagerie</h2></div>
    <div class="panel tw:rounded-2xl tw:border tw:border-emerald-200/60 tw:bg-white/95 tw:mb-4">
        <h3 class="tw:mb-3">Nouveau message</h3>
        <form action="<?php echo e(route('portal.messages.store')); ?>" method="post">
            <?php echo csrf_field(); ?>
            <div class="form-grid tw:gap-4">
                <div class="field">
                    <label>Dossier</label>
                    <select name="demande_id" required <?php if($agentMessageDemandes->isEmpty()): echo 'disabled'; endif; ?> class="tw:rounded-xl tw:border-emerald-200 tw:bg-white tw:px-3 tw:py-2.5 focus:tw:ring-2 focus:tw:ring-emerald-400/40">
                        <option value="">Choisir un dossier</option>
                        <?php $__currentLoopData = $agentMessageDemandes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $demande): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($demande->id); ?>" <?php if(old('demande_id') == $demande->id): echo 'selected'; endif; ?>>
                                <?php echo e($demande->reference); ?> — <?php echo e(trim(($demande->citoyen->first_name ?? '') . ' ' . ($demande->citoyen->last_name ?? '')) ?: ($demande->citoyen->name ?? 'Citoyen')); ?>

                            </option>
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
                    <textarea name="body" rows="4" required <?php if($agentMessageDemandes->isEmpty()): echo 'disabled'; endif; ?> class="tw:rounded-xl tw:border-emerald-200 tw:bg-white tw:px-3 tw:py-2.5 focus:tw:ring-2 focus:tw:ring-emerald-400/40"><?php echo e(old('body')); ?></textarea>
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
            <?php if($agentMessageDemandes->isEmpty()): ?>
                <p class="muted tw:m-0">Aucun dossier assigné pour envoyer un message.</p>
            <?php else: ?>
                <button class="button button--primary tw:rounded-full tw:px-5 tw:py-2.5" type="submit">Envoyer le message</button>
            <?php endif; ?>
        </form>
    </div>
    <div class="msg-switcher">
        <button class="msg-tab-btn active tw:text-sm tw:font-medium" data-msg-tab="recus" data-msg-group="a">
            📥 Reçus <span class="msg-count"><?php echo e($agentMessagesReceived->count()); ?></span>
        </button>
        <button class="msg-tab-btn tw:text-sm tw:font-medium" data-msg-tab="envoyes" data-msg-group="a">
            📤 Envoyés <span class="msg-count"><?php echo e($agentMessagesSent->count()); ?></span>
        </button>
    </div>
    <div class="msg-pane active" data-msg-pane="recus" data-msg-group="a">
        <?php if($agentMessagesReceived->isEmpty()): ?>
            <p class="muted">Aucun message reçu.</p>
        <?php else: ?>
            <div class="msg-list">
                <?php $__currentLoopData = $agentMessagesReceived; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $msg): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="msg-card tw:rounded-2xl tw:border tw:border-emerald-200/60 tw:bg-white/95 tw:p-4">
                        <div class="msg-card__header">
                            <span class="msg-card__from"><?php echo e(trim(($msg->sender->first_name ?? '') . ' ' . ($msg->sender->last_name ?? '')) ?: ($msg->sender->name ?? 'Utilisateur')); ?><span class="muted tw:text-xs tw:ml-1.5">(<?php echo e($msg->sender?->role === 'admin' ? 'Administrateur' : ($msg->sender?->role === 'agent' ? 'Agent' : 'Citoyen')); ?>)</span></span>
                            <span class="msg-card__time"><?php echo e(optional($msg->created_at)->format('d/m/Y H:i')); ?></span>
                        </div>
                        <?php if($msg->demande): ?><span class="msg-card__ref">📁 Dossier <?php echo e($msg->demande->reference); ?></span><?php endif; ?>
                        <p class="msg-card__body tw:text-slate-800"><?php echo e($msg->body); ?></p>
                        <?php if($msg->demande): ?>
                            <a class="button button--ghost tw:rounded-full tw:px-4 tw:py-2 tw:w-fit tw:text-sm" href="<?php echo e(route('portal.demandes.show', $msg->demande)); ?>">Ouvrir le dossier</a>
                        <?php endif; ?>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        <?php endif; ?>
    </div>
    <div class="msg-pane" data-msg-pane="envoyes" data-msg-group="a">
        <?php if($agentMessagesSent->isEmpty()): ?>
            <p class="muted">Aucun message envoyé.</p>
        <?php else: ?>
            <div class="msg-list">
                <?php $__currentLoopData = $agentMessagesSent; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $msg): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="msg-card tw:rounded-2xl tw:border tw:border-emerald-200/60 tw:bg-white/95 tw:p-4">
                        <div class="msg-card__header">
                            <span class="msg-card__from">→ <?php echo e(trim(($msg->receiver->first_name ?? '') . ' ' . ($msg->receiver->last_name ?? '')) ?: ($msg->receiver->name ?? 'Utilisateur')); ?><span class="muted tw:text-xs tw:ml-1.5">(<?php echo e($msg->receiver?->role === 'admin' ? 'Administrateur' : ($msg->receiver?->role === 'agent' ? 'Agent' : 'Citoyen')); ?>)</span></span>
                            <span class="msg-card__time"><?php echo e(optional($msg->created_at)->format('d/m/Y H:i')); ?></span>
                        </div>
                        <?php if($msg->demande): ?><span class="msg-card__ref">📁 Dossier <?php echo e($msg->demande->reference); ?></span><?php endif; ?>
                        <p class="msg-card__body tw:text-slate-800"><?php echo e($msg->body); ?></p>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        <?php endif; ?>
    </div>
</div>


<div class="tab-pane" data-pane="publipostage">
    <div class="sec-header"><h2>Publipostage — Export par type</h2></div>
    <p class="muted tw:mb-5">Exports générés par lots de 10 demandeurs payés. Téléversez votre modèle Word (.docx) pour chaque type.</p>
    <div class="table-card">
        <table>
            <thead>
                <tr>
                    <th>Type de demande</th>
                    <th>Payés</th>
                    <th>Lots</th>
                    <th>Modèle Word</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $agentMailMergeByType; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $typeKey => $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><strong><?php echo e($data['label']); ?></strong></td>
                        <td><?php echo e($data['count']); ?> <span class="muted">(<?php echo e($data['remainder']); ?> hors lot)</span></td>
                        <td>
                            <?php if($data['full_lots'] > 0): ?>
                                <span class="badge completed"><?php echo e($data['full_lots']); ?></span>
                            <?php else: ?>
                                <span class="muted">—</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if(isset($agentTemplatesByType[$typeKey])): ?>
                                <a class="button button--ghost tw:text-xs tw:rounded-full tw:px-3 tw:py-1.5" href="<?php echo e(route('portal.agent.mailmerge.template.download', $typeKey)); ?>">
                                    📄 <?php echo e(\Illuminate\Support\Str::limit($agentTemplatesByType[$typeKey]->original_name, 22)); ?>

                                </a>
                            <?php else: ?>
                                <span class="muted">Aucun modèle</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <form action="<?php echo e(route('portal.agent.mailmerge.template.upload', $typeKey)); ?>" method="post" enctype="multipart/form-data" class="actions tw:mb-2">
                                <?php echo csrf_field(); ?>
                                <input type="file" name="template" accept=".doc,.docx" required class="tw:text-xs">
                                <button class="button button--primary tw:text-xs tw:rounded-full tw:px-3 tw:py-1.5" type="submit">Téléverser</button>
                            </form>
                            <div class="actions">
                                <?php if($data['full_lots'] > 0): ?>
                                    <?php for($lot = 1; $lot <= $data['full_lots']; $lot++): ?>
                                        <a class="button button--accent tw:text-xs tw:rounded-full tw:px-3 tw:py-1.5" href="<?php echo e(route('portal.agent.mailmerge.export', ['requestType' => $typeKey, 'lot' => $lot])); ?>">Lot <?php echo e($lot); ?></a>
                                    <?php endfor; ?>
                                <?php else: ?>
                                    <span class="muted tw:text-xs">Pas de lot disponible</span>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </div>
</div>


<div class="tab-pane" data-pane="profil">
    <div class="sec-header"><h2>Mon profil agent</h2></div>
    <div class="grid grid--2 tw:grid-cols-1 lg:tw:grid-cols-2 tw:gap-5 tw:items-start">
        <div class="panel">
            <span class="eyebrow">Informations</span>
            <h3>Compte agent</h3>
            <ul class="list">
                <li><strong>Identifiant</strong><br><span class="muted"><?php echo e($currentUser->name ?: '—'); ?></span></li>
                <li><strong>Email</strong><br><span class="muted"><?php echo e($currentUser->email); ?></span></li>
                <li><strong>Téléphone</strong><br><span class="muted"><?php echo e($currentUser->phone ?: 'Non renseigné'); ?></span></li>
                <li><strong>Dernière connexion</strong><br><span class="muted"><?php echo e(optional($currentUser->last_login_at)->format('d/m/Y H:i') ?: 'Première connexion'); ?></span></li>
            </ul>
        </div>
        <div class="panel">
            <span class="eyebrow">Modifier</span>
            <h3>Mettre à jour mon profil</h3>
            <form action="<?php echo e(route('portal.agent.profile.save')); ?>" method="post">
                <?php echo csrf_field(); ?>
                <div class="form-grid">
                    <div class="field">
                        <label>Prénom</label>
                        <input name="first_name" type="text" value="<?php echo e(old('first_name', $currentUser->first_name)); ?>">
                    </div>
                    <div class="field">
                        <label>Nom</label>
                        <input name="last_name" type="text" value="<?php echo e(old('last_name', $currentUser->last_name)); ?>">
                    </div>
                    <div class="field">
                        <label>Email</label>
                        <input name="email" type="email" value="<?php echo e(old('email', $currentUser->email)); ?>" required>
                    </div>
                    <div class="field">
                        <label>Téléphone</label>
                        <input name="phone" type="text" value="<?php echo e(old('phone', $currentUser->phone)); ?>">
                    </div>
                    <div class="field field--full">
                        <label>Adresse</label>
                        <textarea name="address"><?php echo e(old('address', $currentUser->address)); ?></textarea>
                    </div>
                    <div class="field">
                        <label>Mot de passe actuel</label>
                        <input name="current_password" type="password">
                    </div>
                    <div class="field">
                        <label>Nouveau mot de passe</label>
                        <input name="password" type="password">
                    </div>
                    <div class="field">
                        <label>Confirmer</label>
                        <input name="password_confirmation" type="password">
                    </div>
                </div>
                <button class="button button--primary" type="submit">Enregistrer</button>
            </form>
        </div>
    </div>
</div>


<?php else: ?>

<nav class="tab-nav">
    <button class="tab-btn tw:rounded-full tw:px-4 tw:py-2 tw:text-sm tw:font-medium" data-tab="accueil">📊 Vue d'ensemble</button>
    <button class="tab-btn tw:rounded-full tw:px-4 tw:py-2 tw:text-sm tw:font-medium" data-tab="utilisateurs">👥 Utilisateurs</button>
    <button class="tab-btn tw:rounded-full tw:px-4 tw:py-2 tw:text-sm tw:font-medium" data-tab="messages-admin">
        ✉ Messages
        <?php if($adminReceivedMessages->count() > 0): ?><span class="tab-badge"><?php echo e($adminReceivedMessages->count()); ?></span><?php endif; ?>
    </button>
    <button class="tab-btn tw:rounded-full tw:px-4 tw:py-2 tw:text-sm tw:font-medium" data-tab="dossiers-admin">📁 Derniers dossiers</button>
</nav>


<div class="tab-pane" data-pane="accueil">
    <div class="kpi-row tw:grid tw:grid-cols-2 lg:tw:grid-cols-3 tw:gap-3">
        <div class="kpi kpi--brand tw:bg-white tw:shadow-sm tw:rounded-lg">
            <span class="kpi__icon">👥</span>
            <span class="kpi__value tw:text-xl md:tw:text-2xl tw:font-bold"><?php echo e($userCounts['total']); ?></span>
            <span class="kpi__label tw:text-xs tw:text-gray-500 tw:font-medium">Utilisateurs</span>
        </div>
        <div class="kpi kpi--brand tw:bg-white tw:shadow-sm tw:rounded-lg">
            <span class="kpi__icon">🗂</span>
            <span class="kpi__value tw:text-xl md:tw:text-2xl tw:font-bold"><?php echo e($userCounts['agents']); ?></span>
            <span class="kpi__label tw:text-xs tw:text-gray-500 tw:font-medium">Agents</span>
        </div>
        <div class="kpi kpi--accent tw:bg-white tw:shadow-sm tw:rounded-lg">
            <span class="kpi__icon">📋</span>
            <span class="kpi__value tw:text-xl md:tw:text-2xl tw:font-bold"><?php echo e($demandeCounts['total']); ?></span>
            <span class="kpi__label tw:text-xs tw:text-gray-500 tw:font-medium">Dossiers total</span>
        </div>
        <div class="kpi kpi--warn tw:bg-white tw:shadow-sm tw:rounded-lg">
            <span class="kpi__icon">⏳</span>
            <span class="kpi__value tw:text-xl md:tw:text-2xl tw:font-bold"><?php echo e($demandeCounts['pending']); ?></span>
            <span class="kpi__label tw:text-xs tw:text-gray-500 tw:font-medium">En attente</span>
        </div>
        <div class="kpi kpi--success tw:bg-white tw:shadow-sm tw:rounded-lg">
            <span class="kpi__icon">✅</span>
            <span class="kpi__value tw:text-xl md:tw:text-2xl tw:font-bold"><?php echo e($demandeCounts['completed']); ?></span>
            <span class="kpi__label tw:text-xs tw:text-gray-500 tw:font-medium">Finalisés</span>
        </div>
        <div class="kpi kpi--danger tw:bg-white tw:shadow-sm tw:rounded-lg">
            <span class="kpi__icon">✗</span>
            <span class="kpi__value tw:text-xl md:tw:text-2xl tw:font-bold"><?php echo e($demandeCounts['rejected']); ?></span>
            <span class="kpi__label tw:text-xs tw:text-gray-500 tw:font-medium">Rejetés</span>
        </div>
    </div>

    <div class="sec-header tw:mb-4"><h2>Accès rapide</h2></div>
    <div class="quick-grid tw:grid tw:grid-cols-1 sm:tw:grid-cols-2 lg:tw:grid-cols-3 tw:gap-3">
        <a class="quick-card tw:rounded-2xl tw:border tw:border-emerald-200/60 tw:bg-white/95 tw:p-4" href="<?php echo e(route('portal.admin.agents')); ?>">
            <span class="quick-card__icon">👷</span>
            <span class="quick-card__label">Agents</span>
            <span class="quick-card__desc">Gérer et créer des agents</span>
        </a>
        <a class="quick-card tw:rounded-2xl tw:border tw:border-emerald-200/60 tw:bg-white/95 tw:p-4" href="<?php echo e(route('portal.admin.demandes')); ?>">
            <span class="quick-card__icon">📋</span>
            <span class="quick-card__label">Demandes</span>
            <span class="quick-card__desc">Tous les dossiers citoyens</span>
        </a>
        <a class="quick-card tw:rounded-2xl tw:border tw:border-emerald-200/60 tw:bg-white/95 tw:p-4" href="<?php echo e(route('portal.admin.citoyens')); ?>">
            <span class="quick-card__icon">👤</span>
            <span class="quick-card__label">Citoyens</span>
            <span class="quick-card__desc">Liste et export PDF</span>
        </a>
        <a class="quick-card tw:rounded-2xl tw:border tw:border-emerald-200/60 tw:bg-white/95 tw:p-4" href="<?php echo e(route('portal.admin.messages')); ?>">
            <span class="quick-card__icon">✉</span>
            <span class="quick-card__label">Messages</span>
            <span class="quick-card__desc">Tous les échanges du portail</span>
        </a>
        <a class="quick-card tw:rounded-2xl tw:border tw:border-emerald-200/60 tw:bg-white/95 tw:p-4" href="<?php echo e(route('portal.admin.stats')); ?>">
            <span class="quick-card__icon">📈</span>
            <span class="quick-card__label">Statistiques</span>
            <span class="quick-card__desc">Tableaux de bord analytiques</span>
        </a>
        <a class="quick-card tw:rounded-2xl tw:border tw:border-emerald-200/60 tw:bg-white/95 tw:p-4" href="<?php echo e(route('portal.admin.settings')); ?>">
            <span class="quick-card__icon">⚙</span>
            <span class="quick-card__label">Paramètres</span>
            <span class="quick-card__desc">Configuration du portail</span>
        </a>
        <a class="quick-card tw:rounded-2xl tw:border tw:border-emerald-200/60 tw:bg-white/95 tw:p-4" href="<?php echo e(route('portal.admin.profile')); ?>">
            <span class="quick-card__icon">🔑</span>
            <span class="quick-card__label">Mon profil</span>
            <span class="quick-card__desc">Mot de passe et informations</span>
        </a>
    </div>

    <div class="sec-header">
        <h2>Activité récente</h2>
    </div>
    <div class="panel">
        <?php if($recentActivity->isEmpty()): ?>
            <p class="muted">Aucune activité enregistrée.</p>
        <?php else: ?>
            <ul class="list">
                <?php $__currentLoopData = $recentActivity->take(8); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $activity): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li>
                        <div class="split">
                            <span>
                                <strong><?php echo e($activity->action); ?></strong>
                                <?php if($activity->user): ?> <span class="muted">par <?php echo e($activity->user->name); ?></span><?php endif; ?>
                            </span>
                            <span class="muted tw:text-xs tw:whitespace-nowrap"><?php echo e(optional($activity->created_at)->format('d/m H:i')); ?></span>
                        </div>
                    </li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        <?php endif; ?>
    </div>
</div>


<div class="tab-pane" data-pane="utilisateurs">
    <div class="grid grid--2 tw:grid-cols-1 lg:tw:grid-cols-2 tw:gap-5 tw:items-start">
        <div class="panel tw:rounded-2xl tw:border tw:border-emerald-200/60 tw:bg-white/95">
            <span class="eyebrow">Créer un compte</span>
            <h3>Nouvel utilisateur</h3>
            <form action="<?php echo e(route('portal.admin.users.store')); ?>" method="post">
                <?php echo csrf_field(); ?>
                <div class="form-grid tw:gap-4">
                    <div class="field">
                        <label>Prénom</label>
                        <input name="first_name" type="text" class="tw:rounded-xl tw:border-emerald-200 tw:bg-white tw:px-3 tw:py-2.5 focus:tw:ring-2 focus:tw:ring-emerald-400/40">
                    </div>
                    <div class="field">
                        <label>Nom</label>
                        <input name="last_name" type="text" class="tw:rounded-xl tw:border-emerald-200 tw:bg-white tw:px-3 tw:py-2.5 focus:tw:ring-2 focus:tw:ring-emerald-400/40">
                    </div>
                    <div class="field">
                        <label>Email</label>
                        <input name="email" type="email" required class="tw:rounded-xl tw:border-emerald-200 tw:bg-white tw:px-3 tw:py-2.5 focus:tw:ring-2 focus:tw:ring-emerald-400/40">
                    </div>
                    <div class="field">
                        <label>Rôle</label>
                        <select name="role" required class="tw:rounded-xl tw:border-emerald-200 tw:bg-white tw:px-3 tw:py-2.5 focus:tw:ring-2 focus:tw:ring-emerald-400/40">
                            <option value="citoyen">Citoyen</option>
                            <option value="agent">Agent</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                    <div class="field field--full">
                        <label>Mot de passe initial</label>
                        <input name="password" type="password" required class="tw:rounded-xl tw:border-emerald-200 tw:bg-white tw:px-3 tw:py-2.5 focus:tw:ring-2 focus:tw:ring-emerald-400/40">
                    </div>
                </div>
                <button class="button button--primary tw:rounded-full tw:px-5 tw:py-2.5" type="submit">Créer le compte</button>
            </form>
        </div>

        <div class="table-card tw:rounded-2xl tw:border tw:border-emerald-200/60 tw:bg-white/95 tw:overflow-auto">
            <div class="sec-header"><h3>Comptes récents</h3></div>
            <table>
                <thead>
                    <tr>
                        <th>Utilisateur</th>
                        <th>Rôle</th>
                        <th>État</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $recentUsers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $u): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td>
                                <strong><?php echo e($u->name); ?></strong><br>
                                <span class="muted tw:text-xs"><?php echo e($u->email); ?></span>
                            </td>
                            <td>
                                <form action="<?php echo e(route('portal.admin.users.role', $u)); ?>" method="post" class="actions tw:gap-1.5">
                                    <?php echo csrf_field(); ?>
                                    <select name="role" class="tw:rounded-lg tw:border-emerald-200 tw:bg-white tw:px-2.5 tw:py-1.5 tw:text-xs">
                                        <option value="citoyen" <?php if($u->role === 'citoyen'): echo 'selected'; endif; ?>>Citoyen</option>
                                        <option value="agent"   <?php if($u->role === 'agent'): echo 'selected'; endif; ?>>Agent</option>
                                        <option value="admin"   <?php if($u->role === 'admin'): echo 'selected'; endif; ?>>Admin</option>
                                    </select>
                                    <button class="button button--ghost tw:rounded-full tw:px-3 tw:py-1.5 tw:text-xs" type="submit">OK</button>
                                </form>
                            </td>
                            <td>
                                <span class="badge <?php echo e($u->is_active ? 'completed' : 'rejected'); ?>"><?php echo e($u->is_active ? 'Actif' : 'Inactif'); ?></span>
                            </td>
                            <td>
                                <form action="<?php echo e(route('portal.admin.users.toggle', $u)); ?>" method="post">
                                    <?php echo csrf_field(); ?>
                                    <button class="button button--ghost tw:rounded-full tw:px-3 tw:py-1.5 tw:text-xs" type="submit">
                                        <?php echo e($u->is_active ? 'Désactiver' : 'Activer'); ?>

                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
    </div>
</div>


<div class="tab-pane" data-pane="messages-admin">
    <div class="sec-header"><h2>Messagerie</h2></div>
    <div class="panel tw:rounded-2xl tw:border tw:border-emerald-200/60 tw:bg-white/95 tw:mb-4">
        <h3 class="tw:mb-3">Nouveau message</h3>
        <form action="<?php echo e(route('portal.messages.store')); ?>" method="post">
            <?php echo csrf_field(); ?>
            <div class="form-grid tw:gap-4">
                <div class="field">
                    <label>Destinataire</label>
                    <select name="receiver_id" required <?php if($adminMessageUsers->isEmpty()): echo 'disabled'; endif; ?> class="tw:rounded-xl tw:border-emerald-200 tw:bg-white tw:px-3 tw:py-2.5 focus:tw:ring-2 focus:tw:ring-emerald-400/40">
                        <option value="">Choisir un utilisateur</option>
                        <?php $__currentLoopData = $adminMessageUsers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $u): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($u->id); ?>" <?php if(old('receiver_id') == $u->id): echo 'selected'; endif; ?>>
                                <?php echo e(trim(($u->first_name ?? '') . ' ' . ($u->last_name ?? '')) ?: $u->name); ?>

                                (<?php echo e(ucfirst($u->role)); ?>)
                            </option>
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
                <div class="field field--full">
                    <label>Message</label>
                    <textarea name="body" rows="4" required <?php if($adminMessageUsers->isEmpty()): echo 'disabled'; endif; ?> class="tw:rounded-xl tw:border-emerald-200 tw:bg-white tw:px-3 tw:py-2.5 focus:tw:ring-2 focus:tw:ring-emerald-400/40"><?php echo e(old('body')); ?></textarea>
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
            <?php if($adminMessageUsers->isEmpty()): ?>
                <p class="muted tw:m-0">Aucun utilisateur actif disponible.</p>
            <?php else: ?>
                <button class="button button--primary tw:rounded-full tw:px-5 tw:py-2.5" type="submit">Envoyer le message</button>
            <?php endif; ?>
        </form>
    </div>
    <div class="msg-switcher">
        <button class="msg-tab-btn active tw:text-sm tw:font-medium" data-msg-tab="recus" data-msg-group="adm">
            📥 Reçus <span class="msg-count"><?php echo e($adminReceivedMessages->count()); ?></span>
        </button>
        <button class="msg-tab-btn tw:text-sm tw:font-medium" data-msg-tab="envoyes" data-msg-group="adm">
            📤 Envoyés <span class="msg-count"><?php echo e($adminSentMessages->count()); ?></span>
        </button>
    </div>
    <div class="msg-pane active" data-msg-pane="recus" data-msg-group="adm">
        <?php if($adminReceivedMessages->isEmpty()): ?>
            <p class="muted">Aucun message reçu.</p>
        <?php else: ?>
            <div class="msg-list">
                <?php $__currentLoopData = $adminReceivedMessages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $msg): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="msg-card tw:rounded-2xl tw:border tw:border-emerald-200/60 tw:bg-white/95 tw:p-4">
                        <div class="msg-card__header">
                            <span class="msg-card__from"><?php echo e(trim(($msg->sender->first_name ?? '') . ' ' . ($msg->sender->last_name ?? '')) ?: ($msg->sender->name ?? 'Utilisateur')); ?><span class="muted tw:text-xs tw:ml-1.5">(<?php echo e(ucfirst($msg->sender->role ?? '')); ?>)</span></span>
                            <span class="msg-card__time"><?php echo e(optional($msg->created_at)->format('d/m/Y H:i')); ?></span>
                        </div>
                        <?php if($msg->demande): ?><span class="msg-card__ref">📁 Dossier <?php echo e($msg->demande->reference); ?></span><?php endif; ?>
                        <p class="msg-card__body tw:text-slate-800"><?php echo e($msg->body); ?></p>
                        <?php if($msg->demande): ?>
                            <a class="button button--ghost tw:rounded-full tw:px-4 tw:py-2 tw:w-fit tw:text-sm" href="<?php echo e(route('portal.demandes.show', $msg->demande)); ?>">Ouvrir le dossier</a>
                        <?php endif; ?>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        <?php endif; ?>
    </div>
    <div class="msg-pane" data-msg-pane="envoyes" data-msg-group="adm">
        <?php if($adminSentMessages->isEmpty()): ?>
            <p class="muted">Aucun message envoyé.</p>
        <?php else: ?>
            <div class="msg-list">
                <?php $__currentLoopData = $adminSentMessages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $msg): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="msg-card tw:rounded-2xl tw:border tw:border-emerald-200/60 tw:bg-white/95 tw:p-4">
                        <div class="msg-card__header">
                            <span class="msg-card__from">→ <?php echo e(trim(($msg->receiver->first_name ?? '') . ' ' . ($msg->receiver->last_name ?? '')) ?: ($msg->receiver->name ?? 'Utilisateur')); ?><span class="muted tw:text-xs tw:ml-1.5">(<?php echo e(ucfirst($msg->receiver->role ?? '')); ?>)</span></span>
                            <span class="msg-card__time"><?php echo e(optional($msg->created_at)->format('d/m/Y H:i')); ?></span>
                        </div>
                        <?php if($msg->demande): ?><span class="msg-card__ref">📁 Dossier <?php echo e($msg->demande->reference); ?></span><?php endif; ?>
                        <p class="msg-card__body tw:text-slate-800"><?php echo e($msg->body); ?></p>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        <?php endif; ?>
    </div>
</div>


<div class="tab-pane" data-pane="dossiers-admin">
    <div class="sec-header tw:flex tw:flex-wrap tw:items-center tw:justify-between tw:gap-3">
        <h2>Derniers dossiers</h2>
        <a class="button button--ghost tw:rounded-full tw:px-4 tw:py-2" href="<?php echo e(route('portal.admin.demandes')); ?>">Voir tous →</a>
    </div>
    <div class="table-card tw:rounded-2xl tw:border tw:border-emerald-200/60 tw:bg-white/95">
        <table>
            <thead>
                <tr>
                    <th>Référence</th>
                    <th>Usager</th>
                    <th>Agent</th>
                    <th>Statut</th>
                    <th>Date</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $recentDemandes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $demande): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><strong><?php echo e($demande->reference); ?></strong></td>
                        <td><?php echo e(trim(($demande->citoyen->first_name ?? '') . ' ' . ($demande->citoyen->last_name ?? '')) ?: ($demande->citoyen->name ?? 'Usager')); ?></td>
                        <td><?php echo e($demande->agent ? (trim(($demande->agent->first_name ?? '') . ' ' . ($demande->agent->last_name ?? '')) ?: $demande->agent->name) : '—'); ?></td>
                        <td><span class="badge <?php echo e($statusClassMap[$demande->status] ?? 'pending'); ?>"><?php echo e($statusLabels[$demande->status] ?? $demande->status); ?></span></td>
                        <td class="muted tw:text-xs"><?php echo e(optional($demande->created_at)->format('d/m/Y')); ?></td>
                        <td><a class="button button--ghost tw:rounded-full tw:px-3 tw:py-1.5 tw:text-xs" href="<?php echo e(route('portal.demandes.show', $demande)); ?>">Voir</a></td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </div>
</div>

<?php endif; ?>

<script>
(function () {
    'use strict';

    var STORAGE_KEY = 'portal_tab_' + '<?php echo e($role); ?>';
    var activeTabClasses = ['tw:bg-amber-500', 'tw:text-white', 'tw:border-amber-600', 'tw:shadow-sm'];
    var inactiveTabClasses = ['tw:bg-transparent', 'tw:text-slate-600'];

    // ── Onglets principaux ───────────────────────────────────────────
    var tabBtns  = document.querySelectorAll('.tab-btn[data-tab]');
    var tabPanes = document.querySelectorAll('.tab-pane[data-pane]');

    function activateTab(name) {
        tabBtns.forEach(function (btn) {
            var isActive = btn.dataset.tab === name;
            btn.classList.toggle('active', isActive);
            activeTabClasses.forEach(function (cls) { btn.classList.toggle(cls, isActive); });
            inactiveTabClasses.forEach(function (cls) { btn.classList.toggle(cls, !isActive); });
        });
        tabPanes.forEach(function (pane) {
            pane.classList.toggle('active', pane.dataset.pane === name);
        });
        try { sessionStorage.setItem(STORAGE_KEY, name); } catch (e) {}
    }

    tabBtns.forEach(function (btn) {
        btn.addEventListener('click', function () { activateTab(this.dataset.tab); });
    });

    // Restaurer l'onglet sauvegardé ou activer le premier
    var saved = null;
    try { saved = sessionStorage.getItem(STORAGE_KEY); } catch (e) {}
    var first = tabBtns.length ? tabBtns[0].dataset.tab : null;
    var target = (saved && document.querySelector('.tab-btn[data-tab="' + saved + '"]')) ? saved : first;
    if (target) activateTab(target);

    // data-goto-tab (boutons de raccourci)
    document.addEventListener('click', function (e) {
        var btn = e.target.closest('[data-goto-tab]');
        if (btn) activateTab(btn.dataset.gotoTab);
    });

    // ── Sous-onglets messages ────────────────────────────────────────
    document.querySelectorAll('.msg-tab-btn[data-msg-tab]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var group  = this.dataset.msgGroup;
            var target = this.dataset.msgTab;
            document.querySelectorAll('.msg-tab-btn[data-msg-group="' + group + '"]').forEach(function (b) {
                var isActive = b.dataset.msgTab === target;
                b.classList.toggle('active', isActive);
                activeTabClasses.forEach(function (cls) { b.classList.toggle(cls, isActive); });
                inactiveTabClasses.forEach(function (cls) { b.classList.toggle(cls, !isActive); });
            });
            document.querySelectorAll('.msg-pane[data-msg-group="' + group + '"]').forEach(function (p) {
                p.classList.toggle('active', p.dataset.msgPane === target);
            });
        });
    });
})();
</script>
</section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('portal.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mairie_wp\backend-laravel\resources\views/portal/dashboard.blade.php ENDPATH**/ ?>