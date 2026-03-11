<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use App\Models\Setting;
use App\Models\Facture;
use App\Models\Client;
use App\Models\Produit;
use App\Observers\FactureObserver;
use App\Observers\ClientObserver;
use App\Observers\ProduitObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Fix pour PostgreSQL sur Render
        Schema::defaultStringLength(191);

        /**
         * Exécution automatique des migrations sur Render
         * Cette partie s'exécute seulement si la table users n'existe pas
         */
        if (env('APP_ENV') === 'production' && env('APP_URL') && str_contains(env('APP_URL'), 'render.com')) {
            try {
                // Vérifier si les tables existent
                if (!Schema::hasTable('users')) {
                    \Illuminate\Support\Facades\Artisan::call('migrate --force');
                    \Illuminate\Support\Facades\Artisan::call('db:seed --force');
                }
            } catch (\Exception $e) {
                // Ignorer les erreurs si la DB n'est pas encore prête
            }
        }

        /**
         * 1️⃣ Injection globale des paramètres de l'application
         */
        view()->composer('*', function ($view) {

            $settings = Setting::first();

            // Si la table settings est vide → on purge le cache
            if (!$settings) {
                Cache::forget('app_settings');
            }

            $view->with('app_settings', $settings);
        });

/**
         * 2️⃣ Observers → Historique & traçabilité automatique
         */
        Facture::observe(FactureObserver::class);
        Client::observe(ClientObserver::class);
        Produit::observe(ProduitObserver::class);
    }
}
