@extends('auth::layout')

@section('title', 'Reportes de ventas')

@section('content')
<div class="min-vh-100 bg-light py-4">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h1 class="h4 fw-semibold mb-1">Reportes de ventas por evento</h1>
                <p class="text-muted small mb-0">Filtra por periodo para ver el resumen de ventas.</p>
            </div>
            <a href="/admin/dashboard" class="btn btn-outline-primary btn-sm">Volver al dashboard</a>
        </div>

        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body">
                <h2 class="h6 fw-semibold mb-3">Periodo</h2>
                <div class="btn-group" role="group" aria-label="Rango de fechas">
                    <button type="button" class="btn btn-outline-primary btn-sm" data-range="daily">Hoy</button>
                    <button type="button" class="btn btn-outline-primary btn-sm" data-range="weekly">Últimos 7 días</button>
                    <button type="button" class="btn btn-outline-primary btn-sm" data-range="monthly">Últimos 30 días</button>
                </div>
            </div>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-12 col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <p class="text-muted small mb-1">Entradas vendidas</p>
                        <p class="h4 mb-0" id="rep-total-tickets">-</p>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <p class="text-muted small mb-1">Importe total</p>
                        <p class="h4 mb-0" id="rep-total-amount">-</p>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <p class="text-muted small mb-1">Eventos con ventas</p>
                        <p class="h4 mb-0" id="rep-events-count">-</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h2 class="h6 fw-semibold mb-0">Ventas por evento</h2>
                        <small class="text-muted" id="rep-period-label"></small>
                    </div>
                    <button type="button" id="btn-export-pdf" class="btn btn-outline-primary btn-sm">
                        <i class="bi bi-file-earmark-pdf me-1"></i> Descargar PDF
                    </button>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Evento</th>
                                <th class="text-end">Entradas</th>
                                <th class="text-end">Importe total</th>
                            </tr>
                        </thead>
                        <tbody id="rep-table-body">
                            <tr>
                                <td colspan="3" class="text-center text-muted small py-3">Selecciona un periodo para ver los datos.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const rangeButtons = document.querySelectorAll('[data-range]');
    let currentRange = 'daily';
    let lastReportData = null;

    async function loadReport(range) {
        currentRange = range;
        rangeButtons.forEach(btn => {
            btn.classList.toggle('active', btn.getAttribute('data-range') === range);
        });

        const token = localStorage.getItem('auth_token');
        if (!token) {
            alert('Debes iniciar sesión como administrador.');
            return;
        }

        const params = new URLSearchParams({ range });

        try {
            const res = await fetch('/api/admin/reports/sales?' + params.toString(), {
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Accept': 'application/json'
                }
            });

            if (!res.ok) throw new Error('Error al cargar los datos');
            
            const data = await res.json();

            // Guardar última data para exportar
            lastReportData = data;
            const totalTickets = Number(data.summary.total_tickets ?? 0) || 0;
            const totalAmount = Number(data.summary.total_amount ?? 0) || 0;
            const eventsCount = Number(data.summary.events_count ?? 0) || 0;

            document.getElementById('rep-total-tickets').textContent = totalTickets;
            document.getElementById('rep-total-amount').textContent = totalAmount.toFixed(2);
            document.getElementById('rep-events-count').textContent = eventsCount;
            document.getElementById('rep-period-label').textContent = data.summary.label || '';

            const tbody = document.getElementById('rep-table-body');
            tbody.innerHTML = '';

            if (!data.events || data.events.length === 0) {
                const tr = document.createElement('tr');
                tr.innerHTML = '<td colspan="3" class="text-center text-muted small py-3">No hay ventas en este periodo.</td>';
                tbody.appendChild(tr);
                return;
            }

            data.events.forEach(ev => {
                const totalTicketsEv = Number(ev.total_tickets ?? 0) || 0;
                const totalAmountEv = Number(ev.total_amount ?? 0) || 0;
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>${ev.event_name}</td>
                    <td class="text-end">${totalTicketsEv}</td>
                    <td class="text-end">${totalAmountEv.toFixed(2)}</td>
                `;
                tbody.appendChild(tr);
            });
        } catch (error) {
            console.error('Error al cargar el reporte:', error);
            alert('Error al cargar el reporte: ' + error.message);
        }
    }

    // Manejadores de eventos para los botones de rango
    rangeButtons.forEach(btn => {
        btn.addEventListener('click', () => loadReport(btn.getAttribute('data-range')));
    });

    // Función para exportar a PDF
    const exportPdfBtn = document.getElementById('btn-export-pdf');
    if (exportPdfBtn) {
        exportPdfBtn.addEventListener('click', async () => {
            if (!lastReportData || !lastReportData.events || lastReportData.events.length === 0) {
                alert('No hay datos para exportar.');
                return;
            }

            const token = localStorage.getItem('auth_token');
            if (!token) {
                alert('Debes iniciar sesión como administrador.');
                return;
            }

            try {
                // Mostrar indicador de carga
                const originalText = exportPdfBtn.innerHTML;
                exportPdfBtn.disabled = true;
                exportPdfBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Generando...';

                const response = await fetch(`/api/admin/reports/sales/pdf?range=${currentRange}`, {
                    headers: {
                        'Authorization': 'Bearer ' + token,
                        'Accept': 'application/pdf'
                    }
                });

                if (!response.ok) {
                    throw new Error('Error al generar el PDF');
                }

                // Crear y descargar el PDF
                const blob = await response.blob();
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = `reporte_ventas_${currentRange}.pdf`;
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
                window.URL.revokeObjectURL(url);
            } catch (error) {
                console.error('Error:', error);
                alert('Error al generar el PDF: ' + error.message);
            } finally {
                // Restaurar el botón
                if (exportPdfBtn) {
                    exportPdfBtn.disabled = false;
                    exportPdfBtn.innerHTML = '<i class="bi bi-file-earmark-pdf me-1"></i> Descargar PDF';
                }
            }
        });
    }

    // Cargar datos iniciales
    loadReport('daily');
});
</script>
@endsection
