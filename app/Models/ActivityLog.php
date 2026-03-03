<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $fillable = [
        'user_id',
        'action',
        'model_type',
        'model_id',
        'description',
        'ip_address',
        'impact_financier',
        'niveau_gravite',
        'old_data',
        'new_data',
    ];

    protected $casts = [
        'impact_financier' => 'decimal:2',
        'created_at' => 'datetime',
        'old_data' => 'array',
        'new_data' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Helper pour obtenir le type d'objet
    public function getModelTypeLabelAttribute()
    {
        return class_basename($this->model_type);
    }

    // Helper pour obtenir la gravité basée sur l'action
    public function getGraviteCalculeeAttribute()
    {
        if (in_array($this->action, ['deleted', 'stock_update'])) {
            return 'warning';
        }
        if (in_array($this->action, ['validated'])) {
            return 'info';
        }
        return $this->niveau_gravite ?? 'info';
    }
}
