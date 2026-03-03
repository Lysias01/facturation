@extends('layouts.app')

@section('title', 'Historique des activites')

@section('content')
{{-- Page Header --}}
<div class="page-header">
    <div>
        <h1 class="page-title">Historique des activites</h1>
        <p class="page-subtitle">Suivez toutes les actions effectuees</p>
    </div>
    @auth
        @if(auth()->user()->isAdmin())
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#cleanupModal">
                    <i class="bi bi-trash me-1"></i> Nettoyer
                </button>
                <div class="dropdown">
                    <button class="btn btn-success dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-download me-1"></i> Exporter
                    </button>
                    <ul class="dropdown-menu p-3" style="width: 300px;">
                        <li>
                            <form method="GET" action="{{ route('historique.export.excel') }}" class="mb-3">
                                <h6 class="dropdown-header"><i class="bi bi-file-earmark-excel me-1"></i> Export Excel</h6>
                                <div class="mb-2">
                                    <label class="form-label small">Date debut</label>
                                    <input type="date" name="date_from" class="form-control form-control-sm" value="{{ $dateFrom ?? '' }}">
                                </div>
                                <div class="mb-2">
                                    <label class="form-label small">Date fin</label>
                                    <input type="date" name="date_to" class="form-control form-control-sm" value="{{ $dateTo ?? '' }}">
                                </div>
                                <div class="mb-2">
                                    <label class="form-label small">Utilisateur</label>
                                    <select name="user_role" class="form-select form-select-sm">
                                        <option value="">Tous</option>
                                        <option value="admin">Admin</option>
                                        <option value="employe">Employe</option>
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-success btn-sm w-100">
                                    <i class="bi bi-download me-1"></i> Excel
                                </button>
                            </form>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="GET" action="{{ route('historique.export.pdf') }}">
                                <h6 class="dropdown-header"><i class="bi bi-file-earmark-pdf me-1"></i> Export PDF</h6>
                                <div class="mb-2">
                                    <label class="form-label small">Date debut</label>
                                    <input type="date" name="date_from" class="form-control form-control-sm" value="{{ $dateFrom ?? '' }}">
                                </div>
                                <div class="mb-2">
                                    <label class="form-label small">Date fin</label>
                                    <input type="date" name="date_to" class="form-control form-control-sm" value="{{ $dateTo ?? '' }}">
                                </div>
                                <div class="mb-2">
                                    <label class="form-label small">Nombre</label>
                                    <select name="per_page" class="form-select form-select-sm">
                                        <option value="25" selected>25</option>
                                        <option value="50">50</option>
                                        <option value="100">100</option>
                                        <option value="500">500</option>
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-danger btn-sm w-100">
                                    <i class="bi bi-download me-1"></i> PDF
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        @endif
    @endauth
</div>

{{-- Cleanup Modal --}}
@auth
    @if(auth()->user()->isAdmin())
<div class="modal fade" id="cleanupModal" tabindex="-1" aria-labelledby="cleanupModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background-color: rgba(255, 193, 7, 0.15);">
                <h5 class="modal-title" id="cleanupModalLabel" style="color: #997404;">
                    <i class="bi bi-trash me-2"></i>Nettoyer les logs
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('historique.cleanup') }}">
                @csrf
                @method('DELETE')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Type de nettoyage</label>
                        <div class="btn-group w-100" role="group">
                            <input type="radio" class="btn-check" name="cleanup_type" id="cleanup_days" value="days" checked autocomplete="off">
                            <label class="btn btn-outline-warning" for="cleanup_days">Par jours</label>
                            
                            <input type="radio" class="btn-check" name="cleanup_type" id="cleanup_date_range" value="date_range" autocomplete="off">
                            <label class="btn btn-outline-warning" for="cleanup_date_range">Par dates</label>
                        </div>
                    </div>
                    
                    <div id="daysOption">
                        <div class="mb-3">
                            <label class="form-label">Supprimer les logs plus vieux de</label>
                            <div class="input-group">
                                <input type="number" name="days" class="form-control" min="1" max="365" placeholder="30" value="30">
                                <span class="input-group-text">jours</span>
                            </div>
                            <small class="text-muted">Les logs plus anciens que le nombre de jours specifie seront supprimes.</small>
                        </div>
                    </div>
                    
                    <div id="dateRangeOption" style="display: none;">
                        <div class="mb-3">
                            <label class="form-label">Periode a supprimer</label>
                            <div class="input-group mb-2">
                                <span class="input-group-text">Du</span>
                                <input type="date" name="date_from" class="form-control">
                            </div>
                            <div class="input-group">
                                <span class="input-group-text">Au</span>
                                <input type="date" name="date_to" class="form-control">
                            </div>
                        </div>
                    </div>
                    
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        <strong>Attention :</strong> Cette action est irreversible !
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-warning" onclick="return confirm('Etes-vous sur ? Cette action est irreversible !')">
                        <i class="bi bi-trash me-1"></i> Nettoyer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
    @endif
@endauth

{{-- Stats Cards --}}
@php
$stats = $stats ?? ['total' => 0, 'today' => 0, 'total_impact' => 0, 'with_financial_impact' => 0];
@endphp
<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-value">{{ $stats['total'] }}</div>
                    <div class="stat-label">Total actions</div>
                </div>
                <div class="stat-icon" style="background-color: rgba(13, 110, 253, 0.1); color: #0d6efd;">
                    <i class="bi bi-activity"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card" style="border-left-color: #198754;">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-value">{{ $stats['today'] }}</div>
                    <div class="stat-label">Aujourd'hui</div>
                </div>
                <div class="stat-icon" style="background-color: rgba(25, 135, 84, 0.1); color: #198754;">
                    <i class="bi bi-calendar-check"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card" style="border-left-color: #ffc107;">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-value">{{ number_format($stats['total_impact'], 0, ',', ' ') }}</div>
                    <div class="stat-label">Chiffre affaires</div>
                </div>
                <div class="stat-icon" style="background-color: rgba(255, 193, 7, 0.15); color: #997404;">
                    <i class="bi bi-currency-exchange"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card" style="border-left-color: #0dcaf0;">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-value">{{ $stats['with_financial_impact'] }}</div>
                    <div class="stat-label">Ventes realisees</div>
                </div>
                <div class="stat-icon" style="background-color: rgba(13, 202, 240, 0.1); color: #055160;">
                    <i class="bi bi-cart-check"></i>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Filters --}}
<form method="GET" class="modern-card mb-4">
    <div class="card-body">
        <div class="row g-3 align-items-end">
            <div class="col-12 col-md-2">
                <label class="form-label">Periode</label>
                <select name="date_preset" class="form-select" onchange="this.form.submit()">
                    <option value="">Toutes</option>
                    <option value="today" {{ request('date_preset') == 'today' ? 'selected' : '' }}>Aujourd'hui</option>
                    <option value="week" {{ request('date_preset') == 'week' ? 'selected' : '' }}>Cette semaine</option>
                    <option value="month" {{ request('date_preset') == 'month' ? 'selected' : '' }}>Ce mois</option>
                    <option value="year" {{ request('date_preset') == 'year' ? 'selected' : '' }}>Cette annee</option>
                </select>
            </div>
            <div class="col-12 col-md-2">
                <label class="form-label">Date debut</label>
                <input type="date" name="date_from" class="form-control" value="{{ $dateFrom ?? '' }}">
            </div>
            <div class="col-12 col-md-2">
                <label class="form-label">Date fin</label>
                <input type="date" name="date_to" class="form-control" value="{{ $dateTo ?? '' }}">
            </div>
            <div class="col-12 col-md-2">
                <label class="form-label">Action</label>
                <select name="action" class="form-select">
                    <option value="">Toutes</option>
                    <option value="created" {{ $action == 'created' ? 'selected' : '' }}>Ajout</option>
                    <option value="updated" {{ $action == 'updated' ? 'selected' : '' }}>Modification</option>
                    <option value="deleted" {{ $action == 'deleted' ? 'selected' : '' }}>Suppression</option>
                    <option value="validated" {{ $action == 'validated' ? 'selected' : '' }}>Validation</option>
                    <option value="stock_update" {{ $action == 'stock_update' ? 'selected' : '' }}>Stock</option>
                </select>
            </div>
            <div class="col-12 col-md-2">
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1">
                        <i class="bi bi-search me-1"></i> Filtrer
                    </button>
                    <a href="{{ route('historique.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</form>

{{-- Activity Table --}}
<div class="modern-card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Utilisateur</th>
                        <th>Action</th>
                        <th class="text-end">Montant</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                        <tr>
                            <td>
                                <div>{{ \Carbon\Carbon::parse($log->created_at)->format('d/m/Y') }}</div>
                                <small class="text-muted">{{ \Carbon\Carbon::parse($log->created_at)->format('H:i') }}</small>
                            </td>
                            <td>
                                <span class="badge" style="background-color: {{ $log->user && $log->user->role === 'admin' ? '#0d6efd' : '#6c757d' }}; color: #fff;">
                                    {{ $log->user->name ?? 'Systeme' }}
                                </span>
                            </td>
                            <td>
                                @if($log->action == 'created')
                                    <i class="bi bi-plus-circle" style="color: #198754;"></i>
                                @elseif($log->action == 'updated')
                                    <i class="bi bi-pencil" style="color: #0d6efd;"></i>
                                @elseif($log->action == 'deleted')
                                    <i class="bi bi-trash" style="color: #dc3545;"></i>
                                @elseif($log->action == 'validated')
                                    <i class="bi bi-check-circle" style="color: #ffc107;"></i>
                                @elseif($log->action == 'stock_update')
                                    <i class="bi bi-box-seam" style="color: #0dcaf0;"></i>
                                @else
                                    <i class="bi bi-circle" style="color: #6c757d;"></i>
                                @endif
                                {{ $log->description }}
                            </td>
                            <td class="text-end">
                                @if($log->impact_financier && $log->impact_financier > 0)
                                    <span class="badge" style="background-color: #198754; color: #fff;">
                                        +{{ number_format($log->impact_financier, 0, ',', ' ') }}
                                    </span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4">
                                <div class="empty-state">
                                    <i class="bi bi-clock-history"></i>
                                    <h5>Aucune activite</h5>
                                    <p class="text-muted">Aucune activite trouvee.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($logs->hasPages())
    <div class="card-footer bg-white">
        <nav>
            <ul class="pagination justify-content-center mb-0" style="flex-wrap: wrap;">
                @foreach ($logs->links()->elements as $element)
                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $logs->currentPage())
                                <li class="page-item active"><span class="page-link" style="background-color: #0d6efd; border-color: #0d6efd;">{{ $page }}</span></li>
                            @else
                                <li class="page-item"><a class="page-link" href="{{ $url }}" style="color: #0d6efd;">{{ $page }}</a></li>
                            @endif
                        @endforeach
                    @endif
                @endforeach
            </ul>
        </nav>
    </div>
    @endif
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var cleanupDays = document.getElementById('cleanup_days');
    var cleanupDateRange = document.getElementById('cleanup_date_range');
    var daysOption = document.getElementById('daysOption');
    var dateRangeOption = document.getElementById('dateRangeOption');
    
    function toggleOptions() {
        if (cleanupDateRange && cleanupDateRange.checked) {
            daysOption.style.display = 'none';
            dateRangeOption.style.display = 'block';
        } else if (cleanupDays) {
            daysOption.style.display = 'block';
            dateRangeOption.style.display = 'none';
        }
    }
    
    if (cleanupDays) cleanupDays.addEventListener('change', toggleOptions);
    if (cleanupDateRange) cleanupDateRange.addEventListener('change', toggleOptions);
});
</script>
@endsection
