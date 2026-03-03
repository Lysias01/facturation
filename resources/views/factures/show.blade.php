@extends('layouts.app')

@section('title', 'Details du document')

@section('content')
{{-- Page Header --}}
<div class="page-header">
    <div>
        <h1 class="page-title">Document</h1>
        <p class="page-subtitle">{{ $facture->numero_facture }}</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('factures.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Retour
        </a>
        <a href="{{ route('pdf.generate', $facture->id) }}" class="btn btn-primary" target="_blank">
            <i class="bi bi-printer me-1"></i> Imprimer
        </a>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-12 col-lg-8">
        <div class="modern-card">
            {{-- Header --}}
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted">Document</span>
                    <h4 class="mb-0 fw-bold">{{ $facture->numero_facture }}</h4>
                </div>
                <span class="badge {{ $facture->type_document=='pro-forma' ? 'bg-warning-subtle text-warning' : 'bg-success-subtle text-success' }} fs-6">
                    {{ ucfirst($facture->type_document) }}
                </span>
            </div>

            {{-- Body --}}
            <div class="card-body">
                {{-- General Info --}}
                <div class="row mb-4">
                    <div class="col-12 col-md-6">
                        <h6 class="text-muted mb-2"><i class="bi bi-person me-1"></i>Client</h6>
                        <p class="fw-semibold mb-0">{{ $facture->client->nom ?? '—' }} {{ $facture->client->prenom ?? '—' }}</p>
                        @if($facture->client?->telephone)
                            <p class="text-muted small mb-0">
                                <i class="bi bi-telephone me-1"></i>{{ $facture->client->telephone }}
                            </p>
                        @endif
                        @if($facture->client?->adresse)
                            <p class="text-muted small mb-0">
                                <i class="bi bi-geo-alt me-1"></i>{{ $facture->client->adresse }}
                            </p>
                        @endif
                    </div>
                    <div class="col-12 col-md-6 text-md-end">
                        <h6 class="text-muted mb-2"><i class="bi bi-calendar me-1"></i>Date</h6>
                        <p class="fw-semibold mb-0">{{ $facture->created_at->format('d/m/Y') }}</p>
                        <p class="text-muted small mb-0">{{ $facture->created_at->format('H:i') }}</p>
                    </div>
                </div>

                <hr class="my-4">

                {{-- Lines Table --}}
                <h5 class="mb-3"><i class="bi bi-list-ul me-2"></i>Details</h5>
                <div class="table-responsive mb-4">
                    <table class="table table-bordered mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Produit</th>
                                <th class="text-center" style="width:100px;">Quantite</th>
                                <th class="text-end" style="width:150px;">Prix unitaire</th>
                                <th class="text-end" style="width:150px;">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($facture->lignes as $ligne)
                                <tr>
                                    <td>{{ $ligne->produit->nom ?? '—' }}</td>
                                    <td class="text-center">{{ $ligne->quantite }}</td>
                                    <td class="text-end">{{ number_format($ligne->prix_unitaire, 0, ',', ' ') }} CFA</td>
                                    <td class="text-end fw-semibold">{{ number_format($ligne->total_ligne, 0, ',', ' ') }} CFA</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Total --}}
                <div class="d-flex justify-content-end">
                    <div class="text-end" style="min-width: 200px;">
                        <div class="text-muted mb-1">Total</div>
                        <div class="h3 mb-0 fw-bold text-primary">{{ number_format($facture->total, 0, ',', ' ') }} CFA</div>
                    </div>
                </div>

                {{-- Actions --}}
                @if($facture->type_document === 'pro-forma' && $facture->modifiable)
                    @if(auth()->user()->isAdmin() || $facture->user_id === auth()->id())
                    <div class="d-flex gap-2 justify-content-end mt-4 pt-3 border-top">
                        <a href="{{ route('factures.edit', $facture->id) }}" class="btn btn-outline-primary">
                            <i class="bi bi-pencil me-1"></i> Modifier
                        </a>
                        
                        <form action="{{ route('factures.valider', $facture->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('PUT')
                            <button type="submit" class="btn btn-success" onclick="return confirm('Voulez-vous valider ce devis en recu ?');">
                                <i class="bi bi-check2-circle me-1"></i> Valider
                            </button>
                        </form>
                    </div>
                    @endif
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
