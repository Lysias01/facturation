<?php

namespace App\Http\Controllers;

use App\Models\Facture;
use App\Models\LigneFacture;
use App\Models\Client;
use App\Models\Produit;
use App\Models\MouvementStock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FactureController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $factures = Facture::with('client')
            ->when($search, function ($query, $search) {
                $query->whereHas('client', fn($q) =>
                    $q->where('nom', 'like', "%$search%")
                )->orWhere('numero_facture', 'like', "%$search%");
            })
            ->orderByDesc('id')
            ->get();

        return view('factures.index', compact('factures'));
    }

    public function create()
    {
        return view('factures.create', [
            'clients' => Client::all(),
            'produits' => Produit::all(),
        ]);
    }

    public function store(Request $request)
{
    $request->validate([
        'client_id' => 'required|exists:clients,id',
        'type_document' => 'required|in:pro-forma,recu',
        'lignes' => 'required|array|min:1',
        'lignes.*.produit_id' => 'required|exists:produits,id',
        'lignes.*.quantite' => 'required|integer|min:1',
        'lignes.*.prix_unitaire' => 'required|numeric|min:0',
    ]);

        $produitIds = collect($request->lignes)->pluck('produit_id');

    if ($produitIds->count() !== $produitIds->unique()->count()) {
        return redirect()->back()
            ->withErrors([
                'lignes' => 'Un même produit ne peut pas apparaître sur plusieurs lignes.'
            ])
            ->withInput();
    }


    /* VÉRIFICATION STOCK AVANT TRANSACTION */
    if ($request->type_document === 'recu') {
        foreach ($request->lignes as $index => $ligne) {
            $produit = Produit::find($ligne['produit_id']);

            if ($ligne['quantite'] > $produit->stock_actuel) {
                return redirect()
                    ->route('factures.create')
                    ->withErrors([
                        "lignes.$index.quantite" =>
                            "(reste: {$produit->stock_actuel})"
                    ])
                    ->withInput();
            }
        }
    }

    /* TRANSACTION PROPRE */
    DB::transaction(function () use ($request) {

        $facture = Facture::create([
            'client_id' => $request->client_id,
            'type_document' => $request->type_document,
            'numero_facture' => Facture::generateNumeroFor($request->type_document),
            'total' => 0,
            'modifiable' => true,
        ]);

        $total = 0;

        foreach ($request->lignes as $ligne) {
            $totalLigne = $ligne['quantite'] * $ligne['prix_unitaire'];
            $total += $totalLigne;

            LigneFacture::create([
                'facture_id' => $facture->id,
                'produit_id' => $ligne['produit_id'],
                'quantite' => $ligne['quantite'],
                'prix_unitaire' => $ligne['prix_unitaire'],
                'total_ligne' => $totalLigne,
            ]);

            if ($request->type_document === 'recu') {
                MouvementStock::create([
                    'produit_id' => $ligne['produit_id'],
                    'type' => 'sortie',
                    'quantite' => $ligne['quantite'],
                    'raison' => "Vente facture {$facture->numero_facture}",
                ]);
            }
        }

        $facture->update(['total' => $total]);
    });

    return redirect()
        ->route('factures.index')
        ->with('success', 'Facture créée avec succès.');
}



public function valider(Facture $facture)
{
    if ($facture->type_document !== 'pro-forma') {
        return redirect()
            ->route('factures.index')
            ->with('error', 'Seul un pro-forma peut être validé.');
    }

    /* VÉRIFICATION STOCK */
    foreach ($facture->lignes as $ligne) {
        $produit = $ligne->produit;

        if ($ligne->quantite > $produit->stock_actuel) {
            return redirect()
                ->route('factures.edit', $facture->id)
                ->withErrors([
                    "lignes.{$ligne->id}.quantite" =>
                        "Stock insuffisant pour {$produit->nom} (reste: {$produit->stock_actuel})"
                ]);
        }
    }

    /* TRANSACTION */
    DB::transaction(function () use ($facture) {
        foreach ($facture->lignes as $ligne) {
            MouvementStock::create([
                'produit_id' => $ligne->produit_id,
                'type' => 'sortie',
                'quantite' => $ligne->quantite,
                'raison' => "Validation facture {$facture->numero_facture}",
            ]);
        }

        $facture->update([
            'type_document' => 'recu',
            'numero_facture' => Facture::generateNumeroFor('recu'),
            'modifiable' => false,
        ]);
    });

    return redirect()
        ->route('factures.index')
        ->with('success', 'Pro-forma validé en reçu.');
}


    public function show(Facture $facture)
    {
        $facture->load('client', 'lignes.produit');
        return view('factures.show', compact('facture'));
    }

    public function edit(Facture $facture)
    {
        if ($facture->isDefinitive()) {
            return back()->with('error', 'Un reçu ne peut pas être modifié.');
        }

        return view('factures.edit', [
            'facture' => $facture->load('lignes.produit'),
            'clients' => Client::all(),
            'produits' => Produit::all(),
        ]);
    }

    public function update(Request $request, Facture $facture)
    {
        if ($facture->isDefinitive()) {
            return back()->with('error', 'Un reçu ne peut pas être modifié.');
        }

        $request->validate([
            'client_id' => 'required|exists:clients,id',
            'lignes' => 'required|array|min:1',
            'lignes.*.produit_id' => 'required|exists:produits,id',
            'lignes.*.quantite' => 'required|integer|min:1',
            'lignes.*.prix_unitaire' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($request, $facture) {
            $facture->lignes()->delete();

            $total = 0;

            foreach ($request->lignes as $ligne) {
                $totalLigne = $ligne['quantite'] * $ligne['prix_unitaire'];
                $total += $totalLigne;

                LigneFacture::create([
                    'facture_id' => $facture->id,
                    'produit_id' => $ligne['produit_id'],
                    'quantite' => $ligne['quantite'],
                    'prix_unitaire' => $ligne['prix_unitaire'],
                    'total_ligne' => $totalLigne,
                ]);
            }

            $facture->update([
                'client_id' => $request->client_id,
                'total' => $total,
            ]);
        });

        return redirect()->route('factures.index')->with('success', 'Facture mise à jour.');
    }

    public function destroy(Facture $facture)
    {
        if ($facture->isDefinitive()) {
            return back()->with('error', 'Un reçu ne peut pas être supprimé.');
        }

        $facture->delete();
        return redirect()->route('factures.index')->with('success', 'Facture supprimée.');
    }
}
