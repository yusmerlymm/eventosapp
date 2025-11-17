<?php $__env->startSection('title', 'Comprar Entradas'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-4xl mx-auto px-4 py-8">
    <div class="mb-6">
        <a href="/events" class="text-blue-600 hover:underline">&larr; Volver a eventos</a>
    </div>

    <div id="event-info" class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h1 class="text-2xl font-bold mb-2" id="event-name"></h1>
        <p class="text-gray-600 mb-4" id="event-description"></p>
        <div class="grid grid-cols-2 gap-4 text-sm">
            <div><strong>Fecha inicio:</strong> <span id="event-start"></span></div>
            <div><strong>Fecha fin:</strong> <span id="event-end"></span></div>
            <div><strong>Lugar:</strong> <span id="event-venue"></span></div>
            <div><strong>Capacidad:</strong> <span id="event-capacity"></span></div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-xl font-semibold mb-4">Selecciona tus entradas</h2>
        
        <div id="error-message" class="hidden rounded-md bg-red-50 border border-red-200 text-red-700 px-4 py-3 mb-4 text-sm"></div>
        <div id="success-message" class="hidden rounded-md bg-green-50 border border-green-200 text-green-700 px-4 py-3 mb-4 text-sm"></div>

        <form id="purchase-form">
            <div id="ticket-types" class="space-y-4 mb-6"></div>

            <div class="border-t pt-4 mb-6">
                <div class="flex justify-between items-center text-lg font-bold">
                    <span>Total:</span>
                    <span id="total-price">$0.00</span>
                </div>
            </div>

            <button 
                type="submit" 
                id="submit-btn"
                class="w-full bg-blue-600 text-white py-3 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:bg-gray-400"
            >
                Confirmar Compra
            </button>
        </form>
    </div>
</div>

<template id="ticket-type-template">
    <div class="flex items-center justify-between p-4 border rounded-lg">
        <div class="flex-1">
            <h3 class="font-medium" data-field="nombre"></h3>
            <p class="text-lg text-blue-600 font-semibold" data-field="precio"></p>
        </div>
        <div class="flex items-center gap-3">
            <button type="button" class="btn-decrease bg-gray-200 hover:bg-gray-300 w-8 h-8 rounded-full">-</button>
            <input 
                type="number" 
                min="0" 
                value="0" 
                class="cantidad w-16 text-center border rounded-md py-1"
                data-ticket-id=""
                data-precio=""
            >
            <button type="button" class="btn-increase bg-gray-200 hover:bg-gray-300 w-8 h-8 rounded-full">+</button>
        </div>
    </div>
</template>

<script>
// Obtener el ID del evento de la URL /events/{id}/purchase
const pathParts = window.location.pathname.split('/').filter(Boolean);
const eventId = pathParts[pathParts.length - 2]; // El ID está antes de "purchase"
let ticketTypes = [];

async function loadEvent() {
    try {
        const res = await fetch(`/api/events/${eventId}`);
        const event = await res.json();
        
        document.getElementById('event-name').textContent = event.nombre;
        document.getElementById('event-description').textContent = event.descripcion;
        document.getElementById('event-start').textContent = formatDate(event.fecha_inicio);
        document.getElementById('event-end').textContent = formatDate(event.fecha_fin);
        document.getElementById('event-venue').textContent = event.venues?.nombre || '-';
        document.getElementById('event-capacity').textContent = event.capacidad_max;
        
        ticketTypes = event.ticket_types || [];
        renderTicketTypes();
    } catch (error) {
        showError('Error al cargar el evento');
    }
}

function formatDate(dateStr) {
    if (!dateStr) return '-';
    const d = new Date(dateStr.replace(' ', 'T'));
    return d.toLocaleDateString('es-ES', { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' });
}

function renderTicketTypes() {
    const container = document.getElementById('ticket-types');
    const template = document.getElementById('ticket-type-template');
    container.innerHTML = '';
    
    if (ticketTypes.length === 0) {
        container.innerHTML = '<p class="text-gray-600">No hay tipos de entrada disponibles</p>';
        return;
    }
    
    ticketTypes.forEach(ticket => {
        const node = template.content.cloneNode(true);
        node.querySelector('[data-field="nombre"]').textContent = ticket.nombre;
        node.querySelector('[data-field="precio"]').textContent = `$${parseFloat(ticket.precio).toFixed(2)}`;
        
        const input = node.querySelector('.cantidad');
        input.dataset.ticketId = ticket.id;
        input.dataset.precio = ticket.precio;
        
        const btnDecrease = node.querySelector('.btn-decrease');
        const btnIncrease = node.querySelector('.btn-increase');
        
        btnDecrease.addEventListener('click', () => {
            if (input.value > 0) {
                input.value = parseInt(input.value) - 1;
                calculateTotal();
            }
        });
        
        btnIncrease.addEventListener('click', () => {
            input.value = parseInt(input.value) + 1;
            calculateTotal();
        });
        
        input.addEventListener('change', calculateTotal);
        
        container.appendChild(node);
    });
}

function calculateTotal() {
    let total = 0;
    document.querySelectorAll('.cantidad').forEach(input => {
        const cantidad = parseInt(input.value) || 0;
        const precio = parseFloat(input.dataset.precio) || 0;
        total += cantidad * precio;
    });
    document.getElementById('total-price').textContent = `$${total.toFixed(2)}`;
}

function showError(message) {
    const errorBox = document.getElementById('error-message');
    errorBox.textContent = message;
    errorBox.classList.remove('hidden');
    document.getElementById('success-message').classList.add('hidden');
}

function showSuccess(message) {
    const successBox = document.getElementById('success-message');
    successBox.textContent = message;
    successBox.classList.remove('hidden');
    document.getElementById('error-message').classList.add('hidden');
}

document.getElementById('purchase-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const token = localStorage.getItem('auth_token');
    if (!token) {
        showError('Debes iniciar sesión para comprar entradas');
        setTimeout(() => window.location.href = '/login', 2000);
        return;
    }
    
    const items = [];
    document.querySelectorAll('.cantidad').forEach(input => {
        const cantidad = parseInt(input.value) || 0;
        if (cantidad > 0) {
            items.push({
                ticket_type_id: parseInt(input.dataset.ticketId),
                cantidad: cantidad
            });
        }
    });
    
    if (items.length === 0) {
        showError('Debes seleccionar al menos una entrada');
        return;
    }
    
    const submitBtn = document.getElementById('submit-btn');
    submitBtn.disabled = true;
    
    try {
        const res = await fetch('/api/purchases', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                event_id: parseInt(eventId),
                items: items
            })
        });
        
        const data = await res.json();
        
        if (res.ok) {
            showSuccess(`¡Compra exitosa! Código: ${data.purchase.codigo_compra}`);
            setTimeout(() => window.location.href = '/my-purchases', 2000);
        } else {
            showError(data.error || data.detalle || 'Error al procesar la compra');
            submitBtn.disabled = false;
        }
    } catch (error) {
        showError('Error de conexión. Intenta nuevamente.');
        submitBtn.disabled = false;
    }
});

loadEvent();
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('events::layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\api-gestion-eventos\app-modules\events\resources\views/purchase.blade.php ENDPATH**/ ?>