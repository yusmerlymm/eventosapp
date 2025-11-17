@extends('events::layout')

@section('title', 'Crear evento')

@section('content')
<div class="flex items-center justify-between mb-6">
  <h1 class="text-2xl font-semibold">Crear evento</h1>
  <a href="/admin/dashboard" class="text-sm text-blue-600 hover:underline">← Volver al Dashboard</a>
</div>

<form id="event-form" class="grid gap-6 max-w-3xl">
  <div class="grid gap-2">
    <label class="text-sm font-medium">Título</label>
    <input name="nombre" type="text" class="w-full rounded-md border px-3 py-2" required />
  </div>

  <div class="grid gap-2">
    <label class="text-sm font-medium">Descripción</label>
    <textarea name="descripcion" class="w-full rounded-md border px-3 py-2" rows="4" required></textarea>
  </div>

  <div class="grid sm:grid-cols-2 gap-4">
    <div class="grid gap-2">
      <label class="text-sm font-medium">Inicio</label>
      <input name="fecha_inicio" type="datetime-local" class="w-full rounded-md border px-3 py-2" required />
    </div>
    <div class="grid gap-2">
      <label class="text-sm font-medium">Fin</label>
      <input name="fecha_fin" type="datetime-local" class="w-full rounded-md border px-3 py-2" required />
    </div>
  </div>

  <div class="grid sm:grid-cols-2 gap-4">
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

  <div class="grid sm:grid-cols-2 gap-4">
    <div class="grid gap-2">
      <label class="text-sm font-medium">Venta - inicio</label>
      <input name="venta_inicio" type="datetime-local" class="w-full rounded-md border px-3 py-2" />
    </div>
    <div class="grid gap-2">
      <label class="text-sm font-medium">Venta - fin</label>
      <input name="venta_fin" type="datetime-local" class="w-full rounded-md border px-3 py-2" />
    </div>
  </div>

  <div class="grid sm:grid-cols-2 gap-4">
    <div class="grid gap-2">
      <label class="text-sm font-medium">Categoría</label>
      <select name="categoryId" id="categoryId" class="w-full rounded-md border px-3 py-2" required></select>
    </div>
    <div class="grid gap-2">
      <label class="text-sm font-medium">Tipo</label>
      <select name="typeId" id="typeId" class="w-full rounded-md border px-3 py-2" required></select>
    </div>
  </div>

  <div class="grid sm:grid-cols-2 gap-4">
    <div class="grid gap-2">
      <label class="text-sm font-medium">Lugar</label>
      <select name="venues_id" id="venues_id" class="w-full rounded-md border px-3 py-2" required></select>
    </div>
    <div class="grid gap-2">
      <label class="text-sm font-medium">Estado</label>
      <select name="status" id="status" class="w-full rounded-md border px-3 py-2" required></select>
    </div>
  </div>

  <div class="grid gap-2">
    <label class="text-sm font-medium">Imágenes</label>
    <input name="files[]" type="file" accept="image/*" multiple />
  </div>

  <div class="grid gap-2">
    <div class="flex items-center justify-between">
      <label class="text-sm font-medium">Tipos de entrada</label>
      <button type="button" id="add-ticket" class="text-sm rounded-md border px-2 py-1 hover:bg-gray-50">Añadir</button>
    </div>
    <div id="tickets" class="grid gap-2"></div>
  </div>

  <div id="form-errors" class="hidden rounded-md border border-red-200 bg-red-50 text-red-700 px-3 py-2 text-sm"></div>

  <div class="flex gap-3">
    <button type="submit" class="rounded-md bg-gray-900 text-white px-4 py-2 hover:bg-black">Guardar</button>
    <a href="/events" class="rounded-md border px-4 py-2 hover:bg-gray-50">Cancelar</a>
  </div>
</form>

<template id="ticket-row">
  <div class="grid grid-cols-5 gap-2 items-center">
    <input placeholder="Nombre" class="col-span-3 rounded-md border px-3 py-2" />
    <input placeholder="Precio" type="number" step="0.01" min="0" class="col-span-1 rounded-md border px-3 py-2" />
    <button type="button" class="col-span-1 rounded-md border px-3 py-2 hover:bg-gray-50" data-action="remove">Quitar</button>
  </div>
</template>

<script>
  // Verificar autenticación y rol
  const token = localStorage.getItem('auth_token');
  const user = JSON.parse(localStorage.getItem('user') || '{}');
  
  if (!token || user.role !== 'super_admin') {
    window.location.href = '/login';
  }

  document.getElementById('add-ticket').addEventListener('click', () => {
    const tpl = document.getElementById('ticket-row');
    const node = tpl.content.cloneNode(true);
    node.querySelector('[data-action="remove"]').addEventListener('click', e => e.currentTarget.parentElement.remove());
    document.getElementById('tickets').appendChild(node);
  });

  async function loadOptions() {
    try {
      console.log('Cargando opciones...');
      console.log('URL base:', window.location.origin);
      
      const baseUrl = window.location.origin;
      const [catsRes, venuesRes, statusesRes] = await Promise.all([
        fetch(`${baseUrl}/api/admin/categories/available`),
        fetch(`${baseUrl}/api/admin/venues/available`),
        fetch(`${baseUrl}/api/admin/statuses`),
      ]);

      console.log('Respuestas:', {
        cats: catsRes.status,
        venues: venuesRes.status,
        statuses: statusesRes.status
      });

      if (!catsRes.ok || !venuesRes.ok || !statusesRes.ok) {
        throw new Error(`Error en las peticiones: cats=${catsRes.status}, venues=${venuesRes.status}, statuses=${statusesRes.status}`);
      }

      const [cats, venues, statuses] = await Promise.all([
        catsRes.json(),
        venuesRes.json(),
        statusesRes.json(),
      ]);

      console.log('Datos recibidos:', { cats, venues, statuses });

      const catSel = document.getElementById('categoryId');
      catSel.innerHTML = '<option value="">Selecciona...</option>' + cats.map(c => `<option value="${c.id}">${c.nombre_categoria ?? c.nombre}</option>`).join('');

      const typeSel = document.getElementById('typeId');
      typeSel.innerHTML = '<option value="">Selecciona una categoría primero</option>';
      catSel.addEventListener('change', async () => {
        const cid = catSel.value;
        if (!cid) { typeSel.innerHTML = '<option value="">Selecciona una categoría primero</option>'; return; }
        const types = await fetch(`${baseUrl}/api/admin/events-type/by-category/${cid}`).then(r => r.json());
        typeSel.innerHTML = '<option value="">Selecciona...</option>' + types.map(t => `<option value="${t.id}">${t.nombre_tipo_evento ?? t.nombre}</option>`).join('');
      });

      const venueSel = document.getElementById('venues_id');
      venueSel.innerHTML = '<option value="">Selecciona...</option>' + venues.map(v => `<option value="${v.id}">${v.nombre ?? ('Lugar ' + v.id)}</option>`).join('');

      const statusSel = document.getElementById('status');
      statusSel.innerHTML = '<option value="">Selecciona...</option>' + statuses.map(s => `<option value="${s.id}">${s.status}</option>`).join('');
      
      console.log('Opciones cargadas correctamente');
    } catch (error) {
      console.error('Error cargando opciones:', error);
      alert('Error al cargar las opciones del formulario: ' + error.message);
    }
  }

  loadOptions();

  document.getElementById('event-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    const form = e.currentTarget;
    const submitBtn = form.querySelector('button[type="submit"]');
    submitBtn.disabled = true;
    const fd = new FormData(form);
    const baseUrl = window.location.origin;

    const tickets = Array.from(document.querySelectorAll('#tickets .grid'))
      .map(row => ({
        nombre: row.children[0].value,
        precio: row.children[1].value
      }))
      .filter(t => t.nombre);

    fd.append('ticket_types', new Blob([JSON.stringify(tickets)], { type: 'application/json' }));

    // Normalizar fechas a formato YYYY-MM-DD HH:MM:SS
    function norm(dt){ if(!dt) return null; const v=String(dt).replace('T',' '); return v.length===16? v+':00': v; }
    const normalized = {
      fecha_inicio: norm(fd.get('fecha_inicio')),
      fecha_fin: norm(fd.get('fecha_fin')),
      venta_inicio: norm(fd.get('venta_inicio')),
      venta_fin: norm(fd.get('venta_fin')),
    };
    // Reemplazar en FormData
    Object.entries(normalized).forEach(([k,v]) => { fd.delete(k); if (v !== null) fd.append(k, v); });

    // Adaptación: como el backend espera arreglo normal, si se envía JSON, podemos reenviar como application/json si no hay archivos.
    const hasFiles = Array.from(fd.keys()).some(k => k.startsWith('files'));
    let res;
    if (hasFiles) {
      // Cuando hay archivos, enviamos cada ticket en formato ticket_types[IDX][campo]
      fd.delete('ticket_types');
      tickets.forEach((t, i) => {
        fd.append(`ticket_types[${i}][nombre]`, t.nombre);
        fd.append(`ticket_types[${i}][precio]`, t.precio);
      });
      res = await fetch(`${baseUrl}/api/events/store`, { 
        method: 'POST', 
        headers: { 'Authorization': `Bearer ${token}` },
        body: fd 
      });
    } else {
      const payload = Object.fromEntries(fd.entries());
      payload.ticket_types = tickets;
      res = await fetch(`${baseUrl}/api/events/store`, {
        method: 'POST',
        headers: { 
          'Content-Type': 'application/json',
          'Authorization': `Bearer ${token}`
        },
        body: JSON.stringify(payload)
      });
    }

    const errorBox = document.getElementById('form-errors');
    if (res.ok) {
      errorBox.classList.add('hidden');
      window.location.href = '/events';
    } else {
      const data = await res.json().catch(async () => ({ text: await res.text().catch(() => '') }));
      const msg = data?.error || data?.detalle || data?.text || (data?.errors ? Object.entries(data.errors).map(([k,v]) => `${k}: ${v.join ? v.join(', ') : v}`).join('<br>') : 'Error desconocido');
      errorBox.innerHTML = msg;
      errorBox.classList.remove('hidden');
      submitBtn.disabled = false;
    }
  });
</script>
@endsection
{{-- CACHE BUSTER: v2.2 - 2025-11-10 22:15 - URLs absolutas --}}
