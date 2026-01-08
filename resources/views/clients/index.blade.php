@extends('layouts.app')

@section('title', 'Clients')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4 mb-0">Clients</h1>
    <a href="{{ route('clients.create') }}" class="btn btn-primary">Ajouter un client</a>
</div>

<form method="GET" action="{{ route('clients.index') }}" class="mb-3">
    <div class="input-group">
        <input type="text" name="search" class="form-control" placeholder="Rechercher un client par son numéro de téléphone" value="{{ request('search') }}">
        <button class="btn btn-outline-secondary" type="submit">Rechercher</button>
        @if(request('search'))
            <a href="{{ route('clients.index') }}" class="btn btn-outline-danger">Voir la liste</a>
        @endif
    </div>
</form>

<div class="table-responsive">
    <table class="table table-striped table-hover align-middle">
        <thead class="table-light">
            <tr>
                <th>Nom</th>
                <th>Prénom(s)</th>
                <th>Téléphone</th>
                <th>Adresse</th>
                <th class="text-end">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($clients as $client)
                <tr>
                    <td>{{ $client->nom }}</td>
                    <td>{{ $client->prenom }}</td>
                    <td>{{ $client->telephone }}</td>
                    <td>{{ $client->adresse ?? '-' }}</td>
                    <td class="text-end">
                        <a href="{{ route('clients.edit', $client->id) }}" class="btn btn-sm btn-outline-secondary">Modifier</a>
                        <form action="{{ route('clients.destroy', $client->id) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Supprimer ce client ?');">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">Supprimer</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center">Aucun client trouvé. Recherchez par son numéro de téléphone.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
