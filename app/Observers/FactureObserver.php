<?php

namespace App\Observers;

use App\Models\Facture;
use App\Models\ActivityLog;

class FactureObserver
{
    public function created(Facture $facture)
    {
        $this->log('created', $facture, 'Création du document');
    }

    public function updated(Facture $facture)
    {
        $this->log('updated', $facture, 'Modification du document');
    }

    public function deleted(Facture $facture)
    {
        $this->log('deleted', $facture, 'Suppression du document');
    }

    private function log(string $action, Facture $facture, string $message)
    {
        ActivityLog::create([
            'user_id'    => auth()->id(),
            'action'     => $action,
            'model_type' => Facture::class,
            'model_id'   => $facture->id,
            'description'=> $message,
            'ip_address' => request()->ip(),
        ]);
    }
}
