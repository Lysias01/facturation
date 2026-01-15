<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Facture;
use App\Models\Client;
use App\Models\Produit;
use App\Models\MouvementStock;
use Carbon\Carbon;
use PDF;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        /** =========================
         *  FILTRES
         *  ========================= */
        $month = $request->month
            ? Carbon::createFromFormat('Y-m', $request->month)
            : now();

        $year = $request->year ?? now()->year;
        $day  = $request->day ? Carbon::parse($request->day) : now();

        $startOfMonth = $month->copy()->startOfMonth();
        $endOfMonth   = $month->copy()->endOfMonth();

        $startOfDay = $day->copy()->startOfDay();
        $endOfDay   = $day->copy()->endOfDay();

        /** =========================
         *  FACTURATION
         *  ========================= */

        // Comptages
        $proformasMonthCount = Facture::where('type_document', 'pro-forma')
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->count();

        $recuMonthCount = Facture::where('type_document', 'recu')
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->count();

        $recuMonthSum = Facture::where('type_document', 'recu')
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->sum('total');

        $recuDaySum = Facture::where('type_document', 'recu')
            ->whereBetween('created_at', [$startOfDay, $endOfDay])
            ->sum('total');

        $proformasMonthSum = Facture::where('type_document', 'pro-forma')
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->sum('total');

        $recuYearSum = Facture::where('type_document', 'recu')
            ->whereYear('created_at', $year)
            ->sum('total');

        // Clients
        $clientsCount = Client::count();

        /** =========================
         *  COURBE DU CHIFFRE D’AFFAIRES
         *  ========================= */
        $chartLabels = [];
        $chartRecu = [];
        $chartProforma = [];

        for ($date = $startOfMonth->copy(); $date->lte($endOfMonth); $date->addDay()) {
            $chartLabels[] = $date->format('d M');

            $chartRecu[] = Facture::where('type_document', 'recu')
                ->whereDate('created_at', $date)
                ->sum('total');

            $chartProforma[] = Facture::where('type_document', 'pro-forma')
                ->whereDate('created_at', $date)
                ->sum('total');
        }

        /** =========================
         *  STOCK
         *  ========================= */
        $produitsCount = Produit::count();
        $stockTotal = Produit::sum('stock');
        $produitsCritiquesCount = Produit::whereColumn('stock', '<=', 'seuil_alerte')->count();

        $entreesStockMonth = MouvementStock::where('type', 'entree')
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->sum('quantite');

        $sortiesStockMonth = MouvementStock::where('type', 'sortie')
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->sum('quantite');

        $produitsCritiques = Produit::whereColumn('stock', '<=', 'seuil_alerte')
            ->orderBy('stock', 'asc')
            ->limit(5)
            ->get();

        $topProduitsSortie = MouvementStock::selectRaw('produit_id, SUM(quantite) as total')
            ->where('type', 'sortie')
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->groupBy('produit_id')
            ->orderByDesc('total')
            ->with('produit')
            ->limit(5)
            ->get();

        /** =========================
         *  DERNIERS ÉLÉMENTS
         *  ========================= */
        $recentFactures = Facture::with('client')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        $recentMouvements = MouvementStock::with('produit')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        /** =========================
         *  ENVOI À LA VUE
         *  ========================= */
        return view('dashboard.index', compact(
            'month',
            'year',
            'day',

            // Facturation
            'proformasMonthCount',
            'recuMonthCount',
            'recuMonthSum',
            'recuDaySum',
            'proformasMonthSum',
            'recuYearSum',

            // Courbe
            'chartLabels',
            'chartRecu',
            'chartProforma',

            // Clients
            'clientsCount',

            // Stock
            'produitsCount',
            'stockTotal',
            'produitsCritiquesCount',
            'entreesStockMonth',
            'sortiesStockMonth',

            // Listes
            'produitsCritiques',
            'topProduitsSortie',
            'recentFactures',
            'recentMouvements'
        ));
    }

    /** =========================
     *  RAPPORT PDF
     *  ========================= */
    public function exportPdf(Request $request, $period = 'daily')
    {
        $date = $request->date ? Carbon::parse($request->date) : now();
        $start = $end = $date;

        if ($period === 'daily') {
            $start = $date->copy()->startOfDay();
            $end   = $date->copy()->endOfDay();
        } elseif ($period === 'monthly') {
            $start = $date->copy()->startOfMonth();
            $end   = $date->copy()->endOfMonth();
        } elseif ($period === 'yearly') {
            $start = $date->copy()->startOfYear();
            $end   = $date->copy()->endOfYear();
        }

        $factures = Facture::with('client')
            ->where('type_document', 'recu')
            ->whereBetween('created_at', [$start, $end])
            ->get();

        $total = $factures->sum('total');

        $pdf = PDF::loadView('reports.sales', compact('factures', 'total', 'period', 'start', 'end'));

        return $pdf->stream("rapport_{$period}.pdf");
    }
}
