<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produit extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'prix_vente',
        'prix_achat',
        'stock',
        'seuil_alerte'
    ];

    // Relation avec les lignes de facture
    public function lignesFacture()
    {
        return $this->hasMany(LigneFacture::class);
    }

    // Relation avec les mouvements de stock
    public function mouvementsStock()
    {
        return $this->hasMany(MouvementStock::class);
    }

    // Calcul du stock actuel à la volée
    public function getStockActuelAttribute()
    {
        $sorties = $this->mouvementsStock()->where('type', 'sortie')->sum('quantite');
        $entrees = $this->mouvementsStock()->where('type', 'entree')->sum('quantite');

        return max($this->stock + $entrees - $sorties, 0);
    }

    // Indicateur de stock critique
    public function getStockCritiqueAttribute()
    {
        return $this->stock_actuel <= $this->seuil_alerte;
    }
}
