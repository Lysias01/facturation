<?php

namespace App\Observers;

use App\Models\Produit;
use App\Models\ActivityLog;

class ProduitObserver
{
    public function created(Produit $produit)
    {
        $this->log('created', $produit, "Création produit: {$produit->nom}");
    }

    public function updated(Produit $produit)
    {
        // Détecter les mouvements de stock
        if ($produit->wasChanged('stock')) {
            $oldStock = $produit->getOriginal('stock');
            $newStock = $produit->stock;
            $diff = $newStock - $oldStock;
            
            $type = $diff > 0 ? 'entrée' : 'sortie';
            $this->log('stock_update', $produit, "Stock {$produit->nom}: {$oldStock} → {$newStock} ({$type} de " . abs($diff) . ")");
            return;
        }

        $this->log('updated', $produit, "Modification produit: {$produit->nom}");
    }

    public function deleted(Produit $produit)
    {
        $this->log('deleted', $produit, "Suppression produit: {$produit->nom}");
    }

    private function log(string $action, Produit $produit, string $message)
    {
        ActivityLog::create([
            'user_id'    => auth()->id(),
            'action'     => $action,
            'model_type' => Produit::class,
            'model_id'   => $produit->id,
            'description'=> $message,
            'ip_address' => request()->ip(),
        ]);
    }
}
