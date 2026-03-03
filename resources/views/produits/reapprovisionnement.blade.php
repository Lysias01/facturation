@extends('layouts.app')

@section('title', 'Reapprovisionnement')

@section('content')
{{-- Page Header --}}
<div class="page-header">
    <div>
        <h1 class="page-title">Reapprovisionnement</h1>
        <p class="page-subtitle">{{ $produit->nom }}</p>
    </div>
    <a href="{{ route('produits.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Retour
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-12 col-md-6 col-lg-5">
        <div class="modern-card">
            <div class="card-body">
                <form action="{{ route('produits.reapprovisionnement', $produit->id) }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label for="quantite" class="form-label">Quantite a ajouter</label>
                        <input type="number"
                               name="quantite"
                               id="quantite"
                               class="form-control"
                               min="1"
                               required>
                    </div>

                    <div class="mb-4">
                        <label for="raison" class="form-label">Motif <span class="text-muted">(optionnel)</span></label>
                        <input type="text"
                               name="raison"
                               id="raison"
                               class="form-control"
                               placeholder="Achat fournisseur, correction stock, retour...">
                    </div>

                    <div class="d-flex gap-2 justify-content-end">
                        <a href="{{ route('produits.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-x-lg me-1"></i> Annuler
                        </a>
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-plus-circle me-1"></i> Ajouter au stock
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
