@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="container-fluid">

    <!-- HEADER -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h4 mb-0">Dashboard</h1>
            <small class="text-muted">
                Vue globale – {{ $month->translatedFormat('F Y') }}
            </small>
        </div>

        <form method="GET" class="d-flex gap-2">
            <input type="month" name="month" class="form-control" value="{{ $month->format('Y-m') }}">
            <button class="btn btn-primary">Filtrer</button>
        </form>
    </div>

    <!-- KPI FACTURATION -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card shadow-sm p-3">
                <div class="text-muted small">Pro-forma ce mois</div>
                <div class="h5 fw-bold">{{ $proformasMonthCount }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm p-3">
                <div class="text-muted small">Reçus ce mois</div>
                <div class="h5 fw-bold">{{ $recuMonthCount }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm p-3">
                <div class="text-muted small">Montant encaissé ce mois</div>
                <div class="h5 fw-bold">{{ number_format($recuMonthSum, 0, ',', ' ') }} FCFA</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm p-3">
                <div class="text-muted small">Total annuel encaissé</div>
                <div class="h5 fw-bold">{{ number_format($recuYearSum, 0, ',', ' ') }} FCFA</div>
            </div>
        </div>
    </div>

    <!-- KPI CLIENTS & STOCK -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card shadow-sm p-3">
                <div class="text-muted small">Clients</div>
                <div class="h5 fw-bold">{{ $clientsCount }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm p-3">
                <div class="text-muted small">Produits</div>
                <div class="h5 fw-bold">{{ $produitsCount }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm p-3">
                <div class="text-muted small">Stock total</div>
                <div class="h5 fw-bold">{{ $stockTotal }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm p-3 border-danger">
                <div class="text-muted small">Produits en alerte</div>
                <div class="h5 fw-bold text-danger">{{ $produitsCritiquesCount }}</div>
            </div>
        </div>
    </div>

    <!-- COURBE CHIFFRE D'AFFAIRES -->
    <div class="card shadow-sm p-3 mb-4">
        <h5 class="mb-3">📈 Évolution du chiffre d'affaires</h5>
        <canvas id="caChart" height="120"></canvas>
    </div>

    <!-- BOUTONS EXPORT PDF -->
    <div class="mb-4">
        <a href="{{ route('dashboard.exportPdf', ['period' => 'daily', 'date' => now()->format('Y-m-d')]) }}" class="btn btn-primary">Rapport Journalier</a>
        <a href="{{ route('dashboard.exportPdf', ['period' => 'monthly', 'date' => now()->format('Y-m-d')]) }}" class="btn btn-success">Rapport Mensuel</a>
        <a href="{{ route('dashboard.exportPdf', ['period' => 'yearly', 'date' => now()->format('Y-m-d')]) }}" class="btn btn-warning">Rapport Annuel</a>
    </div>

    <!-- PRODUITS EN ALERTE & TOP SORTIES -->
    <div class="row g-4">
        <div class="col-md-6">
            <div class="card shadow-sm p-3">
                <h5>⚠️ Produits en alerte</h5>
                <table class="table table-sm">
                    <thead class="text-muted small">
                        <tr><th>Produit</th><th>Stock</th><th>Seuil</th></tr>
                    </thead>
                    <tbody>
                        @forelse($produitsCritiques as $produit)
                        <tr>
                            <td>{{ $produit->nom }}</td>
                            <td class="text-danger fw-bold">{{ $produit->stock }}</td>
                            <td>{{ $produit->seuil_alerte }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="3" class="text-center text-muted">Aucun produit critique</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow-sm p-3">
                <h5>🔥 Produits les plus vendus</h5>
                <table class="table table-sm">
                    <thead class="text-muted small">
                        <tr><th>Produit</th><th>Quantité sortie</th></tr>
                    </thead>
                    <tbody>
                        @forelse($topProduitsSortie as $item)
                        <tr>
                            <td>{{ $item->produit->nom ?? '—' }}</td>
                            <td class="fw-bold">{{ $item->total }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="2" class="text-center text-muted">Aucune donnée</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- ACTIVITÉS RÉCENTES -->
    <div class="row g-4 mt-1">
        <div class="col-md-6">
            <div class="card shadow-sm p-3">
                <h5>🧾 Dernières factures</h5>
                <table class="table table-sm">
                    <thead class="text-muted small"><tr><th>Numéro</th><th>Total</th><th></th></tr></thead>
                    <tbody>
                        @foreach($recentFactures as $facture)
                        <tr>
                            <td>{{ $facture->numero_facture }}</td>
                            <td>{{ number_format($facture->total, 0, ',', ' ') }} FCFA</td>
                            <td class="text-end">
                                <a href="{{ route('factures.show', $facture->id) }}" class="btn btn-sm btn-outline-primary">Voir</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow-sm p-3">
                <h5>📦 Mouvements de stock récents</h5>
                <table class="table table-sm">
                    <thead class="text-muted small"><tr><th>Produit</th><th>Type</th><th>Qté</th></tr></thead>
                    <tbody>
                        @foreach($recentMouvements as $mvt)
                        <tr>
                            <td>{{ $mvt->produit->nom ?? '—' }}</td>
                            <td>
                                <span class="badge {{ $mvt->type == 'entree' ? 'bg-success' : 'bg-danger' }}">
                                    {{ ucfirst($mvt->type) }}
                                </span>
                            </td>
                            <td>{{ $mvt->quantite }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('caChart').getContext('2d');
    const caChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: {!! json_encode($chartLabels) !!}, // ex: ['01 Jan', '02 Jan', ...]
            datasets: [
                {
                    label: 'Reçus encaissés',
                    data: {!! json_encode($chartRecu) !!},
                    borderColor: '#28a745',
                    backgroundColor: 'rgba(40, 167, 69, 0.2)',
                    fill: true,
                    tension: 0.3
                },
                {
                    label: 'Pro-forma',
                    data: {!! json_encode($chartProforma) !!},
                    borderColor: '#ffc107',
                    backgroundColor: 'rgba(255, 193, 7, 0.2)',
                    fill: true,
                    tension: 0.3
                }
            ]
        },
        options: {
            responsive: true,
            interaction: { mode: 'index', intersect: false },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': ' + context.formattedValue + ' FCFA';
                        }
                    }
                },
                legend: { position: 'top' }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { callback: value => value.toLocaleString() + ' FCFA' }
                },
                x: {
                    title: { display: true, text: 'Jour' }
                }
            }
        }
    });
</script>
@endpush
