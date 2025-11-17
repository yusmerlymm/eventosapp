@extends('events::layout')

@section('title', 'Editar evento')

@section('content')
<div class="flex items-center justify-between mb-6">
  <h1 class="text-2xl font-semibold">Editar evento</h1>
  <a href="/admin/dashboard" class="text-sm text-blue-600 hover:underline">← Volver al Dashboard</a>
</div>

<form id="event-form" class="grid gap-6 max-w-3xl"></form>
<div id="event-error" class="hidden rounded-md border border-red-200 bg-red-50 text-red-700 px-3 py-2 text-sm"></div>

<template id="form-template">
  <div>
    <div class="grid gap-2 mb-4">
      <label class="text-sm font-medium">Título</label>
      <input name="nombre" type="text" class="w-full rounded-md border px-3 py-2" required />
    </div>

    <div class="grid gap-2 mb-4">
      <label class="text-sm font-medium">Descripción</label>
      <textarea name="descripcion" class="w-full rounded-md border px-3 py-2" rows="4" required></textarea>
    </div>

    <div class="grid sm:grid-cols-2 gap-4 mb-4">
      <div class="grid gap-2">
        <label class="text-sm font-medium">Inicio</label>
        <input name="fecha_inicio" type="datetime-local" class="w-full rounded-md border px-3 py-2" required />
      </div>
      <div class="grid gap-2">
        <label class="text-sm font-medium">Fin</label>
        <input name="fecha_fin" type="datetime-local" class="w-full rounded-md border px-3 py-2" required />
      </div>
    </div>

    <div class="grid sm:grid-cols-2 gap-4 mb-4">
      <div class="grid gap-2">
        <label class="text-sm font-medium">Audiencia</label>
        <select name="audiencia" class="w-full rounded-md border px-3 py-2">
          <option value="general">General</option>
          <option value="estudiantes">Estudiantes</option>
          <option value="profesores">Profesores</option>
          <option value="jubilados">Jubilados</option>
        </select>
      </div>
      <div class="grid gap-2">
        <label class="text-sm font-medium">Capacidad</label>
        <input name="capacidad_max" type="number" min="1" class="w-full rounded-md border px-3 py-2" required />
      </div>
    </div>

    <div class="grid sm:grid-cols-2 gap-4 mb-4">
      <div class="grid gap-2">
        <label class="text-sm font-medium">Venta - inicio</label>
        <input name="venta_inicio" type="datetime-local" class="w-full rounded-md border px-3 py-2" />
      </div>
      <div class="grid gap-2">
        <label class="text-sm font-medium">Venta - fin</label>
        <input name="venta_fin" type="datetime-local" class="w-full rounded-md border px-3 py-2" />
      </div>
    </div>

    <div class="grid sm:grid-cols-2 gap-4 mb-4">
      <div class="grid gap-2">
        <label class="text-sm font-medium">Categoría</label>
        <select name="id_categoria_evento" id="categoryId" class="w-full rounded-md border px-3 py-2" required></select>
      </div>
      <div class="grid gap-2">
        <label class="text-sm font-medium">Tipo</label>
        <select name="id_tipo_evento" id="typeId" class="w-full rounded-md border px-3 py-2" required></select>
      </div>
    </div>

    <div class="grid sm:grid-cols-2 gap-4 mb-4">
      <div class="grid gap-2">
        <label class="text-sm font-medium">Lugar</label>
        <select name="venues_id" id="venues_id" class="w-full rounded-md border px-3 py-2" required></select>
      </div>
      <div class="grid gap-2">
        <label class="text-sm font-medium">Estado</label>
        <select name="status" id="status" class="w-full rounded-md border px-3 py-2" required></select>
      </div>
    </div>

    <div class="grid gap-2 mb-4">
      <div class="flex items-center justify-between">
        <label class="text-sm font-medium">Tipos de entrada</label>
        <button type="button" id="add-ticket" class="text-sm rounded-md border px-2 py-1 hover:bg-gray-50">Añadir</button>
      </div>
      <div id="tickets" class="grid gap-2"></div>
    </div>

    <div id="form-errors" class="hidden rounded-md border border-red-200 bg-red-50 text-red-700 px-3 py-2 text-sm mb-3"></div>

    <div class="flex gap-3">
      <button type="submit" class="rounded-md bg-gray-900 text-white px-4 py-2 hover:bg-black">Guardar</button>
      <a href="/events" class="rounded-md border px-4 py-2 hover:bg-gray-50">Cancelar</a>
    </div>
  </div>
</template>

<template id="ticket-row">
  <div class="grid grid-cols-5 gap-2 items-center">
    <input placeholder="Nombre" class="col-span-3 rounded-md border px-3 py-2" />
    <input placeholder="Precio" type="number" step="0.01" min="0" class="col-span-1 rounded-md border px-3 py-2" />
    <button type="button" class="col-span-1 rounded-md border px-3 py-2 hover:bg-gray-50" data-action="remove">Quitar</button>
  </div>
</template>

<script>
  // Path: /events/{id}/edit -> extrae el penúltimo segmento
  const parts = window.location.pathname.split('/').filter(Boolean);
  const id = parts[parts.length - 2];

  function toLocal(dt) {
    if (!dt) return '';
    const d = new Date(dt.replace(' ', 'T'));
    const off = d.getTimezoneOffset();
    const local = new Date(d.getTime() - off*60000);
    return local.toISOString().slice(0,16);
  }

  // Verificar autenticación y rol
  const token = localStorage.getItem('auth_token');
  const user = JSON.parse(localStorage.getItem('user') || '{}');
  
  if (!token || user.role !== 'super_admin') {
    window.location.href = '/login';
  }

  async function load() {
    const err = document.getElementById('event-error');
    let ev;
    try {
      const res = await fetch(`/api/events/${id}`);
      if (!res.ok) throw new Error('No se pudo cargar el evento');
      ev = await res.json();
      err.classList.add('hidden');
    } catch (e) {
      err.textContent = e.message;
      err.classList.remove('hidden');
      return;
    }
    const tpl = document.getElementById('form-template');
    const container = document.getElementById('event-form');
    container.appendChild(tpl.content.cloneNode(true));

    const f = container;
    f.querySelector('[name="nombre"]').value = ev.nombre || '';
    f.querySelector('[name="descripcion"]').value = ev.descripcion || '';
    f.querySelector('[name="fecha_inicio"]').value = toLocal(ev.fecha_inicio);
    f.querySelector('[name="fecha_fin"]').value = toLocal(ev.fecha_fin);
    f.querySelector('[name="audiencia"]').value = ev.audiencia || 'general';
    f.querySelector('[name="capacidad_max"]').value = ev.capacidad_max || 1;
    f.querySelector('[name="venta_inicio"]').value = toLocal(ev.venta_inicio);
    f.querySelector('[name="venta_fin"]').value = toLocal(ev.venta_fin);
    // Load selects
    let cats = [], venues = [], statuses = [];
    try {
      [cats, venues, statuses] = await Promise.all([
        fetch('/api/admin/categories/available').then(r => r.json()),
        fetch('/api/admin/venues/available').then(r => r.json()),
        fetch('/api/admin/statuses').then(r => r.json()),
      ]);
    } catch (_) {
      // Si falla, dejamos selects vacíos pero no rompemos la UI
    }

    const catSel = document.getElementById('categoryId');
    catSel.innerHTML = '<option value="">Selecciona...</option>' + cats.map(c => `<option value="${c.id}">${c.nombre_categoria ?? c.nombre}</option>`).join('');
    catSel.value = ev.id_categoria_evento || '';

    const typeSel = document.getElementById('typeId');
    async function loadTypesForCategory(cid, selected) {
      if (!cid) { typeSel.innerHTML = '<option value="">Selecciona una categoría primero</option>'; return; }
      const types = await fetch(`/api/admin/events-type/by-category/${cid}`).then(r => r.json());
      typeSel.innerHTML = '<option value="">Selecciona...</option>' + types.map(t => `<option value="${t.id}">${t.nombre_tipo_evento ?? t.nombre}</option>`).join('');
      typeSel.value = selected || '';
    }
    await loadTypesForCategory(catSel.value, ev.id_tipo_evento || '');
    catSel.addEventListener('change', () => loadTypesForCategory(catSel.value, ''));

    const venueSel = document.getElementById('venues_id');
    venueSel.innerHTML = '<option value="">Selecciona...</option>' + venues.map(v => `<option value="${v.id}">${v.nombre ?? ('Lugar ' + v.id)}</option>`).join('');
    venueSel.value = ev.venues_id || '';

    const statusSel = document.getElementById('status');
    statusSel.innerHTML = '<option value="">Selecciona...</option>' + statuses.map(s => `<option value="${s.id}">${s.status}</option>`).join('');
    statusSel.value = ev.status || '';

    const ticketsC = document.getElementById('tickets');
    const rowTpl = document.getElementById('ticket-row');
    (ev.ticket_types || ev.ticketTypes || []).forEach(t => {
      const n = rowTpl.content.cloneNode(true);
      n.querySelector('input[placeholder="Nombre"]').value = t.nombre;
      n.querySelector('input[placeholder="Precio"]').value = t.precio;
      n.querySelector('[data-action="remove"]').addEventListener('click', e => e.currentTarget.parentElement.remove());
      ticketsC.appendChild(n);
    });

    document.getElementById('add-ticket').addEventListener('click', () => {
      const n = rowTpl.content.cloneNode(true);
      n.querySelector('[data-action="remove"]').addEventListener('click', e => e.currentTarget.parentElement.remove());
      ticketsC.appendChild(n);
    });

    f.addEventListener('submit', async (e) => {
      e.preventDefault();
      const fd = new FormData(f);
      const payload = Object.fromEntries(fd.entries());
      // Map keys to API expectations
      payload.categoryId = Number(payload.id_categoria_evento);
      payload.typeId = Number(payload.id_tipo_evento);
      delete payload.id_categoria_evento;
      delete payload.id_tipo_evento;

      // Normalizar fechas a formato YYYY-MM-DD HH:MM:SS
      function norm(dt){ if(!dt) return null; const v=String(dt).replace('T',' '); return v.length===16? v+':00': v; }
      payload.fecha_inicio = norm(payload.fecha_inicio);
      payload.fecha_fin = norm(payload.fecha_fin);
      payload.venta_inicio = norm(payload.venta_inicio);
      payload.venta_fin = norm(payload.venta_fin);

      const tickets = Array.from(document.querySelectorAll('#tickets .grid'))
        .map(row => ({ nombre: row.children[0].value, precio: row.children[1].value }))
        .filter(t => t.nombre);
      payload.ticket_types = tickets;

      const res = await fetch(`/api/events/${id}`, {
        method: 'PUT',
        headers: { 
          'Content-Type': 'application/json',
          'Authorization': `Bearer ${token}`
        },
        body: JSON.stringify(payload)
      });
      const errorBox = document.getElementById('form-errors');
      if (res.ok) {
        errorBox.classList.add('hidden');
        window.location.href = `/events/${id}`;
      } else {
        const data = await res.json().catch(() => ({}));
        const msg = data?.error || (data?.errors ? Object.entries(data.errors).map(([k,v]) => `${k}: ${v.join ? v.join(', ') : v}`).join('<br>') : 'Error desconocido');
        errorBox.innerHTML = msg;
        errorBox.classList.remove('hidden');
      }
    });
  }

  load();
</script>
@endsection
