<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Historique des actions</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f5f5f5; font-weight: bold; }
        .header { text-align: center; margin-bottom: 20px; }
        .footer { text-align: center; margin-top: 20px; font-size: 10px; color: #666; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Historique des actions</h2>
        <p>Généré le {{ now()->format('d/m/Y à H:i') }}</p>
        @if(isset($periodInfo) && $periodInfo)
            <p><strong>Période:</strong> {{ $periodInfo }}</p>
        @endif
    </div>

    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Utilisateur</th>
                <th>Action</th>
                <th>Objet</th>
                <th>Description</th>
                <th>Impact</th>
            </tr>
        </thead>
        <tbody>
            @foreach($logs as $log)
                <tr>
                    <td>{{ $log->created_at->format('d/m/Y H:i') }}</td>
                    <td>{{ $log->user->name ?? 'Système' }}</td>
                    <td>{{ $log->action }}</td>
                    <td>{{ class_basename($log->model_type) }}</td>
                    <td>{{ $log->description }}</td>
                    <td>{{ $log->impact_financier ? number_format($log->impact_financier, 0, ',', ' ') . ' €' : '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Total: {{ $logs->count() }} action(s)</p>
    </div>
</body>
</html>
