<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Reporte de Ventas</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h1 {
            font-size: 18px;
            margin: 0;
            color: #2c3e50;
        }
        .header p {
            margin: 5px 0 0;
            color: #7f8c8d;
        }
        .summary {
            background-color: #f8f9fa;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 20px;
        }
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            text-align: center;
        }
        .summary-item {
            background-color: white;
            border-radius: 5px;
            padding: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        .summary-item h3 {
            margin: 0 0 5px 0;
            font-size: 14px;
            color: #7f8c8d;
        }
        .summary-item p {
            margin: 0;
            font-size: 20px;
            font-weight: bold;
            color: #2c3e50;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th {
            background-color: #2c3e50;
            color: white;
            text-align: left;
            padding: 8px;
            font-size: 12px;
        }
        td {
            padding: 8px;
            border-bottom: 1px solid #ddd;
            font-size: 12px;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #7f8c8d;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Reporte de Ventas</h1>
        <p>Generado el: {{ $now }} | Período: {{ $summary['label'] }}</p>
    </div>

    <div class="summary">
        <div class="summary-grid">
            <div class="summary-item">
                <h3>Total Ventas</h3>
                <p>${{ number_format($summary['total_amount'], 2) }}</p>
            </div>
            <div class="summary-item">
                <h3>Total Entradas</h3>
                <p>{{ $summary['total_tickets'] }}</p>
            </div>
            <div class="summary-item">
                <h3>Eventos</h3>
                <p>{{ $summary['events_count'] }}</p>
            </div>
        </div>
    </div>

    @if(count($events) > 0)
        <table>
            <thead>
                <tr>
                    <th>Evento</th>
                    <th class="text-right">Entradas Vendidas</th>
                    <th class="text-right">Monto Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($events as $event)
                <tr>
                    <td>{{ $event->event_name }}</td>
                    <td class="text-right">{{ number_format($event->total_tickets, 0) }}</td>
                    <td class="text-right">${{ number_format($event->total_amount, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p class="text-center">No hay datos disponibles para el período seleccionado.</p>
    @endif

    <div class="footer">
        <p>Sistema de Gestión de Eventos - Generado el {{ $now }}</p>
    </div>
</body>
</html>
