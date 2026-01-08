@extends('layouts.app')

@section('title', 'Historique & Traçabilité')

@section('content')
<div class="container">
    <div class="mb-4">
        <h1 class="h4">Historique des actions</h1>
        <small class="text-muted">
            Toutes les actions effectuées dans le système
        </small>
    </div>

    <div class="card shadow-sm p-3">
        <div class="table-responsive">
            <table class="table table-hover table-sm">
                <thead class="table-light text-muted small">
                    <tr>
                        <th>Date</th>
                        <th>Utilisateur</th>
                        <th>Action</th>
                        <th>Objet</th>
                        <th>Description</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                        <tr>
                            <td>{{ $log->created_at->format('d/m/Y H:i') }}</td>
                            <td>{{ $log->user->name ?? 'Système' }}</td>
                            <td>
                                <span class="badge bg-secondary text-uppercase">
                                    {{ $log->action }}
                                </span>
                            </td>
                            <td>
                                {{ class_basename($log->model_type) }}
                                #{{ $log->model_id }}
                            </td>
                            <td>{{ $log->description }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted">
                                Aucun historique disponible.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $logs->links() }}
        </div>
    </div>
</div>
@endsection
