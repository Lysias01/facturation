<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\FactureController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\PdfController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProduitController;
use App\Http\Controllers\ActivityLogController;

/*
|--------------------------------------------------------------------------
| AUTH - Public routes
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.perform');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| Protected routes - All authenticated users
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Dashboard - Different view based on role
    |--------------------------------------------------------------------------
    */
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard')
        ->middleware('role:admin');

    Route::get('/dashboard/employe', [DashboardController::class, 'employe'])
        ->name('dashboard.employe')
        ->middleware('role:employe');

    /*
    |--------------------------------------------------------------------------
    | Clients - Full access for both admin and employe
    |--------------------------------------------------------------------------
    */
    // Admin and Employe: full access to clients
    Route::resource('clients', ClientController::class);

    /*
    |--------------------------------------------------------------------------
    | Produits - Admin only (full access)
    |--------------------------------------------------------------------------
    */
    // Admin only: full resource CRUD + reapprovisionnement + ajustement
    Route::middleware('role:admin')->group(function () {
        Route::resource('produits', ProduitController::class);

        Route::match(['get', 'post'], '/produits/{produit}/reapprovisionnement',
            [ProduitController::class, 'reapprovisionnement'])
            ->name('produits.reapprovisionnement');

        Route::match(['get', 'post'], '/produits/{produit}/ajustement',
            [ProduitController::class, 'ajustement'])
            ->name('produits.ajustement');
    });

    /*
    |--------------------------------------------------------------------------
    | Produits - Employe (read-only + mouvements)
    |--------------------------------------------------------------------------
    */
    // Employe: read-only access to produits
    Route::get('/produits', [ProduitController::class, 'index'])->name('produits.index');
    Route::get('/produits/{produit}', [ProduitController::class, 'show'])->name('produits.show');
    Route::get('/produits/{produit}/mouvements', [ProduitController::class, 'mouvements'])->name('produits.mouvements');

    /*
    |--------------------------------------------------------------------------
    | Historique - Admin only
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:admin')->group(function () {
        Route::get('/historique', [ActivityLogController::class, 'index'])
            ->name('historique.index');

        // Export
        Route::get('/historique/export/excel', [ActivityLogController::class, 'exportExcel'])
            ->name('historique.export.excel');

        Route::get('/historique/export/pdf', [ActivityLogController::class, 'exportPdf'])
            ->name('historique.export.pdf');

        // Graphiques
        Route::get('/historique/chart', [ActivityLogController::class, 'chartData'])
            ->name('historique.chart');

        // Détails d'un log
        Route::get('/historique/{activityLog}/details', [ActivityLogController::class, 'details'])
            ->name('historique.details');

        // Nettoyage (admin uniquement)
        Route::delete('/historique/cleanup', [ActivityLogController::class, 'cleanup'])
            ->name('historique.cleanup');
    });

    /*
    |--------------------------------------------------------------------------
    | Admin only routes - Settings
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:admin')->group(function () {
        Route::get('/settings', [SettingController::class, 'edit'])
            ->name('settings.edit');

        Route::put('/settings', [SettingController::class, 'update'])
            ->name('settings.update');

        Route::delete('/settings/reset', [SettingController::class, 'reset'])
            ->name('settings.reset');

        // Gestion des utilisateurs
        Route::post('/settings/users', [SettingController::class, 'storeUser'])
            ->name('settings.users.store');

        Route::put('/settings/users/{user}', [SettingController::class, 'updateUser'])
            ->name('settings.users.update');

        Route::delete('/settings/users/{user}', [SettingController::class, 'destroyUser'])
            ->name('settings.users.destroy');

        Route::match(['get', 'patch'], '/settings/users/{user}/toggle', [SettingController::class, 'toggleUserStatus'])
            ->name('settings.users.toggle');

        Route::post('/settings/users/{user}/reset-password', [SettingController::class, 'resetPassword'])
            ->name('settings.users.reset-password');
    });

    /*
    |--------------------------------------------------------------------------
    | Factures - Accessible by both admin and employe
    |--------------------------------------------------------------------------
    */
    Route::resource('factures', FactureController::class);

    Route::put('/factures/{facture}/valider',
        [FactureController::class, 'valider'])
        ->name('factures.valider');

    Route::get('/factures/{facture}/pdf',
        [PdfController::class, 'generate'])
        ->name('pdf.generate');
});
