<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MouvementStock extends Model
{
    protected $table = 'mouvements_stock'; // 🔥 IMPORTANT

    protected $fillable = [
        'produit_id',
        'type',
        'quantite',
        'raison',
    ];

    public function produit()
    {
        return $this->belongsTo(Produit::class);
    }
}


