@extends('layouts.app')

@section('title', 'Liste des produits')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4">Produits</h1>
    <a href="{{ route('produits.create') }}" class="btn btn-primary">Ajouter un produit</a>
</div>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <table class="table table-striped table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>Nom</th>
                    <th>Prix vente</th>
                    <th>Prix achat</th>
                    <th>Stock actuel</th>
                    <th>Seuil</th>
                    <th width="180">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($produits as $produit)
                    <tr>
                        <td>{{ $produit->nom }}</td>
                        <td>{{ number_format($produit->prix_vente,2,',',' ') }} CFA</td>
                        <td>{{ number_format($produit->prix_achat,2,',',' ') }} CFA</td>
                        <td>
                            {{ $produit->stock_actuel }}
                            @if($produit->stock_critique)
                                <span class="badge bg-danger ms-1">⚠</span>
                            @endif
                        </td>
                        <td>{{ $produit->seuil_alerte }}</td>
                        <td>
                            <a href="{{ route('produits.reapprovisionnement', $produit) }}" class="btn btn-sm btn-success">Réappro</a>
                            <a href="{{ route('produits.mouvements', $produit->id) }}" class="btn btn-sm btn-info">Historique</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted">Aucun produit trouvé.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
