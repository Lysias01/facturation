@extends('layouts.app')

@section('title', 'Modifier document')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <h1 class="h4 mb-3">Modifier Pro-forma / Réçu</h1>

        @if($errors->has('stock'))
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->get('stock') as $msg)
                        <li>{!! $msg !!}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('factures.update', $facture->id) }}" method="POST" class="card p-3 shadow-sm">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label class="form-label">Client</label>
                <select name="client_id" class="form-select select-client" required>
                    <option value="">-- Sélectionner un client --</option>
                    @foreach($clients as $client)
                        <option value="{{ $client->id }}" {{ $facture->client_id == $client->id ? 'selected' : '' }}>
                            {{ $client->nom }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Type</label>
                <select name="type_document" class="form-select" required>
                    <option value="pro-forma" {{ $facture->type_document=='pro-forma'?'selected':'' }}>Pro-forma</option>
                    <option value="recu" {{ $facture->type_document=='recu'?'selected':'' }}>Réçu</option>
                </select>
            </div>

            <h5 class="mt-3">Lignes</h5>
            <div class="table-responsive mb-3">
                <table class="table table-bordered" id="lignes_table">
                    <thead class="table-light">
                        <tr>
                            <th>Produit</th>
                            <th style="width:110px;">Quantité</th>
                            <th style="width:160px;">Prix unitaire</th>
                            <th style="width:140px;">Total</th>
                            <th style="width:90px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @error('lignes')
                            <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                        @foreach($facture->lignes as $i => $ligne)
                            <tr class="@error("lignes.$i.quantite") table-danger @enderror">
                                <td>
                                    <select name="lignes[{{$i}}][produit_id]" class="form-select select-produit" required>
                                        <option value="">-- Sélectionner un produit --</option>
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
                                        min="1" required
                                    >

                                    @error("lignes.$i.quantite")
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </td>
                                <td><input type="number" name="lignes[{{$i}}][prix_unitaire]" class="form-control prix" value="{{ $ligne->prix_unitaire }}" readonly></td>
                                <td><input type="text" class="form-control total_ligne" value="{{ $ligne->quantite * $ligne->prix_unitaire }}" readonly></td>
                                <td class="text-center"><button type="button" class="btn btn-sm btn-danger remove_ligne">Suppr</button></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-3">
                <button type="button" id="add_ligne" class="btn btn-outline-secondary">Ajouter une ligne</button>
                <div class="text-end">
                    <label class="form-label mb-0">Total</label>
                    <div><strong><span id="total_facture">0.00</span> CFA</strong></div>
                </div>
            </div>

            <div class="d-flex justify-content-between">
                <a href="{{ route('factures.index') }}" class="btn btn-link">Annuler</a>
                <button class="btn btn-primary">Mettre à jour</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(()=>$('.select-client').select2({placeholder:"Choisir un client",allowClear:true,width:'100%'}));

let index = {{ count($facture->lignes) }};
const produitsData = @json($produits);

function recalculTotalGlobal(){
    let total = 0;
    document.querySelectorAll('#lignes_table tbody tr').forEach(r => {
        total += parseFloat(r.querySelector('.total_ligne').value || 0);
    });
    document.getElementById('total_facture').innerText = total.toFixed(2);
}

document.getElementById('add_ligne').addEventListener('click', () => {
    const tbody = document.querySelector('#lignes_table tbody');
    const row = document.createElement('tr');

    let options = `<option value="">-- Sélectionner un produit --</option>`;
    produitsData.forEach(p => {
        options += `<option value="${p.id}" data-prix="${p.prix_vente}">${p.nom}</option>`;
    });

    row.innerHTML = `
        <td><select name="lignes[${index}][produit_id]" class="form-select select-produit" required>${options}</select></td>
        <td><input type="number" name="lignes[${index}][quantite]" class="form-control quantite" value="1" min="1" required></td>
        <td><input type="number" name="lignes[${index}][prix_unitaire]" class="form-control prix" value="0" readonly></td>
        <td><input type="text" class="form-control total_ligne" value="0.00" readonly></td>
        <td class="text-center"><button type="button" class="btn btn-sm btn-danger remove_ligne">Suppr</button></td>
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

        const q = parseFloat(row.querySelector('.quantite').value || 0);
        row.querySelector('.total_ligne').value = (q * prixInput.value).toFixed(2);
        recalculTotalGlobal();
    }
});

document.addEventListener('input', e => {
    if(e.target.classList.contains('quantite')) {
        const row = e.target.closest('tr');
        const q = parseFloat(row.querySelector('.quantite').value || 0);
        const p = parseFloat(row.querySelector('.prix').value || 0);
        row.querySelector('.total_ligne').value = (q * p).toFixed(2);
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
