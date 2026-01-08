@extends('layouts.app')

@section('title', 'Ajouter un client')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <h1 class="h4 mb-3">Ajouter un client</h1>

        <form action="{{ route('clients.store') }}" method="POST" class="card p-3 shadow-sm">
            @csrf

            <div class="mb-3">
                <label class="form-label">Nom</label>
                <input type="text" name="nom" value="{{ old('nom') }}" class="form-control" required
                    pattern="[A-Za-zÀ-ÿ' \-]+" title="Seules les lettres, espaces, apostrophes et traits d'union sont autorisés">
                @error('nom')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Prénom(s)</label>
                <input type="text" name="prenom" value="{{ old('prenom') }}" class="form-control" required
                    pattern="[A-Za-zÀ-ÿ' \-]+" title="Seules les lettres, espaces, apostrophes et traits d'union sont autorisés">
                @error('prenom')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Téléphone</label>
                <input type="text" name="telephone" value="{{ old('telephone') }}" class="form-control" required
                    pattern="^\+?\d+$" title="Seuls les chiffres et le signe + sont autorisés">
                @error('telephone')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Adresse (optionnelle)</label>
                <input type="text" name="adresse" value="{{ old('adresse') }}" class="form-control">
                @error('adresse')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <div class="d-flex justify-content-between">
                <a href="{{ route('clients.index') }}" class="btn btn-link">Annuler</a>
                <button class="btn btn-primary">Enregistrer</button>
            </div>
        </form>
    </div>
</div>
@endsection
