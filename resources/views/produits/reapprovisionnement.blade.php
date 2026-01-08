@extends('layouts.app')

@section('title', 'Réapprovisionnement')

@section('content')
<div class="container">
    <h4 class="mb-4">
        Réapprovisionnement du produit :
        <strong>{{ $produit->nom }}</strong>
    </h4>

    <div class="card shadow-sm">
        <div class="card-body">

            <form action="{{ route('produits.reapprovisionnement', $produit->id) }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Quantité à ajouter</label>
                    <input
                        type="number"
                        name="quantite"
                        class="form-control"
                        min="1"
                        required
                    >
                </div>

                <div class="mb-3">
                    <label class="form-label">Motif (optionnel)</label>
                    <input
                        type="text"
                        name="raison"
                        class="form-control"
                        placeholder="Achat fournisseur, correction stock, retour…"
                    >
                </div>

                <div class="d-flex justify-content-between">
                    <a href="{{ route('produits.index') }}" class="btn btn-secondary">
                        ← Annuler
                    </a>

                    <button type="submit" class="btn btn-success">
                        Ajouter au stock
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>
@endsection
