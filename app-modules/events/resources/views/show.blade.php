@extends('events::layout')

@section('title', 'Detalle de evento')

@section('content')
<div id="event" class="grid gap-6 max-w-3xl"></div>
<div id="event-error" class="hidden rounded-md border border-red-200 bg-red-50 text-red-700 px-3 py-2 text-sm"></div>

<template id="event-tpl">
  <h1 class="text-2xl font-semibold" data-field="nombre"></h1>
  <p class="text-gray-700" data-field="descripcion"></p>

  <div class="grid sm:grid-cols-2 gap-4">
    <div class="rounded-lg border bg-white p-4">
      <div class="text-sm text-gray-700">
        <div><strong>Inicio:</strong> <span data-field="fecha_inicio"></span></div>
        <div><strong>Fin:</strong> <span data-field="fecha_fin"></span></div>
        <div><strong>Audiencia:</strong> <span data-field="audiencia"></span></div>
        <div><strong>Capacidad:</strong> <span data-field="capacidad_max"></span></div>
        <div><strong>Estado:</strong> <span data-field="status"></span></div>
      </div>
      <div class="mt-3 text-sm text-gray-700">
        <div><strong>Venta desde:</strong> <span data-field="venta_inicio"></span></div>
        <div><strong>Venta hasta:</strong> <span data-field="venta_fin"></span></div>
      </div>
    </div>
    <div class="rounded-lg border bg-white p-4">
      <h3 class="font-medium mb-2">Tipos de entrada</h3>
      <ul id="tickets" class="grid gap-2"></ul>
    </div>
  </div>

  <div class="rounded-lg border bg-white p-4">
    <h3 class="font-medium mb-3">Imágenes</h3>
    <div id="gallery" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3"></div>
  </div>

  <div class="flex gap-2">
    <a class="rounded-md border px-3 py-2 hover:bg-gray-50" href="/events">Volver</a>
    <a class="rounded-md bg-blue-600 text-white px-3 py-2 hover:bg-blue-700" data-action="comprar">Comprar Entradas</a>
    <button class="rounded-md bg-red-600 text-white px-3 py-2 hover:bg-red-700" data-action="cancelar" style="display: none;">Cancelar</button>
    <a class="rounded-md border px-3 py-2 hover:bg-gray-50" data-action="editar" style="display: none;">Editar</a>
  </div>
</template>

<script>
  const id = window.location.pathname.split('/').filter(Boolean).pop();
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

    const tpl = document.getElementById('event-tpl');
    const container = document.getElementById('event');
    const node = tpl.content.cloneNode(true);

    function fmt(dt){ 
      if(!dt) return '-'; 
      try{ 
        const d = new Date(String(dt).replace(' ','T')); 
        const day = String(d.getDate()).padStart(2, '0');
        const month = String(d.getMonth() + 1).padStart(2, '0');
        const year = d.getFullYear();
        const hours = String(d.getHours()).padStart(2, '0');
        const minutes = String(d.getMinutes()).padStart(2, '0');
        return `${day}/${month}/${year}, ${hours}:${minutes}`;
      }catch(_){ 
        return String(dt);
      } 
    }
    
    node.querySelector('[data-field="nombre"]').textContent = ev.nombre || '';
    node.querySelector('[data-field="descripcion"]').textContent = ev.descripcion || '';
    node.querySelector('[data-field="fecha_inicio"]').textContent = fmt(ev.fecha_inicio);
    node.querySelector('[data-field="fecha_fin"]').textContent = fmt(ev.fecha_fin);
    node.querySelector('[data-field="audiencia"]').textContent = ev.audiencia || 'general';
    node.querySelector('[data-field="capacidad_max"]').textContent = ev.capacidad_max || '';
    node.querySelector('[data-field="venta_inicio"]').textContent = fmt(ev.venta_inicio);
    node.querySelector('[data-field="venta_fin"]').textContent = fmt(ev.venta_fin);
    const statusName = (ev.event_status && ev.event_status.status) || (ev.eventStatus && ev.eventStatus.status) || ev.status || '';
    node.querySelector('[data-field="status"]').textContent = statusName;

    const list = node.querySelector('#tickets');
    const tks = (ev.ticket_types || ev.ticketTypes || []);
    if (!tks.length) {
      const li = document.createElement('li');
      li.className = 'text-sm text-gray-600';
      li.textContent = 'Sin tipos de entrada';
      list.appendChild(li);
    } else {
      tks.forEach(t => {
        const li = document.createElement('li');
        li.className = 'flex items-center justify-between rounded-md border px-3 py-2';
        const precio = (t.precio !== undefined && t.precio !== null && t.precio !== '') ? Number(t.precio).toFixed(2) : '0.00';
        li.innerHTML = `<span>${t.nombre}</span><span class="font-medium">$${precio}</span>`;
        list.appendChild(li);
      });
    }

    // Configurar botones según rol del usuario
    const user = JSON.parse(localStorage.getItem('user') || '{}');
    const token = localStorage.getItem('auth_token');
    
    const comprarBtn = node.querySelector('[data-action="comprar"]');
    comprarBtn.href = `/events/${id}/purchase`;
    // Solo permitir compras cuando el evento esté publicado
    if (statusName !== 'Publicado') {
      comprarBtn.style.display = 'none';
    }
    
    // Configurar botón de editar
    const editBtn = node.querySelector('[data-action="editar"]');
    if (editBtn) {
      editBtn.href = `/events/${id}/edit`;
      if (user.role === 'super_admin') {
        editBtn.style.display = 'inline-block';
      }
    }
    
    if (user.role === 'super_admin') {
      // Mostrar botón de cancelar
      node.querySelector('[data-action="cancelar"]').style.display = 'inline-block';
    }
    
    node.querySelector('[data-action="cancelar"]').addEventListener('click', async () => {
      if (!confirm('¿Cancelar este evento?')) return;
      if (!token) {
        alert('Debes iniciar sesión');
        return;
      }
      try {
        const res = await fetch(`/api/events/${id}/cancel`, { 
          method: 'POST', 
          headers: { 
            'X-Requested-With': 'XMLHttpRequest',
            'Authorization': `Bearer ${token}`
          } 
        });
        if (res.ok) {
          load();
        } else {
          const data = await res.json().catch(async () => ({ text: await res.text().catch(() => '') }));
          alert('No se pudo cancelar: ' + (data?.error || data?.detalle || data?.text || 'Error desconocido'));
        }
      } catch (e) {
        alert('Error de red al cancelar');
      }
    });

    container.innerHTML = '';
    container.appendChild(node);

    // Render imágenes
    const gal = document.getElementById('gallery');
    gal.innerHTML = '';
    const imgs = [];
    if (ev.img_principal || ev.imgPrincipal) imgs.push(ev.img_principal || ev.imgPrincipal);
    if (Array.isArray(ev.images)) {
      ev.images.forEach(im => imgs.push(im));
    }
    const seen = new Set();
    imgs.forEach(im => {
      const url = im.url_imagen || im.url || '';
      if (!url || seen.has(url)) return;
      seen.add(url);
      const a = document.createElement('a');
      a.href = `/storage/${url}`;
      a.target = '_blank';
      const img = document.createElement('img');
      img.src = `/storage/${url}`;
      img.alt = 'Imagen del evento';
      img.className = 'w-full h-28 object-cover rounded-md border';
      a.appendChild(img);
      gal.appendChild(a);
    });
  }
  load();
</script>
@endsection
