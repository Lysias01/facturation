<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Facture;

class ActivityLogController extends Controller
{
    public function index(Facture $facture)
    {
        // Pagination à 15 logs par page
        $logs = ActivityLog::where('model_type', Facture::class)
            ->where('model_id', $facture->id)
            ->latest()
            ->paginate(15);

        return view('historique.index', compact('facture', 'logs'));
    }
}

