<?php

namespace App\Observers;

use App\Models\Facture;
use App\Models\ActivityLog;

class FactureObserver
{
    public function created(Facture $facture)
    {
        $typeLabel = $facture->type_document === 'pro-forma' ? 'Pro-forma' : 'Reçu';
        $impact = $facture->type_document === 'recu' ? $facture->total : null;
        $this->log('created', $facture, "Création {$typeLabel} {$facture->numero_facture}", $impact);
    }

    public function updated(Facture $facture)
    {
        // Détecter si c'est une validation pro-forma → reçu
        if ($facture->wasChanged('type_document') && 
            $facture->getOriginal('type_document') === 'pro-forma' && 
            $facture->type_document === 'recu') {
            $this->log('validated', $facture, "Validation pro-forma {$facture->getOriginal('numero_facture')} → Reçu {$facture->numero_facture}", $facture->total);
            return;
        }

        $this->log('updated', $facture, 'Modification du document');
    }

    public function deleted(Facture $facture)
    {
        $this->log('deleted', $facture, "Suppression {$facture->type_document} {$facture->numero_facture}");
    }

    private function log(string $action, Facture $facture, string $message, ?float $impactFinancier = null)
    {
        ActivityLog::create([
            'user_id'           => auth()->id(),
            'action'            => $action,
            'model_type'        => Facture::class,
            'model_id'          => $facture->id,
            'description'       => $message,
            'ip_address'        => request()->ip(),
            'impact_financier'  => $impactFinancier,
            'niveau_gravite'    => in_array($action, ['deleted']) ? 'error' : 'info',
        ]);
    }
}
