<?php

namespace App\Http\Controllers;

use App\Models\Facture;
use App\Models\LigneFacture;
use App\Models\Client;
use App\Models\Produit;
use App\Models\MouvementStock;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FactureController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $factures = Facture::with('client', 'user')
            ->when($search, function ($query, $search) {
                $query->whereHas('client', fn($q) =>
                    $q->where('nom', 'like', "%$search%")
                )->orWhere('numero_facture', 'like', "%$search%");
            })
            ->orderByDesc('id')
            ->get();

        return view('factures.index', compact('factures'));
    }

    public function create(Request $request)
    {
        $type = $request->get('type', 'recu');
        
        return view('factures.create', [
            'clients' => Client::all(),
            'produits' => Produit::all(),
            'type_document' => $type,
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

        $client = Client::find($request->client_id);

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
        $facture = DB::transaction(function () use ($request, $client) {
            $facture = Facture::create([
                'client_id' => $request->client_id,
                'user_id' => auth()->id(),
                'type_document' => $request->type_document,
                'numero_facture' => Facture::generateNumeroFor($request->type_document),
                'total' => 0,
                'modifiable' => true,
            ]);

            $total = 0;

            foreach ($request->lignes as $ligne) {
                // Cast to integer for XOF currency (no decimals)
                $quantite = (int) $ligne['quantite'];
                // Remove any non-numeric characters except digits
                $prixUnitaire = (int) preg_replace('/[^0-9]/', '', $ligne['prix_unitaire']);
                $totalLigne = $quantite * $prixUnitaire;
                
                $total += $totalLigne;

                LigneFacture::create([
                    'facture_id' => $facture->id,
                    'produit_id' => $ligne['produit_id'],
                    'quantite' => $quantite,
                    'prix_unitaire' => $prixUnitaire,
                    'total_ligne' => (int) $totalLigne,
                ]);

                if ($request->type_document === 'recu') {
                    MouvementStock::create([
                        'produit_id' => $ligne['produit_id'],
                        'type' => 'sortie',
                        'quantite' => $quantite,
                        'raison' => "Vente facture {$facture->numero_facture}",
                    ]);
                }
            }

            $facture->update(['total' => $total]);

            return $facture;
        });

        // Enregistrer l'activité
        ActivityLogger::created(
            $facture,
            "Création {$facture->type_document} {$facture->numero_facture} pour {$client->nomComplet} - Total: {$facture->total}",
            $facture->type_document === 'recu' ? (float) $facture->total : null
        );

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

        $oldNumero = $facture->numero_facture;
        $client = $facture->client;
        $total = $facture->total;

        /* TRANSACTION */
        DB::transaction(function () use ($facture) {
            foreach ($facture->lignes as $ligne) {
                MouvementStock::create([
                    'produit_id' => $ligne->produit_id,
                    'type' => 'sortie',
                    'quantite' => $ligne['quantite'],
                    'raison' => "Validation facture {$facture->numero_facture}",
                ]);
            }

            $facture->update([
                'type_document' => 'recu',
                'numero_facture' => Facture::generateNumeroFor('recu'),
                'modifiable' => false,
            ]);
        });

        // Enregistrer l'activité
        ActivityLogger::validated(
            $facture,
            "Validation pro-forma {$oldNumero} → reçu {$facture->numero_facture} pour {$client->nomComplet}",
            (float) $total
        );

        return redirect()
            ->route('factures.index')
            ->with('success', 'Pro-forma validé en reçu.');
    }


    public function show(Facture $facture)
    {
        $facture->load('client', 'lignes.produit', 'user');
        return view('factures.show', compact('facture'));
    }

    public function edit(Facture $facture)
    {
        // Only admin can edit any facture
        if (auth()->user()->isAdmin()) {
            if ($facture->isDefinitive()) {
                return back()->with('error', 'Un reçu ne peut pas être modifié.');
            }
            return view('factures.edit', [
                'facture' => $facture->load('lignes.produit'),
                'clients' => Client::all(),
                'produits' => Produit::all(),
            ]);
        }

        // Employe can only edit their own factures
        if ($facture->user_id !== auth()->id()) {
            return back()->with('error', 'Vous ne pouvez pas modifier une facture créée par un autre utilisateur.');
        }

        // Employe can only edit pro-forma (not validated)
        if ($facture->isDefinitive()) {
            return back()->with('error', 'Un reçu ne peut pas être modifié.');
        }

        // Employe can only edit factures created today
        $today = Carbon::today();
        $factureDate = Carbon::parse($facture->created_at)->startOfDay();
        
        if ($factureDate->lt($today)) {
            return back()->with('error', 'Vous ne pouvez pas modifier une facture après le jour de création.');
        }

        return view('factures.edit', [
            'facture' => $facture->load('lignes.produit'),
            'clients' => Client::all(),
            'produits' => Produit::all(),
        ]);
    }

    public function update(Request $request, Facture $facture)
    {
        // Only admin can update any facture
        if (!auth()->user()->isAdmin()) {
            // Employe can only update their own factures
            if ($facture->user_id !== auth()->id()) {
                return back()->with('error', 'Vous ne pouvez pas modifier une facture créée par un autre utilisateur.');
            }

            // Employe can only update pro-forma (not validated)
            if ($facture->isDefinitive()) {
                return back()->with('error', 'Un reçu ne peut pas être modifié.');
            }

            // Employe can only update factures created today
            $today = Carbon::today();
            $factureDate = Carbon::parse($facture->created_at)->startOfDay();
            
            if ($factureDate->lt($today)) {
                return back()->with('error', 'Vous ne pouvez pas modifier une facture après le jour de création.');
            }
        } else {
            if ($facture->isDefinitive()) {
                return back()->with('error', 'Un reçu ne peut pas être modifié.');
            }
        }

        $request->validate([
            'client_id' => 'required|exists:clients,id',
            'lignes' => 'required|array|min:1',
            'lignes.*.produit_id' => 'required|exists:produits,id',
            'lignes.*.quantite' => 'required|integer|min:1',
            'lignes.*.prix_unitaire' => 'required|numeric|min:0',
        ]);

        $oldData = [
            'client_id' => $facture->client_id,
            'total' => $facture->total,
            'lignes_count' => $facture->lignes->count(),
        ];

        $client = Client::find($request->client_id);

        DB::transaction(function () use ($request, $facture) {
            $facture->lignes()->delete();

            $total = 0;

            foreach ($request->lignes as $ligne) {
                // Cast to integer for XOF currency (no decimals)
                $quantite = (int) $ligne['quantite'];
                // Remove any non-numeric characters except digits
                $prixUnitaire = (int) preg_replace('/[^0-9]/', '', $ligne['prix_unitaire']);
                $totalLigne = $quantite * $prixUnitaire;
                
                $total += $totalLigne;

                LigneFacture::create([
                    'facture_id' => $facture->id,
                    'produit_id' => $ligne['produit_id'],
                    'quantite' => $quantite,
                    'prix_unitaire' => $prixUnitaire,
                    'total_ligne' => (int) $totalLigne,
                ]);
            }

            $facture->update([
                'client_id' => $request->client_id,
                'total' => $total,
            ]);
        });

        // Enregistrer l'activité
        ActivityLogger::updated(
            $facture,
            "Mise à jour facture {$facture->numero_facture} pour {$client->nomComplet}",
            (float) ($total - $oldData['total']),
            $oldData
        );

        return redirect()->route('factures.index')->with('success', 'Facture mise à jour.');
    }

    public function destroy(Facture $facture)
    {
        // Only admin can delete any facture
        if (!auth()->user()->isAdmin()) {
            // Employe can only delete their own factures
            if ($facture->user_id !== auth()->id()) {
                return back()->with('error', 'Vous ne pouvez pas supprimer une facture créée par un autre utilisateur.');
            }

            // Employe can only delete pro-forma (not validated)
            if ($facture->isDefinitive()) {
                return back()->with('error', 'Vous ne pouvez pas supprimer un reçu.');
            }
        } else {
            if ($facture->isDefinitive()) {
                return back()->with('error', 'Un reçu ne peut pas être supprimé.');
            }
        }

        $factureData = [
            'numero' => $facture->numero_facture,
            'client' => $facture->client->nomComplet ?? 'Inconnu',
            'total' => $facture->total,
        ];

        $facture->delete();

        // Enregistrer l'activité
        ActivityLogger::deleted(
            $facture,
            "Suppression {$factureData['numero']} pour {$factureData['client']} - Total: {$factureData['total']}"
        );

        return redirect()->route('factures.index')->with('success', 'Facture supprimée.');
    }
}
