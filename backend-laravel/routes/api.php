<?php

use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DemandeController;
use App\Http\Controllers\Api\MessageController;
use App\Http\Controllers\Api\WordPressContactMessageController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Routes publiques (sans authentification)
|--------------------------------------------------------------------------
*/
Route::prefix('v1')->group(function (): void {

    // --- Auth ---
    Route::post('/auth/register', [AuthController::class, 'register']);
    Route::post('/auth/login',    [AuthController::class, 'login']);

    // --- Soumission WordPress (via token applicatif mairie, sans JWT) ---
    Route::post('/demandes/wordpress', [DemandeController::class, 'store'])
        ->middleware('mairie.token');

    Route::post('/contact-messages/wordpress', [WordPressContactMessageController::class, 'store'])
        ->middleware('mairie.token');

    // --- Synchronisation WordPress -> Laravel (renouvellement JWT technique) ---
    Route::post('/auth/sync-token', [AuthController::class, 'issueSyncToken'])
        ->middleware('mairie.token');

    /*
    |--------------------------------------------------------------------------
    | Routes JWT protégées
    |--------------------------------------------------------------------------
    */
    Route::middleware('jwt.auth')->group(function (): void {

        // --- Auth connecté ---
        Route::post('/auth/logout',  [AuthController::class, 'logout']);
        Route::post('/auth/refresh', [AuthController::class, 'refresh']);
        Route::get('/auth/me',       [AuthController::class, 'me']);
        Route::put('/auth/profile',  [AuthController::class, 'updateProfile']);

        // --- Demandes : citoyen peut créer + consulter ses demandes ---
        Route::post('/demandes',         [DemandeController::class, 'store']);
        Route::get('/mes-demandes',      [DemandeController::class, 'index']);
        Route::get('/mes-demandes/{id}', [DemandeController::class, 'show']);

        // --- Actions agent / admin sur les demandes ---
        Route::middleware('role:agent,admin')->group(function (): void {
            Route::post('/demandes/{id}/assign',  [DemandeController::class, 'assign']);
            Route::post('/demandes/{id}/process', [DemandeController::class, 'process']);
            Route::get('/demandes/stats',         [DemandeController::class, 'stats']);
        });

        // --- Messagerie (toutes les parties sur leurs demandes) ---
        Route::get('/demandes/{demandeId}/messages',  [MessageController::class, 'index']);
        Route::post('/demandes/{demandeId}/messages', [MessageController::class, 'store']);
        Route::get('/messages/unread-count',          [MessageController::class, 'unreadCount']);

        /*
        |----------------------------------------------------------------------
        | Routes ADMIN uniquement
        |----------------------------------------------------------------------
        */
        Route::middleware('role:admin')->prefix('admin')->group(function (): void {
            Route::get('/dashboard',               [AdminController::class, 'dashboard']);
            Route::get('/activity-log',            [AdminController::class, 'activityLog']);
            Route::get('/users',                   [AdminController::class, 'listUsers']);
            Route::post('/users',                  [AdminController::class, 'createUser']);
            Route::patch('/users/{id}/toggle',     [AdminController::class, 'toggleUser']);
            Route::patch('/users/{id}/role',       [AdminController::class, 'changeRole']);
            Route::delete('/users/{id}',           [AdminController::class, 'deleteUser']);
        });
    });
});
