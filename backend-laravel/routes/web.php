<?php

use App\Http\Controllers\Web\PortalController;
use App\Http\Controllers\Web\CivicAdminController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('portal.auth');
});
Route::get('/recensement', [PortalController::class, 'recensement'])->name('portal.citoyen.recensement');
Route::post('/recensement', [PortalController::class, 'storeRecensement'])->name('portal.citoyen.recensement.store');
Route::get('/connexion', [PortalController::class, 'showAuth'])->name('portal.auth');
Route::post('/connexion', [PortalController::class, 'login'])->name('portal.login');
Route::post('/inscription', [PortalController::class, 'register'])->name('portal.register');
Route::post('/deconnexion', [PortalController::class, 'logout'])->name('portal.logout');

Route::middleware('portal.auth')->group(function (): void {
    Route::get('/portail', [PortalController::class, 'entry'])->name('portal.entry');

    Route::get('/portail/citoyen', [PortalController::class, 'citizenDashboard'])
        ->middleware('portal.role:citoyen')
        ->name('portal.citizen');

    Route::post('/portail/citoyen/demandes', [PortalController::class, 'storeCitizenDemande'])
        ->middleware('portal.role:citoyen')
        ->name('portal.citizen.demandes.store');

    Route::post('/portail/citoyen/profil', [PortalController::class, 'saveCitizenProfile'])
        ->middleware('portal.role:citoyen')
        ->name('portal.citizen.profile.save');

    Route::post('/portail/citoyen/demandes/{demande}/payer', [PortalController::class, 'payCitizenDemande'])
        ->middleware('portal.role:citoyen')
        ->name('portal.citizen.demandes.pay');



    Route::get('/portail/agent', [PortalController::class, 'agentDashboard'])
        ->middleware('portal.role:agent')
        ->name('portal.agent');

    Route::get('/portail/superviseur', [PortalController::class, 'superviseurDashboard'])
        ->middleware('portal.role:superviseur')
        ->name('portal.superviseur');

    Route::post('/portail/superviseur/pointage', [PortalController::class, 'storeSuperviseurAgentAttendance'])
        ->middleware('portal.role:superviseur')
        ->name('portal.superviseur.attendance.store');

    Route::get('/portail/agent/performance', [PortalController::class, 'agentPerformance'])
        ->middleware('portal.role:agent')
        ->name('portal.agent.performance');

    Route::post('/portail/agent/profil', [PortalController::class, 'saveAgentProfile'])
        ->middleware('portal.role:agent')
        ->name('portal.agent.profile.save');

    Route::post('/portail/agent/demandes/{demande}/valider-paiement', [PortalController::class, 'validateAgentPayment'])
        ->middleware('portal.role:agent')
        ->name('portal.agent.demandes.payment.validate');

    Route::post('/portail/agent/publipostage/template/{requestType}', [PortalController::class, 'uploadAgentMailMergeTemplate'])
        ->middleware('portal.role:agent')
        ->name('portal.agent.mailmerge.template.upload');

    Route::get('/portail/agent/publipostage/template/{requestType}', [PortalController::class, 'downloadAgentMailMergeTemplate'])
        ->middleware('portal.role:agent')
        ->name('portal.agent.mailmerge.template.download');

    Route::get('/portail/agent/publipostage/{requestType}/excel', [PortalController::class, 'agentMailingExport'])
        ->middleware('portal.role:agent')
        ->name('portal.agent.mailmerge.export');

    Route::get('/portail/admin', [PortalController::class, 'adminDashboard'])
        ->middleware('portal.role:admin')
        ->name('portal.admin');

    Route::get('/portail/demandes/{demande}', [PortalController::class, 'showDemande'])
        ->name('portal.demandes.show');

    Route::get('/portail/demandes/{demande}/document', [PortalController::class, 'downloadDemandeDocument'])
        ->name('portal.demandes.document.download');

    Route::post('/portail/demandes/{demande}/messages', [PortalController::class, 'storeDemandeMessage'])
        ->name('portal.demandes.messages.store');

    Route::post('/portail/messages', [PortalController::class, 'storePortalMessage'])
        ->middleware('portal.role:citoyen,agent,admin')
        ->name('portal.messages.store');

    Route::post('/portail/demandes/{demande}/assign', [PortalController::class, 'assignDemande'])
        ->middleware('portal.role:agent,admin')
        ->name('portal.demandes.assign');

    Route::post('/portail/demandes/{demande}/process', [PortalController::class, 'processDemande'])
        ->middleware('portal.role:agent,admin')
        ->name('portal.demandes.process');

    Route::post('/portail/admin/utilisateurs', [PortalController::class, 'storeUser'])
        ->middleware('portal.role:admin')
        ->name('portal.admin.users.store');

    Route::post('/portail/admin/utilisateurs/{user}/toggle', [PortalController::class, 'toggleUser'])
        ->middleware('portal.role:admin')
        ->name('portal.admin.users.toggle');

    Route::post('/portail/admin/utilisateurs/{user}/role', [PortalController::class, 'changeUserRole'])
        ->middleware('portal.role:admin')
        ->name('portal.admin.users.role');

        // --- Admin sub-pages ---
        Route::get('/portail/admin/agents', [PortalController::class, 'adminAgents'])
            ->middleware('portal.role:admin')
            ->name('portal.admin.agents');

        Route::post('/portail/admin/agents/creer', [PortalController::class, 'storeUser'])
            ->middleware('portal.role:admin')
            ->name('portal.admin.agents.store');

        Route::post('/portail/admin/agents/{user}/modifier', [PortalController::class, 'updateAgent'])
            ->middleware('portal.role:admin')
            ->name('portal.admin.agents.update');

        Route::post('/portail/admin/agents/{user}/supprimer', [PortalController::class, 'deleteUser'])
            ->middleware('portal.role:admin')
            ->name('portal.admin.agents.delete');

        Route::get('/portail/admin/demandes', [PortalController::class, 'adminDemandes'])
            ->middleware('portal.role:admin')
            ->name('portal.admin.demandes');

        Route::get('/portail/admin/messages', [PortalController::class, 'adminMessages'])
            ->middleware('portal.role:admin')
            ->name('portal.admin.messages');

        Route::post('/portail/admin/messages/wordpress/{contactMessage}/reply', [PortalController::class, 'replyWordPressContactMessage'])
            ->middleware('portal.role:admin')
            ->name('portal.admin.messages.wordpress.reply');

        Route::get('/portail/admin/citoyens', [PortalController::class, 'adminCitoyens'])
            ->middleware('portal.role:admin')
            ->name('portal.admin.citoyens');

        Route::get('/portail/admin/citoyens/export', [PortalController::class, 'adminCitoyensExport'])
            ->middleware('portal.role:admin')
            ->name('portal.admin.citoyens.export');

        Route::get('/portail/admin/registre-citoyens', [PortalController::class, 'adminCitizenRegistry'])
            ->middleware('portal.role:admin')
            ->name('portal.admin.citizen_registry');

        Route::get('/portail/admin/registre-citoyens/{registry}/cv', [PortalController::class, 'downloadCitizenCV'])
            ->middleware('portal.role:admin')
            ->name('portal.admin.citizen_registry.download_cv');

        Route::get('/portail/admin/statistiques', [PortalController::class, 'adminStats'])
            ->middleware('portal.role:admin')
            ->name('portal.admin.stats');

        Route::get('/portail/admin/documents/architecture', [PortalController::class, 'downloadAdminArchitectureDoc'])
            ->middleware('portal.role:admin')
            ->name('portal.admin.documents.architecture.download');

        Route::get('/portail/admin/parametres', [PortalController::class, 'adminSettings'])
            ->middleware('portal.role:admin')
            ->name('portal.admin.settings');

        Route::post('/portail/admin/parametres', [PortalController::class, 'saveAdminSettings'])
            ->middleware('portal.role:admin')
            ->name('portal.admin.settings.save');

        Route::get('/portail/admin/profil', [PortalController::class, 'adminProfile'])
            ->middleware('portal.role:admin')
            ->name('portal.admin.profile');

        Route::post('/portail/admin/profil', [PortalController::class, 'saveAdminProfile'])
            ->middleware('portal.role:admin')
            ->name('portal.admin.profile.save');

        // --- Civic Courses & Activities Management ---
        Route::get('/portail/admin/cours-citoyens', [CivicAdminController::class, 'indexCourses'])
            ->middleware('portal.role:admin')
            ->name('portal.admin.civic_courses');

        Route::get('/portail/admin/cours-citoyens/creer', [CivicAdminController::class, 'createCourse'])
            ->middleware('portal.role:admin')
            ->name('portal.admin.civic_courses.create');

        Route::post('/portail/admin/cours-citoyens', [CivicAdminController::class, 'storeCourse'])
            ->middleware('portal.role:admin')
            ->name('portal.admin.civic_courses.store');

        Route::get('/portail/admin/cours-citoyens/{civicCourse}/modifier', [CivicAdminController::class, 'editCourse'])
            ->middleware('portal.role:admin')
            ->name('portal.admin.civic_courses.edit');

        Route::post('/portail/admin/cours-citoyens/{civicCourse}', [CivicAdminController::class, 'updateCourse'])
            ->middleware('portal.role:admin')
            ->name('portal.admin.civic_courses.update');

        Route::post('/portail/admin/cours-citoyens/{civicCourse}/supprimer', [CivicAdminController::class, 'destroyCourse'])
            ->middleware('portal.role:admin')
            ->name('portal.admin.civic_courses.destroy');

        Route::get('/portail/admin/activites-communautaires', [CivicAdminController::class, 'indexActivities'])
            ->middleware('portal.role:admin')
            ->name('portal.admin.civic_activities');

        Route::get('/portail/admin/activites-communautaires/creer', [CivicAdminController::class, 'createActivity'])
            ->middleware('portal.role:admin')
            ->name('portal.admin.civic_activities.create');

        Route::post('/portail/admin/activites-communautaires', [CivicAdminController::class, 'storeActivity'])
            ->middleware('portal.role:admin')
            ->name('portal.admin.civic_activities.store');

        Route::get('/portail/admin/activites-communautaires/{civicActivity}/modifier', [CivicAdminController::class, 'editActivity'])
            ->middleware('portal.role:admin')
            ->name('portal.admin.civic_activities.edit');

        Route::post('/portail/admin/activites-communautaires/{civicActivity}', [CivicAdminController::class, 'updateActivity'])
            ->middleware('portal.role:admin')
            ->name('portal.admin.civic_activities.update');

        Route::post('/portail/admin/activites-communautaires/{civicActivity}/supprimer', [CivicAdminController::class, 'destroyActivity'])
            ->middleware('portal.role:admin')
            ->name('portal.admin.civic_activities.destroy');

        // Admin routes to view registrations and course views
        Route::get('/portail/admin/activites-communautaires/{civicActivity}/inscriptions', [CivicAdminController::class, 'viewActivityRegistrations'])
            ->middleware('portal.role:admin')
            ->name('portal.admin.civic_activities.registrations');

        Route::get('/portail/admin/cours-citoyens/{civicCourse}/lecteurs', [CivicAdminController::class, 'viewCourseReaders'])
            ->middleware('portal.role:admin')
            ->name('portal.admin.civic_courses.readers');

    // Routes citoyennes pour cours et activités
    Route::get('/portail/citoyen/cours/{civicCourse}', [PortalController::class, 'viewCourse'])
        ->middleware('portal.role:citoyen')
        ->name('portal.citizen.course.view');

    Route::post('/portail/citoyen/activites/{civicActivity}/inscrire', [PortalController::class, 'registerActivity'])
        ->middleware('portal.role:citoyen')
        ->name('portal.citizen.activity.register');
});

