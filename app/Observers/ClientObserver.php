<?php

namespace App\Observers;

use App\Models\Client;
use App\Models\ActivityLog;

class ClientObserver
{
    public function created(Client $client)
    {
        $this->log('created', $client, "Création client: {$client->nomComplet}");
    }

    public function updated(Client $client)
    {
        $this->log('updated', $client, "Modification client: {$client->nomComplet}");
    }

    public function deleted(Client $client)
    {
        $this->log('deleted', $client, "Suppression client: {$client->nomComplet}");
    }

    private function log(string $action, Client $client, string $message)
    {
        ActivityLog::create([
            'user_id'    => auth()->id(),
            'action'     => $action,
            'model_type' => Client::class,
            'model_id'   => $client->id,
            'description'=> $message,
            'ip_address' => request()->ip(),
        ]);
    }
}
