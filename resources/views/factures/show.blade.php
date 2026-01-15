@extends('layouts.app')

@section('title', 'Détails document')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow-sm p-3">

            <!-- En-tête -->
            <div class="d-flex justify-content-between mb-3">
                <h3 class="h5">Facture #{{ $facture->numero_facture }}</h3>
                <span class="badge {{ $facture->type_document=='pro-forma' ? 'bg-warning text-dark' : 'bg-success' }}">
                    {{ ucfirst($facture->type_document) }}
                </span>
            </div>

            <!-- Infos générales -->
            <div class="mb-3">
                <strong>Client :</strong>
                {{ $facture->client->nom ?? '—' }} {{ $facture->client->prenom ?? '—' }}<br>
                <strong>Date :</strong> {{ $facture->created_at->format('d/m/Y') }}
            </div>

            <!-- Lignes -->
            <h5>Lignes</h5>
            <div class="table-responsive mb-3">
                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>Produit</th>
                            <th>Quantité</th>
                            <th>Prix unitaire</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($facture->lignes as $ligne)
                            <tr>
                                <td>{{ $ligne->produit->nom ?? '—' }}</td>
                                <td>{{ $ligne->quantite }}</td>
                                <td>{{ number_format($ligne->prix_unitaire, 2, ',', ' ') }} CFA</td>
                                <td>{{ number_format($ligne->total_ligne, 2, ',', ' ') }} CFA</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Total -->
            <div class="text-end fw-bold fs-5 mb-4">
                Total : {{ number_format($facture->total, 2, ',', ' ') }} CFA
            </div>

            <!-- Actions -->
            <div class="d-flex justify-content-between mb-4">
                <a href="{{ url()->previous() }}" class="btn btn-link">Retour</a>

                <div class="d-flex gap-2">
                    @if($facture->type_document === 'pro-forma' && $facture->modifiable)
                        <a href="{{ route('factures.edit', $facture->id) }}" class="btn btn-secondary">
                            Modifier
                        </a>
                    @endif

                    <a href="{{ route('pdf.generate', $facture->id) }}" class="btn btn-primary" target="_blank">
                        Imprimer
                    </a>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
