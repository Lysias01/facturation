@extends('layouts.app')

@section('title', 'Documents')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h4 mb-0">Documents</h1>
    <a href="{{ route('factures.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Créer Reçu / Pro-forma
    </a>
</div>

{{-- Filtres --}}
<div class="row g-2 mb-4">
    <div class="col-md-3">
        <select id="filter_type" class="form-select">
            <option value="">Tous les types</option>
            <option value="pro-forma">Pro-forma</option>
            <option value="recu">Réçu</option>
        </select>
    </div>

    <div class="col-md-6 position-relative">
        <i class="bi bi-search position-absolute"
           style="top: 50%; left: 10px; transform: translateY(-50%); color:#777;">
        </i>
        <input id="search_input"
               type="text"
               class="form-control ps-5"
               placeholder="Rechercher par numéro ou client..."
               value="{{ request('search') }}">
    </div>
</div>

<div class="table-responsive">
    <table class="table table-bordered table-hover align-middle shadow-sm">
        <thead class="table-light">
            <tr>
                <th>Numéro</th>
                <th>Client</th>
                <th>Type</th>
                <th>Total (CFA)</th>
                <th class="text-end">Actions</th>
            </tr>
        </thead>

        <tbody id="factures_table">
            @forelse($factures as $facture)
                <tr data-type="{{ $facture->type_document }}">
                    <td class="fw-bold">{{ $facture->numero_facture }}</td>
                    <td>{{ $facture->client->nom ?? '—' }} {{ $facture->client->prenom ?? '—' }}</td>
                    <td>
                        @if($facture->type_document === 'pro-forma')
                            <span class="badge bg-warning text-dark">Pro-forma</span>
                        @else
                            <span class="badge bg-success">Réçu</span>
                        @endif
                    </td>
                    <td class="fw-semibold">{{ number_format($facture->total, 2, ',', ' ') }}</td>
                    <td class="text-end">
                        <a href="{{ route('factures.show', $facture->id) }}"
                           class="btn btn-sm btn-success me-1">
                            <i class="bi bi-eye"></i> Voir
                        </a>

                        @if($facture->type_document === 'pro-forma')
                            @if(auth()->user()->isAdmin() || $facture->user_id === auth()->id())
                                <a href="{{ route('factures.edit', $facture->id) }}"
                                   class="btn btn-sm btn-secondary me-1">
                                    <i class="bi bi-pencil"></i> Modifier
                                </a>
                            @endif

                            <form action="{{ route('factures.valider', $facture->id) }}"
                                  method="POST"
                                  class="d-inline-block me-1"
                                  onsubmit="return confirm('Valider ce Pro-forma en Réçu ?');">
                                @csrf
                                @method('PUT')
                                <button class="btn btn-sm btn-primary">
                                    <i class="bi bi-check-circle"></i> Valider
                                </button>
                            </form>

                            @if(!$facture->isDefinitive())
                                @if(auth()->user()->isAdmin() || $facture->user_id === auth()->id())
                                    <form action="{{ route('factures.destroy', $facture->id) }}"
                                          method="POST"
                                          class="d-inline-block"
                                          onsubmit="return confirm('Supprimer ce document ?');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-danger">
                                            <i class="bi bi-trash"></i> Supprimer
                                        </button>
                                    </form>
                                @endif
                            @endif
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center py-4 text-muted">
                        Aucune facture trouvée.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection

@push('scripts')
<script>
const searchInput = document.getElementById('search_input');
const filterType = document.getElementById('filter_type');

function filterRows() {
    const search = searchInput.value.toLowerCase().trim();
    const type = filterType.value;

    document.querySelectorAll('#factures_table tr').forEach(row => {
        const numero = row.querySelector('td:nth-child(1)').innerText.toLowerCase();
        const client = row.querySelector('td:nth-child(2)').innerText.toLowerCase();
        const rowType = row.dataset.type;

        const matchesSearch = numero.includes(search) || client.includes(search);
        const matchesType = type === '' || type === rowType;

        row.style.display = (matchesSearch && matchesType) ? '' : 'none';
    });
}

searchInput.addEventListener('input', filterRows);
filterType.addEventListener('change', filterRows);
</script>
@endpush
