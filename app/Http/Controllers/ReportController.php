<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    public function salesReportPdf(Request $request)
    {
        $range = $request->query('range', 'daily');
        $now = now();

        switch ($range) {
            case 'weekly':
                $startDate = $now->copy()->subDays(7);
                $endDate = $now->copy();
                $label = 'Últimos 7 días';
                break;
            case 'monthly':
                $startDate = $now->copy()->subDays(30);
                $endDate = $now->copy();
                $label = 'Últimos 30 días';
                break;
            case 'daily':
            default:
                $startDate = $now->copy()->startOfDay();
                $endDate = $now->copy()->endOfDay();
                $label = 'Hoy';
                break;
        }

        $events = DB::table('purchases as p')
            ->join('purchase_items as pi', 'p.id', '=', 'pi.purchase_id')
            ->join('event_ticket_types as ett', 'pi.ticket_type_id', '=', 'ett.id')
            ->join('events as e', 'p.event_id', '=', 'e.id')
            ->whereBetween('p.created_at', [$startDate, $endDate])
            ->select(
                'e.id as event_id',
                'e.nombre as event_name',
                DB::raw('SUM(pi.cantidad) as total_tickets'),
                DB::raw('SUM(pi.subtotal) as total_amount')
            )
            ->groupBy('e.id', 'e.nombre')
            ->orderBy('total_amount', 'desc')
            ->get();

        $summary = [
            'total_tickets' => $events->sum('total_tickets'),
            'total_amount' => $events->sum('total_amount'),
            'events_count' => $events->count(),
            'label' => $label
        ];

        $pdf = Pdf::loadView('pdf.sales-report', [
            'events' => $events,
            'summary' => $summary,
            'range' => $range,
            'now' => $now->format('d/m/Y H:i:s')
        ]);

        return $pdf->download('reporte_ventas_' . $range . '.pdf');
    }
}
