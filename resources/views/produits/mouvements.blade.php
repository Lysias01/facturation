@extends('layouts.app')

@section('title', 'Historique du produit')

@section('content')
<h4>Mouvements du produit : {{ $produit->nom }}</h4>

<table class="table table-bordered mt-3">
    <thead>
        <tr>
            <th>Date</th>
            <th>Type</th>
            <th>Quantité</th>
            <th>Evènement</th>
        </tr>
    </thead>
    <tbody>
        @forelse($mouvements as $m)
            <tr>
                <td>{{ $m->created_at }}</td>
                <td>{{ ucfirst($m->type) }}</td>
                <td>{{ $m->quantite }}</td>
                <td>{{ $m->raison }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="4" class="text-center">Aucun mouvement enregistré.</td>
            </tr>
        @endforelse
    </tbody>
</table>

<a href="{{ route('produits.index') }}" class="btn btn-secondary mt-3">← Retour aux produits</a>
@endsection
