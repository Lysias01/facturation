@extends('layouts.app')

@section('title', 'Modifier le document')

@section('content')
{{-- Page Header --}}
<div class="page-header">
    <div>
        <h1 class="page-title">Modifier le document</h1>
        <p class="page-subtitle">{{ $facture->numero_facture }}</p>
    </div>
</div>

{{-- Error Alert --}}
@if($errors->has('stock'))
    <div class="alert alert-danger d-flex align-items-center" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>
        <div>
            <ul class="mb-0">
                @foreach($errors->get('stock') as $msg)
                    <li>{!! $msg !!}</li>
                @endforeach
            </ul>
        </div>
    </div>
@endif

<div class="row justify-content-center">
    <div class="col-12 col-lg-10">
        <div class="modern-card">
            <div class="card-body">
                <form action="{{ route('factures.update', $facture->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row g-3 mb-4">
                        <div class="col-12 col-md-6">
                            <label for="client_id" class="form-label">Client</label>
                            <select name="client_id" 
                                    id="client_id" 
                                    class="form-select select2 @error('client_id') is-invalid @enderror" 
                                    required>
                                <option value="">-- Selectionner un client --</option>
                                @foreach($clients as $client)
                                    <option value="{{ $client->id }}" {{ $facture->client_id == $client->id ? 'selected' : '' }}>
                                        {{ $client->nom }} {{ $client->prenom }}
                                    </option>
                                @endforeach
                            </select>
                            @error('client_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12 col-md-6">
                            <label for="type_document" class="form-label">Type de document</label>
                            <select name="type_document" 
                                    id="type_document" 
                                    class="form-select @error('type_document') is-invalid @enderror" 
                                    required>
                                <option value="pro-forma" {{ $facture->type_document=='pro-forma'?'selected':'' }}>Pro-forma</option>
                                <option value="recu" {{ $facture->type_document=='recu'?'selected':'' }}>Recu</option>
                            </select>
                            @error('type_document')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <hr class="my-4">

                    <h5 class="mb-3"><i class="bi bi-list-ul me-2"></i>Lignes du document</h5>

                    {{-- Lines Table --}}
                    <div class="table-responsive mb-3">
                        <table class="table table-bordered table-sm mb-0" id="lignes_table">
                            <thead class="table-light">
                                <tr>
                                    <th style="min-width:150px;">Produit</th>
                                    <th style="min-width:80px;">Qte</th>
                                    <th style="min-width:100px;">Prix</th>
                                    <th style="min-width:100px;">Total</th>
                                    <th style="width:50px;"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @error('lignes')
                                    <tr>
                                        <td colspan="5">
                                            <div class="alert alert-danger mb-0">{{ $message }}</div>
                                        </td>
                                    </tr>
                                @enderror
                                @foreach($facture->lignes as $i => $ligne)
                                    <tr class="@error("lignes.$i.quantite") table-danger @enderror">
                                        <td>
                                            <select name="lignes[{{$i}}][produit_id]" class="form-select select-produit" required>
                                                <option value="">-- Selectionner --</option>
                                                @foreach($produits as $produit)
                                                    <option value="{{ $produit->id }}" data-prix="{{ $produit->prix_vente }}"
                                                        {{ $ligne->produit_id == $produit->id ? 'selected' : '' }}>
                                                        {{ $produit->nom }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <input type="number"
                                                name="lignes[{{$i}}][quantite]"
                                                class="form-control quantite @error("lignes.$i.quantite") is-invalid @enderror"
                                                value="{{ $ligne->quantite }}"
                                                min="1" required>
                                        </td>
                                        <td>
                                            <input type="number" 
                                                   name="lignes[{{$i}}][prix_unitaire]" 
                                                   class="form-control prix" 
                                                   value="{{ $ligne->prix_unitaire }}" 
                                                   readonly>
                                        </td>
                                        <td>
                                            <input type="text" 
                                                   class="form-control total_ligne" 
                                                   value="{{ $ligne->quantite * $ligne->prix_unitaire }}" 
                                                   readonly>
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-sm btn-outline-danger remove_ligne">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
                        <button type="button" id="add_ligne" class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-plus-lg me-1"></i> Ajouter
                        </button>
                        
                        <div class="text-end">
                            <span class="text-muted d-block" style="font-size: 0.75rem;">Total</span>
                            <span class="h5 mb-0 fw-bold"><span id="total_facture">0</span> CFA</span>
                        </div>
                    </div>

                    <div class="d-flex gap-2 justify-content-end mt-4 pt-3 border-top">
                        <a href="{{ route('factures.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-x-lg me-1"></i> Annuler
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-1"></i> Mettre a jour
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('.select2').select2({
        placeholder: "Choisir un client",
        allowClear: true,
        width: '100%',
        language: 'fr'
    });
});

let index = {{ count($facture->lignes) }};
const produitsData = @json($produits);

function recalculTotalGlobal(){
    let total = 0;
    document.querySelectorAll('#lignes_table tbody tr').forEach(r => {
        if (r.querySelector('.total_ligne')) {
            total += parseInt(r.querySelector('.total_ligne').value || 0);
        }
    });
    document.getElementById('total_facture').innerText = total.toLocaleString('fr-FR');
}

document.getElementById('add_ligne').addEventListener('click', () => {
    const tbody = document.querySelector('#lignes_table tbody');
    const row = document.createElement('tr');

    let options = `<option value="">-- Selectionner --</option>`;
    produitsData.forEach(p => {
        options += `<option value="${p.id}" data-prix="${p.prix_vente}">${p.nom}</option>`;
    });

    row.innerHTML = `
        <td><select name="lignes[${index}][produit_id]" class="form-select form-select-sm select-produit" required>${options}</select></td>
        <td><input type="number" name="lignes[${index}][quantite]" class="form-control form-control-sm quantite" value="1" min="1" required></td>
        <td><input type="number" name="lignes[${index}][prix_unitaire]" class="form-control form-control-sm prix" value="0" readonly></td>
        <td><input type="text" class="form-control form-control-sm total_ligne" value="0" readonly></td>
        <td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger remove_ligne"><i class="bi bi-trash"></i></button></td>
    `;

    tbody.appendChild(row);
    index++;
});

document.addEventListener('change', e => {
    if(e.target.classList.contains('select-produit')) {
        const row = e.target.closest('tr');
        const prixInput = row.querySelector('.prix');
        const selectedOption = e.target.selectedOptions[0];
        prixInput.value = selectedOption.dataset.prix || 0;

        const q = parseInt(row.querySelector('.quantite').value || 0);
        row.querySelector('.total_ligne').value = q * prixInput.value;

        recalculTotalGlobal();
    }
});

document.addEventListener('input', e => {
    if(e.target.classList.contains('quantite')) {
        const row = e.target.closest('tr');
        const q = parseInt(row.querySelector('.quantite').value || 0);
        const p = parseInt(row.querySelector('.prix').value || 0);
        row.querySelector('.total_ligne').value = q * p;
        recalculTotalGlobal();
    }
});

document.addEventListener('click', e => {
    if (e.target.classList.contains('remove_ligne')) {
        e.target.closest('tr').remove();
        recalculTotalGlobal();
    }
});

recalculTotalGlobal();
</script>
@endpush
