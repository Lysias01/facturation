<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Facture;

class ActivityLogController extends Controller
{
    public function index(Facture $facture)
    {
        $logs = ActivityLog::where('model_type', Facture::class)
            ->where('model_id', $facture->id)
            ->latest()
            ->get();

        return view('historique.index', compact('facture', 'logs'));
    }
}
