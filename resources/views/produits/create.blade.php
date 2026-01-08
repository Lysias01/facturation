@extends('layouts.app')

@section('title', 'Ajouter un produit')

@section('content')
<h1 class="h4 mb-3">Ajouter un produit</h1>

<form action="{{ route('produits.store') }}" method="POST" class="card p-4 shadow-sm">
    @csrf

    <div class="mb-3">
        <label class="form-label">Nom du produit</label>
        <input type="text"
               name="nom"
               class="form-control @error('nom') is-invalid @enderror"
               value="{{ old('nom') }}"
               required>

        @error('nom')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label class="form-label">Prix d'achat (CFA)</label>
        <input type="number"
               name="prix_achat"
               class="form-control @error('prix_achat') is-invalid @enderror"
               step="0.01"
               min="0.01"
               value="{{ old('prix_achat') }}"
               required>

        @error('prix_achat')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label class="form-label">Prix de vente (CFA)</label>
        <input type="number"
               name="prix_vente"
               class="form-control @error('prix_vente') is-invalid @enderror"
               step="0.01"
               min="0.01"
               value="{{ old('prix_vente') }}"
               required>

        @error('prix_vente')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label class="form-label">Quantité en stock</label>
        <input type="number"
               name="stock"
               class="form-control @error('stock') is-invalid @enderror"
               min="1"
               value="{{ old('stock') }}"
               required>

        @error('stock')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label class="form-label">Seuil d'alerte</label>
        <input type="number"
               name="seuil_alerte"
               class="form-control @error('seuil_alerte') is-invalid @enderror"
               min="1"
               value="{{ old('seuil_alerte') }}"
               required>

        @error('seuil_alerte')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="d-flex justify-content-between">
        <a href="{{ route('produits.index') }}" class="btn btn-secondary">Annuler</a>
        <button class="btn btn-primary">Enregistrer</button>
    </div>
</form>


@endsection
