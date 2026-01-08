<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produit;
use App\Models\MouvementStock;

class ProduitController extends Controller
{
    public function index()
    {
        $produits = Produit::all(); 
        return view('produits.index', compact('produits'));
    }

    public function create()
    {
        return view('produits.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:255|unique:produits,nom',
            'prix_achat' => 'required|numeric|min:0.01',
            'prix_vente' => 'required|numeric|min:0.01',
            'stock' => 'required|integer|min:1',
            'seuil_alerte' => 'required|integer|min:1',
        ], [
            'nom.unique' => 'Ce produit existe déjà.'
        ]);

        $produit = Produit::create($request->only([
            'nom', 'prix_achat', 'prix_vente', 'seuil_alerte'
        ]));

        if ($request->stock > 0) {
            MouvementStock::create([
                'produit_id' => $produit->id,
                'type' => 'entree',
                'quantite' => $request->stock,
                'raison' => 'Stock initial',
            ]);
        }

        return redirect()->route('produits.index')
            ->with('success', 'Produit créé avec succès.');
    }


    public function edit(Produit $produit)
    {
        return view('produits.edit', compact('produit'));
    }

    public function update(Request $request, Produit $produit)
    {
        $request->validate([
            'nom' => [
                'required',
                'string',
                'max:255',
                Rule::unique('produits', 'nom')->ignore($produit->id),
            ],
            'prix_achat' => 'required|numeric|min:0',
            'prix_vente' => 'required|numeric|min:0',
            'seuil_alerte' => 'required|integer|min:1',
            'stock' => 'nullable|integer|min:0',
        ]);

        $produit->update($request->only([
            'nom','prix_achat','prix_vente','seuil_alerte'
        ]));

        if ($request->filled('stock')) {
            $diff = $request->stock - $produit->stock_actuel;

            if ($diff !== 0) {
                MouvementStock::create([
                    'produit_id' => $produit->id,
                    'type' => $diff > 0 ? 'entree' : 'sortie',
                    'quantite' => abs($diff),
                    'raison' => 'Ajustement stock',
                ]);
            }
        }

        return redirect()->route('produits.index')
            ->with('success', 'Produit mis à jour.');
    }

    public function destroy(Produit $produit)
    {
        $produit->delete();
        return redirect()->route('produits.index')->with('success','Produit supprimé avec succès.');
    }

    public function reapprovisionnement(Request $request, Produit $produit)
    {
        if ($request->isMethod('post')) {

            $request->validate([
                'quantite' => 'required|integer|min:1',
                'raison' => 'nullable|string|max:255',
            ]);

            // Création du mouvement d'entrée
            MouvementStock::create([
                'produit_id' => $produit->id,
                'type' => 'entree',
                'quantite' => $request->quantite,
                'raison' => $request->raison ?? 'Réapprovisionnement',
            ]);

            return redirect()->route('produits.index')
                             ->with('success', 'Stock réapprovisionné avec succès.');
        }

        // GET → afficher le formulaire
        return view('produits.reapprovisionnement', compact('produit'));
    }

    public function mouvements(Produit $produit)
    {
        $mouvements = $produit->mouvementsStock()->orderBy('created_at','desc')->get();
        return view('produits.mouvements', compact('produit','mouvements'));
    }
}
