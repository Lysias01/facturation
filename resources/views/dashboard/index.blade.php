@extends('layouts.app')

@section('title', 'Tableau de bord')

@section('content')
<div class="container-fluid">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h4 mb-0">Tableau de bord</h1>
            <small class="text-muted">Vue d'ensemble</small>
        </div>

        <div class="d-flex gap-2">
            <form method="GET" class="d-flex gap-2">
                <input type="month"
                       name="month"
                       class="form-control"
                       min="2020-01"
                       value="{{ $month->format('Y-m') }}">

                <select name="year" class="form-select">
                    @for($y = now()->year; $y >= 2020; $y--)
                        <option value="{{ $y }}" {{ $y == $year ? 'selected' : '' }}>
                            {{ $y }}
                        </option>
                    @endfor
                </select>

                <button class="btn btn-primary">Filtrer</button>
            </form>

            <form method="GET" action="{{ route('dashboard.exportExcel') }}">
                <input type="hidden" name="month" value="{{ $month->format('Y-m') }}">
                <input type="hidden" name="year" value="{{ $year }}">
                <button class="btn btn-success">Exporter Excel</button>
            </form>
        </div>
    </div>

    <!-- Top cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card shadow-sm p-3 h-100">
                <div class="text-muted small">Pro-forma en attente ce mois</div>
                <div class="h5 fw-bold">{{ $proformasMonthCount }}</div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm p-3 h-100">
                <div class="text-muted small">Reçus validés ce mois</div>
                <div class="h5 fw-bold">{{ $recuMonthCount }}</div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm p-3 h-100">
                <div class="text-muted small">Montant reçu ce mois</div>
                <div class="h5 fw-bold">
                    {{ number_format($recuMonthSum, 2, ',', ' ') }} FCFA
                </div>
                <small class="text-muted">
                    Attendu : {{ number_format($proformasMonthSum, 2, ',', ' ') }} FCFA
                </small>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm p-3 h-100">
                <div class="text-muted small">Total annuel reçu</div>
                <div class="h5 fw-bold">
                    {{ number_format($recuYearSum, 2, ',', ' ') }} FCFA
                </div>
            </div>
        </div>
    </div>

    <!-- Max / Min -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card shadow-sm p-3">
                <div class="text-muted small">Mois le plus actif</div>
                <div class="h5 fw-bold">{{ $maxMonth ?? '—' }}</div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm p-3">
                <div class="text-muted small">Mois le moins actif</div>
                <div class="h5 fw-bold">{{ $minMonth ?? '—' }}</div>
            </div>
        </div>
    </div>

    <!-- Graphique -->
    <div class="card shadow-sm p-3 mb-4">
        <h5>Comparatif Reçu vs Pro-forma</h5>
        <canvas id="chartComparatif"></canvas>
    </div>

    <!-- Derniers documents -->
    <div class="card shadow-sm p-3">
        <h5 class="mb-3">Derniers documents</h5>

        <table class="table table-borderless">
            <thead>
                <tr class="text-muted small">
                    <th>Numéro</th>
                    <th>Client</th>
                    <th>Type</th>
                    <th class="text-end">Total</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentDocuments as $doc)
                    <tr>
                        <td>{{ $doc->numero_facture }}</td>
                        <td>{{ $doc->client->nom ?? '—' }}</td>
                        <td>
                            @if($doc->type_document === 'pro-forma')
                                <span class="badge bg-warning text-dark">Pro-forma</span>
                            @else
                                <span class="badge bg-success">Reçu</span>
                            @endif
                        </td>
                        <td class="text-end">
                            {{ number_format($doc->total, 2, ',', ' ') }} FCFA
                        </td>
                        <td class="text-end">
                            <a href="{{ route('factures.show', $doc->id) }}"
                               class="btn btn-sm btn-outline-success">
                                Voir
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center">
                            Aucun document récent
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('chartComparatif');

new Chart(ctx, {
    type: 'bar',
    data: {
        labels: ['Reçu', 'Pro-forma'],
        datasets: [{
            data: [{{ $recuMonthSum }}, {{ $proformasMonthSum }}],
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display: false }
        }
    }
});
</script>
@endpush
