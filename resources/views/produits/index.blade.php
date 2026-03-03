@extends('layouts.app')

@section('title', 'Produits')

@section('content')
{{-- Page Header --}}
<div class="page-header">
    <div>
        <h1 class="page-title">Produits</h1>
        <p class="page-subtitle">Gestion des produits et du stock</p>
    </div>
    @auth
        @if(auth()->user()->isAdmin())
            <a href="{{ route('produits.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i> Ajouter un produit
            </a>
        @endif
    @endauth
</div>

{{-- Products Table --}}
<div class="modern-card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th class="text-end">Prix de vente</th>
                        <th class="text-end">Prix d'achat</th>
                        <th class="text-center">Stock</th>
                        <th class="text-center">Seuil</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($produits as $produit)
                        <tr class="{{ $produit->enAlerte() ? 'table-warning' : '' }}">
                            <td class="fw-semibold">{{ $produit->nom }}</td>
                            <td class="text-end">{{ number_format($produit->prix_vente, 0, ',', ' ') }} CFA</td>
                            <td class="text-end text-muted">{{ number_format($produit->prix_achat, 0, ',', ' ') }} CFA</td>
                            <td class="text-center">
                                <span class="fw-semibold {{ $produit->enAlerte() ? 'text-danger' : '' }}">
                                    {{ $produit->stock_actuel }}
                                </span>
                                @if($produit->enAlerte())
                                    <i class="bi bi-exclamation-triangle text-danger ms-1" title="Stock en alerte"></i>
                                @endif
                            </td>
                            <td class="text-center text-muted">{{ $produit->seuil_alerte }}</td>
                            <td class="text-end">
                                <div class="action-buttons justify-content-end">
                                    <a href="{{ route('produits.show', $produit->id) }}" 
                                       class="btn btn-sm btn-outline-info"
                                       title="Voir">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('produits.mouvements', $produit->id) }}" 
                                       class="btn btn-sm btn-outline-secondary"
                                       title="Mouvements">
                                        <i class="bi bi-clock-history"></i>
                                    </a>
                                    @auth
                                        @if(auth()->user()->isAdmin())
                                            <a href="{{ route('produits.reapprovisionnement', $produit->id) }}" 
                                               class="btn btn-sm btn-outline-success"
                                               title="Reapprovisionner">
                                                <i class="bi bi-plus-circle"></i>
                                            </a>
                                        @endif
                                    @endauth
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <div class="empty-state">
                                    <i class="bi bi-box-seam"></i>
                                    <h5>Aucun produit</h5>
                                    <p class="text-muted">Aucun produit trouve.</p>
                                    @auth
                                        @if(auth()->user()->isAdmin())
                                            <a href="{{ route('produits.create') }}" class="btn btn-primary btn-sm">
                                                <i class="bi bi-plus-lg me-1"></i>Ajouter un produit
                                            </a>
                                        @endif
                                    @endauth
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    @if($produits->hasPages())
    <div class="card-footer bg-white">
        {{ $produits->links() }}
    </div>
    @endif
</div>
@endsection
