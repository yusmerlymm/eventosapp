@extends('events::layout')

@section('title', 'Mis Compras')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-8">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-semibold">Mis Compras</h1>
        <a href="/events" class="text-blue-600 hover:underline">Ver eventos</a>
    </div>

    <div id="purchases-list" class="space-y-4"></div>
    <div id="no-purchases" class="hidden text-center py-12 text-gray-600">
        <p class="text-lg mb-2">No tienes compras realizadas</p>
        <a href="/events" class="text-blue-600 hover:underline">Explorar eventos</a>
    </div>
</div>

<template id="purchase-card">
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex justify-between items-start mb-4">
            <div>
                <h3 class="text-lg font-semibold" data-field="event-name"></h3>
                <p class="text-sm text-gray-600" data-field="codigo"></p>
                <p class="text-sm text-gray-600" data-field="fecha"></p>
            </div>
            <span class="px-3 py-1 rounded-full text-sm font-medium" data-field="estado"></span>
        </div>

        <div class="border-t pt-4">
            <h4 class="font-medium mb-2">Entradas:</h4>
            <div data-field="items" class="space-y-2 mb-4"></div>
            
            <div class="flex justify-between items-center pt-4 border-t">
                <span class="font-semibold">Total:</span>
                <span class="text-xl font-bold text-blue-600" data-field="total"></span>
            </div>
        </div>
    </div>
</template>

<script>
async function loadPurchases() {
    const token = localStorage.getItem('auth_token');
    if (!token) {
        window.location.href = '/login';
        return;
    }

    try {
        const res = await fetch('/api/purchases', {
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            }
        });

        if (!res.ok) {
            if (res.status === 401) {
                window.location.href = '/login';
                return;
            }
            throw new Error('Error al cargar compras');
        }

        const purchases = await res.json();
        renderPurchases(purchases);
    } catch (error) {
        console.error('Error:', error);
    }
}

function renderPurchases(purchases) {
    const container = document.getElementById('purchases-list');
    const noPurchases = document.getElementById('no-purchases');
    const template = document.getElementById('purchase-card');

    if (purchases.length === 0) {
        container.classList.add('hidden');
        noPurchases.classList.remove('hidden');
        return;
    }

    container.innerHTML = '';
    purchases.forEach(purchase => {
        const node = template.content.cloneNode(true);

        node.querySelector('[data-field="event-name"]').textContent = purchase.event?.nombre || 'Evento';
        node.querySelector('[data-field="codigo"]').textContent = `Código: ${purchase.codigo_compra}`;
        node.querySelector('[data-field="fecha"]').textContent = `Fecha: ${formatDate(purchase.created_at)}`;
        node.querySelector('[data-field="total"]').textContent = `$${parseFloat(purchase.total).toFixed(2)}`;

        const estadoBadge = node.querySelector('[data-field="estado"]');
        estadoBadge.textContent = purchase.estado.charAt(0).toUpperCase() + purchase.estado.slice(1);
        const estadoClass =
            purchase.estado === 'completado' ? 'bg-green-100 text-green-800' :
            purchase.estado === 'pendiente' ? 'bg-yellow-100 text-yellow-800' :
            'bg-red-100 text-red-800';
        estadoClass.split(' ').forEach(cls => estadoBadge.classList.add(cls));

        const itemsContainer = node.querySelector('[data-field="items"]');
        purchase.items.forEach(item => {
            const itemDiv = document.createElement('div');
            itemDiv.className = 'flex justify-between text-sm';
            itemDiv.innerHTML = `
                <span>${item.cantidad}x ${item.ticket_type?.nombre || 'Entrada'}</span>
                <span>$${parseFloat(item.subtotal).toFixed(2)}</span>
            `;
            itemsContainer.appendChild(itemDiv);
        });

        container.appendChild(node);
    });
}

function formatDate(dateStr) {
    const d = new Date(dateStr);
    return d.toLocaleDateString('es-ES', { 
        day: '2-digit', 
        month: '2-digit', 
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}

loadPurchases();
</script>
@endsection
