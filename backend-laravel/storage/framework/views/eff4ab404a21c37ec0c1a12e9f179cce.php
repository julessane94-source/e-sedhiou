

<?php $__env->startSection('content'); ?>
    <?php echo $__env->make('portal.components.admin-nav', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <section class="hero tw:pb-8">
        <div class="hero__panel">
            <span class="eyebrow">Administration</span>
            <h1 class="tw:font-semibold tw:text-3xl">Registre des citoyens</h1>
            <p class="muted tw:mt-2">Consultez et gérez les citoyens qui se sont auto-enregistrés pour les services municipaux</p>
            <div class="tw:flex tw:gap-4 tw:mt-4">
                <div class="tw:px-4 tw:py-3 tw:bg-emerald-50 tw:border-l-4 tw:border-emerald-600 tw:rounded">
                    <p class="tw:text-sm tw:text-gray-600">Disponibilité pour la mairie</p>
                    <p class="tw:font-bold tw:text-emerald-700 tw:text-lg"><?php echo e($availableCount); ?> / <?php echo e($totalRegistrations); ?></p>
                </div>
            </div>
        </div>
    </section>

    <!-- Barre de filtrage & Recherche -->
    <div class="panel tw:mb-6">
        <div class="panel-header tw:mb-6">
            <div class="panel-header__title">
                <span class="eyebrow">Recherche & Filtrage</span>
                <h2 class="tw:font-semibold tw:text-xl">Outils de consultation</h2>
            </div>
        </div>

        <form method="get" action="<?php echo e(route('portal.admin.citizen_registry')); ?>" class="tw:grid tw:grid-cols-1 md:tw:grid-cols-4 tw:gap-4 tw:p-4 tw:bg-gray-50 tw:rounded-lg">
            <div class="field">
                <label for="f-search" class="tw:block tw:text-sm tw:font-medium tw:text-gray-700 tw:mb-1">Recherche</label>
                <input id="f-search" 
                       name="search" 
                       type="text" 
                       value="<?php echo e($searchTerm); ?>" 
                       placeholder="Nom, email, téléphone..." 
                       class="tw:w-full tw:border tw:border-gray-300 tw:rounded-lg tw:px-4 tw:py-2 tw:text-gray-700 tw:bg-white focus:tw:outline-none focus:tw:ring-2 focus:tw:ring-emerald-500 focus:tw:border-transparent">
            </div>

            <div class="field">
                <label for="f-sector" class="tw:block tw:text-sm tw:font-medium tw:text-gray-700 tw:mb-1">Secteur d'activité</label>
                <select id="f-sector" 
                        name="sector"
                        class="tw:w-full tw:border tw:border-gray-300 tw:rounded-lg tw:px-4 tw:py-2 tw:text-gray-700 tw:bg-white focus:tw:outline-none focus:tw:ring-2 focus:tw:ring-emerald-500 focus:tw:border-transparent">
                    <option value="">Tous les secteurs</option>
                    <?php $__currentLoopData = $sectors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sector): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($sector); ?>" <?php if($filterSector === $sector): echo 'selected'; endif; ?>><?php echo e($sector); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>

            <div class="field">
                <label for="f-available" class="tw:block tw:text-sm tw:font-medium tw:text-gray-700 tw:mb-1">Disponibilité</label>
                <select id="f-available" 
                        name="available"
                        class="tw:w-full tw:border tw:border-gray-300 tw:rounded-lg tw:px-4 tw:py-2 tw:text-gray-700 tw:bg-white focus:tw:outline-none focus:tw:ring-2 focus:tw:ring-emerald-500 focus:tw:border-transparent">
                    <option value="">Tous</option>
                    <option value="1" <?php if($filterAvailable === '1'): echo 'selected'; endif; ?>>✅ Disponible</option>
                    <option value="0" <?php if($filterAvailable === '0'): echo 'selected'; endif; ?>>❌ Non disponible</option>
                </select>
            </div>

            <div class="tw:flex tw:gap-2 tw:items-end">
                <button class="button button--accent tw:flex-1 tw:px-6" type="submit">
                    🔍 Filtrer
                </button>
                <a class="button button--ghost tw:flex-1 tw:text-center" href="<?php echo e(route('portal.admin.citizen_registry')); ?>">
                    ↻ Réinit.
                </a>
            </div>
        </form>
    </div>

    <!-- Tableau de registre -->
    <div class="panel">
        <div class="panel-header tw:mb-4">
            <div class="panel-header__title">
                <span class="eyebrow">Résultats</span>
                <h2 class="tw:font-semibold tw:text-lg">Liste des citoyens enregistrés</h2>
            </div>
        </div>

        <div class="tw:overflow-x-auto">
            <table class="tw:w-full tw:border-collapse tw:text-sm">
                <thead>
                    <tr class="tw:bg-gradient-to-r tw:from-emerald-600 tw:to-emerald-700 tw:text-white">
                        <th class="tw:text-left tw:px-4 tw:py-3 tw:font-semibold">Identité</th>
                        <th class="tw:text-left tw:px-4 tw:py-3 tw:font-semibold">Contact</th>
                        <th class="tw:text-left tw:px-4 tw:py-3 tw:font-semibold">Profession</th>
                        <th class="tw:text-center tw:px-4 tw:py-3 tw:font-semibold">Exp.</th>
                        <th class="tw:text-center tw:px-4 tw:py-3 tw:font-semibold">Disponible</th>
                        <th class="tw:text-center tw:px-4 tw:py-3 tw:font-semibold">CV</th>
                        <th class="tw:text-center tw:px-4 tw:py-3 tw:font-semibold">Date</th>
                        <th class="tw:text-center tw:px-4 tw:py-3 tw:font-semibold">Action</th>
                    </tr>
                </thead>
                <tbody class="tw:divide-y tw:divide-gray-200">
                    <?php $__empty_1 = true; $__currentLoopData = $registrations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $registration): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:tw:bg-gray-50 tw:transition-colors">
                            <td class="tw:px-4 tw:py-4">
                                <div class="tw:font-semibold tw:text-gray-900">
                                    <?php echo e($registration->first_name); ?> <?php echo e($registration->last_name); ?>

                                </div>
                                <div class="tw:text-xs tw:text-gray-500 tw:mt-1">
                                    📋 #<?php echo e(str_pad($registration->register_number, 6, '0', STR_PAD_LEFT)); ?>

                                </div>
                            </td>
                            <td class="tw:px-4 tw:py-4">
                                <div class="tw:flex tw:flex-col tw:gap-1">
                                    <a href="mailto:<?php echo e($registration->email); ?>" 
                                       class="tw:text-emerald-600 hover:tw:text-emerald-700 tw:underline tw:text-xs">
                                        ✉️ <?php echo e($registration->email); ?>

                                    </a>
                                    <a href="tel:<?php echo e($registration->phone); ?>" 
                                       class="tw:text-emerald-600 hover:tw:text-emerald-700 tw:underline tw:text-xs">
                                        📱 <?php echo e($registration->phone); ?>

                                    </a>
                                </div>
                            </td>
                            <td class="tw:px-4 tw:py-4">
                                <div class="tw:font-medium tw:text-gray-900"><?php echo e($registration->profession_title); ?></div>
                                <div class="tw:text-xs tw:text-gray-500 tw:mt-1"><?php echo e($registration->profession_sector); ?></div>
                            </td>
                            <td class="tw:px-4 tw:py-4 tw:text-center">
                                <span class="tw:inline-block tw:px-3 tw:py-1 tw:bg-blue-100 tw:text-blue-800 tw:rounded-full tw:text-xs tw:font-semibold">
                                    <?php echo e($registration->years_experience); ?> ans
                                </span>
                            </td>
                            <td class="tw:px-4 tw:py-4 tw:text-center">
                                <?php if($registration->available_for_municipality): ?>
                                    <span class="tw:inline-block tw:px-3 tw:py-1 tw:bg-emerald-100 tw:text-emerald-800 tw:rounded-full tw:text-xs tw:font-bold">
                                        ✅ Oui
                                    </span>
                                <?php else: ?>
                                    <span class="tw:inline-block tw:px-3 tw:py-1 tw:bg-gray-200 tw:text-gray-700 tw:rounded-full tw:text-xs tw:font-semibold">
                                        ○ Non
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="tw:px-4 tw:py-4 tw:text-center">
                                <?php if($registration->cv_file_path): ?>
                                    <a href="<?php echo e(route('portal.admin.citizen_registry.download_cv', $registration)); ?>" 
                                       class="tw:inline-flex tw:items-center tw:justify-center tw:px-3 tw:py-2 tw:bg-blue-500 hover:tw:bg-blue-600 tw:text-white tw:rounded-md tw:text-xs tw:font-semibold tw:transition-colors"
                                       title="Télécharger le CV"
                                       download>
                                        📄 CV
                                    </a>
                                <?php else: ?>
                                    <span class="tw:text-gray-400 tw:text-xs">—</span>
                                <?php endif; ?>
                            </td>
                            <td class="tw:px-4 tw:py-4 tw:text-center tw:text-xs tw:text-gray-600">
                                <?php echo e($registration->created_at->format('d/m/y')); ?><br>
                                <span class="tw:text-gray-500"><?php echo e($registration->created_at->format('H:i')); ?></span>
                            </td>
                            <td class="tw:px-4 tw:py-4 tw:text-center">
                                <button type="button" 
                                        class="tw:inline-flex tw:items-center tw:justify-center tw:px-4 tw:py-2 tw:bg-emerald-600 hover:tw:bg-emerald-700 tw:text-white tw:rounded-md tw:text-xs tw:font-semibold tw:transition-colors"
                                        onclick="openDetailModal(<?php echo e($registration->id); ?>, `<?php echo e(addslashes($registration->first_name)); ?> <?php echo e(addslashes($registration->last_name)); ?>`)">
                                    👁️ Voir
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="8" class="tw:text-center tw:px-4 tw:py-8 tw:text-gray-500">
                                <div class="tw:text-4xl tw:mb-2">🔍</div>
                                <p class="tw:font-medium">Aucun citoyen enregistré correspondant aux critères.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <?php if($registrations->hasPages()): ?>
            <div class="tw:mt-6 tw:flex tw:justify-center">
                <div class="tw:flex tw:gap-2">
                    <?php echo e($registrations->appends(request()->query())->links()); ?>

                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Modal Détails Citoyen -->
    <div id="detailModal" class="tw:fixed tw:inset-0 tw:bg-black/60 tw:hidden tw:items-center tw:justify-center tw:z-50 tw:p-4 tw:backdrop-blur-sm">
        <div class="tw:bg-white tw:rounded-2xl tw:max-w-3xl tw:w-full tw:max-h-[90vh] tw:overflow-y-auto tw:shadow-2xl">
            <!-- Modal Header -->
            <div class="tw:sticky tw:top-0 tw:bg-gradient-to-r tw:from-emerald-600 tw:to-emerald-700 tw:text-white tw:px-6 tw:py-4 tw:flex tw:justify-between tw:items-center tw:rounded-t-2xl">
                <h2 id="modalTitle" class="tw:text-2xl tw:font-bold"></h2>
                <button onclick="closeDetailModal()" class="tw:text-white hover:tw:bg-white/20 tw:rounded-lg tw:p-1 tw:transition-colors">
                    <svg class="tw:w-6 tw:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Modal Content -->
            <div id="modalContent" class="tw:p-6 tw:space-y-6">
                <!-- Contenu chargé dynamiquement -->
            </div>
        </div>
    </div>

    <script>
        function openDetailModal(id, name) {
            const modal = document.getElementById('detailModal');
            const modalContent = document.getElementById('modalContent');
            
            modal.classList.remove('tw:hidden');
            modal.classList.add('tw:flex');
            document.getElementById('modalTitle').textContent = name;

            const registration = <?php echo json_encode($registrations->items(), 15, 512) ?>;
            const citizen = registration.find(r => r.id === id);

            if (citizen) {
                const html = `
                    <!-- Section Identité -->
                    <div class="tw:bg-gray-50 tw:rounded-lg tw:p-4 tw:border tw:border-gray-200">
                        <h3 class="tw:text-lg tw:font-bold tw:text-gray-900 tw:mb-3">📋 Identité</h3>
                        <div class="tw:grid tw:grid-cols-3 tw:gap-4">
                            <div>
                                <span class="tw:block tw:text-xs tw:uppercase tw:tracking-wide tw:font-semibold tw:text-gray-600">Prénom</span>
                                <p class="tw:mt-1 tw:text-gray-900 tw:font-medium">${citizen.first_name}</p>
                            </div>
                            <div>
                                <span class="tw:block tw:text-xs tw:uppercase tw:tracking-wide tw:font-semibold tw:text-gray-600">Nom</span>
                                <p class="tw:mt-1 tw:text-gray-900 tw:font-medium">${citizen.last_name}</p>
                            </div>
                            <div>
                                <span class="tw:block tw:text-xs tw:uppercase tw:tracking-wide tw:font-semibold tw:text-gray-600">Numéro</span>
                                <p class="tw:mt-1 tw:text-gray-900 tw:font-medium">#${String(citizen.register_number).padStart(6, '0')}</p>
                            </div>
                            <div>
                                <span class="tw:block tw:text-xs tw:uppercase tw:tracking-wide tw:font-semibold tw:text-gray-600">Date de naissance</span>
                                <p class="tw:mt-1 tw:text-gray-900">${new Date(citizen.birth_date).toLocaleDateString('fr-FR')}</p>
                            </div>
                            <div>
                                <span class="tw:block tw:text-xs tw:uppercase tw:tracking-wide tw:font-semibold tw:text-gray-600">Lieu de naissance</span>
                                <p class="tw:mt-1 tw:text-gray-900">${citizen.birth_place}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Section Contact -->
                    <div class="tw:bg-blue-50 tw:rounded-lg tw:p-4 tw:border tw:border-blue-200">
                        <h3 class="tw:text-lg tw:font-bold tw:text-gray-900 tw:mb-3">📞 Contact</h3>
                        <div class="tw:grid tw:grid-cols-2 tw:gap-4">
                            <div>
                                <span class="tw:block tw:text-xs tw:uppercase tw:tracking-wide tw:font-semibold tw:text-gray-600">Email</span>
                                <p class="tw:mt-1"><a href="mailto:${citizen.email}" class="tw:text-emerald-600 hover:tw:text-emerald-700 tw:underline">${citizen.email}</a></p>
                            </div>
                            <div>
                                <span class="tw:block tw:text-xs tw:uppercase tw:tracking-wide tw:font-semibold tw:text-gray-600">Téléphone</span>
                                <p class="tw:mt-1"><a href="tel:${citizen.phone}" class="tw:text-emerald-600 hover:tw:text-emerald-700 tw:underline">${citizen.phone}</a></p>
                            </div>
                            <div class="tw:col-span-2">
                                <span class="tw:block tw:text-xs tw:uppercase tw:tracking-wide tw:font-semibold tw:text-gray-600">Adresse</span>
                                <p class="tw:mt-1 tw:text-gray-900">${citizen.address}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Section Profession -->
                    <div class="tw:bg-purple-50 tw:rounded-lg tw:p-4 tw:border tw:border-purple-200">
                        <h3 class="tw:text-lg tw:font-bold tw:text-gray-900 tw:mb-3">💼 Profession</h3>
                        <div class="tw:grid tw:grid-cols-2 tw:gap-4">
                            <div>
                                <span class="tw:block tw:text-xs tw:uppercase tw:tracking-wide tw:font-semibold tw:text-gray-600">Secteur d'activité</span>
                                <p class="tw:mt-1 tw:text-gray-900 tw:font-medium">${citizen.profession_sector}</p>
                            </div>
                            <div>
                                <span class="tw:block tw:text-xs tw:uppercase tw:tracking-wide tw:font-semibold tw:text-gray-600">Métier / Titre</span>
                                <p class="tw:mt-1 tw:text-gray-900 tw:font-medium">${citizen.profession_title}</p>
                            </div>
                            <div>
                                <span class="tw:block tw:text-xs tw:uppercase tw:tracking-wide tw:font-semibold tw:text-gray-600">Niveau d'études</span>
                                <p class="tw:mt-1 tw:text-gray-900">${citizen.education_level}</p>
                            </div>
                            <div>
                                <span class="tw:block tw:text-xs tw:uppercase tw:tracking-wide tw:font-semibold tw:text-gray-600">Expérience</span>
                                <p class="tw:mt-1 tw:text-gray-900 tw:font-medium">${citizen.years_experience} ans</p>
                            </div>
                            <div class="tw:col-span-2">
                                <span class="tw:block tw:text-xs tw:uppercase tw:tracking-wide tw:font-semibold tw:text-gray-600">Compétences</span>
                                <p class="tw:mt-1 tw:text-gray-900">${citizen.skills || 'Non spécifiées'}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Section Disponibilité -->
                    <div class="tw:bg-emerald-50 tw:rounded-lg tw:p-4 tw:border tw:border-emerald-200">
                        <h3 class="tw:text-lg tw:font-bold tw:text-gray-900 tw:mb-3">✅ Disponibilité</h3>
                        <div class="tw:grid tw:grid-cols-2 tw:gap-4">
                            <div>
                                <span class="tw:block tw:text-xs tw:uppercase tw:tracking-wide tw:font-semibold tw:text-gray-600">Statut actuel</span>
                                <p class="tw:mt-1 tw:text-gray-900">${citizen.current_status}</p>
                            </div>
                            <div>
                                <span class="tw:block tw:text-xs tw:uppercase tw:tracking-wide tw:font-semibold tw:text-gray-600">Pour la mairie</span>
                                <p class="tw:mt-1">
                                    ${citizen.available_for_municipality ? '<span class="tw:inline-block tw:px-3 tw:py-1 tw:bg-emerald-200 tw:text-emerald-800 tw:rounded-full tw:text-sm tw:font-bold">✅ Disponible</span>' : '<span class="tw:inline-block tw:px-3 tw:py-1 tw:bg-gray-200 tw:text-gray-700 tw:rounded-full tw:text-sm tw:font-semibold">Non spécifié</span>'}
                                </p>
                            </div>
                            ${citizen.portfolio_url ? `
                                <div class="tw:col-span-2">
                                    <span class="tw:block tw:text-xs tw:uppercase tw:tracking-wide tw:font-semibold tw:text-gray-600">Portfolio / Site Web</span>
                                    <p class="tw:mt-1"><a href="${citizen.portfolio_url}" target="_blank" class="tw:text-emerald-600 hover:tw:text-emerald-700 tw:underline">${citizen.portfolio_url}</a></p>
                                </div>
                            ` : ''}
                        </div>
                    </div>

                    <!-- CV et Documents -->
                    ${citizen.cv_file_path ? `
                        <div class="tw:bg-yellow-50 tw:rounded-lg tw:p-4 tw:border tw:border-yellow-200">
                            <h3 class="tw:text-lg tw:font-bold tw:text-gray-900 tw:mb-3">📄 Documents</h3>
                            <p class="tw:text-sm tw:text-gray-600 tw:mb-3">CV: <strong>${citizen.cv_file_name}</strong></p>
                            <a href="/portail/admin/registre-citoyens/${citizen.id}/cv" class="tw:inline-flex tw:items-center tw:gap-2 tw:px-4 tw:py-2 tw:bg-blue-600 hover:tw:bg-blue-700 tw:text-white tw:rounded-lg tw:font-semibold tw:transition-colors" download>
                                📥 Télécharger le CV
                            </a>
                        </div>
                    ` : ''}

                    <!-- Métadonnées -->
                    <div class="tw:bg-gray-100 tw:rounded-lg tw:p-3 tw:text-xs tw:text-gray-600">
                        Inscrit le <strong>${new Date(citizen.created_at).toLocaleDateString('fr-FR')} à ${new Date(citizen.created_at).toLocaleTimeString('fr-FR')}</strong>
                    </div>
                `;
                modalContent.innerHTML = html;
            }
        }

        function closeDetailModal() {
            const modal = document.getElementById('detailModal');
            modal.classList.add('tw:hidden');
            modal.classList.remove('tw:flex');
        }

        // Fermer le modal en cliquant en dehors
        document.getElementById('detailModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeDetailModal();
            }
        });

        // Fermer avec la touche Escape
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeDetailModal();
            }
        });
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('portal.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mairie_wp\backend-laravel\resources\views/portal/admin-citizen-registry.blade.php ENDPATH**/ ?>