

<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\FactureController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\PdfController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProduitController;
use App\Http\Controllers\ActivityLogController;

/*
|--------------------------------------------------------------------------
| Accueil
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return view('welcome');
});

/*
|--------------------------------------------------------------------------
| Dashboard
|--------------------------------------------------------------------------
*/
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->name('dashboard');

Route::get('/dashboard/export-excel', [DashboardController::class, 'exportExcel'])
    ->name('dashboard.exportExcel');

    //Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');

// Export PDF (journalier, mensuel, annuel)
Route::get('/dashboard/export-pdf/{period}', [DashboardController::class, 'exportPdf'])
    ->name('dashboard.exportPdf');

/*
|--------------------------------------------------------------------------
| Clients
|--------------------------------------------------------------------------
*/
Route::resource('clients', ClientController::class);

/*
|--------------------------------------------------------------------------
| Produits
|--------------------------------------------------------------------------
*/
Route::resource('produits', ProduitController::class);

/*
|--------------------------------------------------------------------------
| Réapprovisionnement des produits
|--------------------------------------------------------------------------
*/

// Affichage du formulaire & Traitement du formulaire
Route::match(['get', 'post'], '/produits/{produit}/reapprovisionnement', [ProduitController::class, 'reapprovisionnement'])
    ->name('produits.reapprovisionnement');


/*
|--------------------------------------------------------------------------
| Historique des mouvements de stock
|--------------------------------------------------------------------------
*/
Route::get(
    '/produits/{produit}/mouvements',
    [ProduitController::class, 'mouvements']
)->name('produits.mouvements');

/*
|--------------------------------------------------------------------------
| Factures
|--------------------------------------------------------------------------
*/
Route::resource('factures', FactureController::class);

/*
|--------------------------------------------------------------------------
| Validation pro-forma → reçu
|--------------------------------------------------------------------------
*/
Route::put(
    '/factures/{facture}/valider',
    [FactureController::class, 'valider']
)->name('factures.valider');

/*
|--------------------------------------------------------------------------
| Génération PDF facture
|--------------------------------------------------------------------------
*/
Route::get(
    '/factures/{facture}/pdf',
    [PdfController::class, 'generate']
)->name('factures.pdf');


Route::get(
    'factures/{facture}/pdf', 
    [PdfController::class, 'generate']
)->name('pdf.generate');

/*
|--------------------------------------------------------------------------
| Paramètres société
|--------------------------------------------------------------------------
*/
Route::get('/historique', [ActivityLogController::class, 'index'])->name('historique.index');



/*
|--------------------------------------------------------------------------
| Paramètres société
|--------------------------------------------------------------------------
*/
Route::get('/settings', [SettingController::class, 'edit'])
    ->name('settings.edit');

Route::put('/settings', [SettingController::class, 'update'])
    ->name('settings.update');

Route::delete('/settings/reset', [SettingController::class, 'reset'])->name('settings.reset');



