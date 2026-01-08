<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Facture;
use App\Models\Client;
use App\Models\Setting;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\DashboardStatsExport;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $month = $request->month
            ? Carbon::createFromFormat('Y-m', $request->month)
            : now();

        $year = $request->year ?? now()->year;

        $startOfMonth = $month->copy()->startOfMonth();
        $endOfMonth   = $month->copy()->endOfMonth();

        /** =========================
         *  STATS DU MOIS (ANNÉE FIXÉE)
         *  ========================= */
        $proformasMonthCount = Facture::where('type_document', 'pro-forma')
            ->whereYear('created_at', $year)
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->count();

        $recuMonthCount = Facture::where('type_document', 'recu')
            ->whereYear('created_at', $year)
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->count();

        $recuMonthSum = Facture::where('type_document', 'recu')
            ->whereYear('created_at', $year)
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->sum('total');

        $proformasMonthSum = Facture::where('type_document', 'pro-forma')
            ->whereYear('created_at', $year)
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->sum('total');

        /** =========================
         *  TOTAL ANNUEL (REÇUS)
         *  ========================= */
        $recuYearSum = Facture::where('type_document', 'recu')
            ->whereYear('created_at', $year)
            ->sum('total');

        /** =========================
         *  MOIS MAX / MIN (LOGIQUE CORRIGÉE)
         *  ========================= */
        $facturesByMonth = Facture::where('type_document', 'recu')
            ->whereYear('created_at', $year)
            ->selectRaw('MONTH(created_at) as month, COUNT(*) as total')
            ->groupBy('month')
            ->pluck('total', 'month');

        $maxMonth = null;
        $minMonth = null;

        if ($facturesByMonth->isNotEmpty()) {
            $maxMonthNumber = $facturesByMonth->sortDesc()->keys()->first();
            $minMonthNumber = $facturesByMonth->sort()->keys()->first();

            $maxMonth = Carbon::create()->month($maxMonthNumber)->translatedFormat('F');
            $minMonth = Carbon::create()->month($minMonthNumber)->translatedFormat('F');
        }

        /** =========================
         *  DONNÉES ANNEXES
         *  ========================= */
        $recentDocuments = Facture::with('client')
            ->orderBy('created_at', 'desc')
            ->limit(8)
            ->get();

        $clientsCount = Client::count();
        $settings = Setting::first();

        return view('dashboard.index', compact(
            'proformasMonthCount',
            'recuMonthCount',
            'recuMonthSum',
            'proformasMonthSum',
            'recuYearSum',
            'recentDocuments',
            'clientsCount',
            'settings',
            'maxMonth',
            'minMonth',
            'month',
            'year'
        ));
    }

    public function exportExcel(Request $request)
    {
        $month = $request->month ?? now()->format('Y-m');
        $year  = $request->year ?? now()->year;

        return Excel::download(
            new DashboardStatsExport($month, $year),
            "dashboard_stats_{$month}.xlsx"
        );
    }
}
