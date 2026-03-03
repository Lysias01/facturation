<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ActivityLogger
{
    /**
     * Enregistrer une activité avec un message simple
     */
    public static function log(array $data): ActivityLog
    {
        $user = Auth::user();
        
        return ActivityLog::create([
            'user_id' => $user?->id,
            'action' => $data['action'],
            'model_type' => $data['model_type'] ?? null,
            'model_id' => $data['model_id'] ?? null,
            'description' => $data['description'],
            'ip_address' => self::getIpAddress(),
            'impact_financier' => $data['impact_financier'] ?? null,
            'niveau_gravite' => $data['niveau_gravite'] ?? self::calculateGravity($data['action']),
            'old_data' => $data['old_data'] ?? null,
            'new_data' => $data['new_data'] ?? null,
        ]);
    }

    /**
     * Nouveau client ajouté
     */
    public static function created($model, string $description, ?float $impactFinancier = null): ActivityLog
    {
        return self::log([
            'action' => 'created',
            'model_type' => get_class($model),
            'model_id' => $model->id,
            'description' => $description,
            'impact_financier' => $impactFinancier,
            'new_data' => $model->toArray(),
        ]);
    }

    /**
     * Modification d'un élément
     */
    public static function updated($model, string $description, ?float $impactFinancier = null, ?array $oldData = null): ActivityLog
    {
        return self::log([
            'action' => 'updated',
            'model_type' => get_class($model),
            'model_id' => $model->id,
            'description' => $description,
            'impact_financier' => $impactFinancier,
            'niveau_gravite' => $impactFinancier ? 'warning' : null,
            'old_data' => $oldData,
            'new_data' => $model->toArray(),
        ]);
    }

    /**
     * Suppression d'un élément
     */
    public static function deleted($model, string $description): ActivityLog
    {
        return self::log([
            'action' => 'deleted',
            'model_type' => get_class($model),
            'model_id' => $model->id,
            'description' => $description,
            'niveau_gravite' => 'error',
            'old_data' => $model->toArray(),
        ]);
    }

    /**
     * Validation (ex: pro-forma → reçu)
     */
    public static function validated($model, string $description, ?float $impactFinancier = null): ActivityLog
    {
        return self::log([
            'action' => 'validated',
            'model_type' => get_class($model),
            'model_id' => $model->id,
            'description' => $description,
            'impact_financier' => $impactFinancier,
            'niveau_gravite' => $impactFinancier ? 'warning' : 'info',
        ]);
    }

    /**
     * Mouvement de stock
     */
    public static function stockUpdate($model, string $description, int $quantite = 0): ActivityLog
    {
        return self::log([
            'action' => 'stock_update',
            'model_type' => get_class($model),
            'model_id' => $model->id,
            'description' => $description,
            'niveau_gravite' => 'warning',
        ]);
    }

    /**
     * Connexion à l'application
     */
    public static function login(): ActivityLog
    {
        return self::log([
            'action' => 'login',
            'description' => 's\'est connecté à l\'application',
        ]);
    }

    /**
     * Déconnexion de l'application
     */
    public static function logout(): ActivityLog
    {
        return self::log([
            'action' => 'logout',
            'description' => 's\'est déconnecté de l\'application',
        ]);
    }

    /**
     * Obtenir l'adresse IP
     */
    private static function getIpAddress(): ?string
    {
        $request = request();
        
        if ($request) {
            return $request->ip();
        }
        
        return null;
    }

    /**
     * Calculer le niveau de gravité basé sur l'action
     */
    private static function calculateGravity(string $action): string
    {
        return match ($action) {
            'deleted' => 'error',
            'stock_update' => 'warning',
            'validated' => 'info',
            'login', 'logout' => 'info',
            default => 'info',
        };
    }
}
