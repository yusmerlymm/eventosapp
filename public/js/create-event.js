// Versión 3.1 - URLs absolutas con DOMContentLoaded
console.log('✅ Script cargado: create-event.js v3.1');

// Verificar autenticación y rol
const token = localStorage.getItem('auth_token');
const user = JSON.parse(localStorage.getItem('user') || '{}');

if (!token || user.role !== 'super_admin') {
  window.location.href = '/login';
}

// Esperar a que el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
  console.log('📄 DOM listo');

  // Agregar tipo de entrada
  const addTicketBtn = document.getElementById('add-ticket');
  if (addTicketBtn) {
    addTicketBtn.addEventListener('click', () => {
      const tpl = document.getElementById('ticket-row');
      const node = tpl.content.cloneNode(true);
      node.querySelector('[data-action="remove"]').addEventListener('click', e => e.currentTarget.parentElement.remove());
      document.getElementById('tickets').appendChild(node);
    });
  }

// Cargar opciones del formulario
async function loadOptions() {
  try {
    console.log('🔄 Cargando opciones...');
    const baseUrl = window.location.origin;
    console.log('📍 Base URL:', baseUrl);
    
    const [catsRes, venuesRes, statusesRes] = await Promise.all([
      fetch(`${baseUrl}/api/admin/categories/available`),
      fetch(`${baseUrl}/api/admin/venues/available`),
      fetch(`${baseUrl}/api/admin/statuses`),
    ]);

    console.log('📊 Respuestas:', {
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

    console.log('✅ Datos recibidos:', { cats: cats.length, venues: venues.length, statuses: statuses.length });

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
    venueSel.innerHTML = '<option value="">Selecciona...</option>' + venues.map(v => `<option value="${v.id}">${v.nombre ?? ('Venue ' + v.id)}</option>`).join('');

    const statusSel = document.getElementById('status');
    statusSel.innerHTML = '<option value="">Selecciona...</option>' + statuses.map(s => `<option value="${s.id}">${s.status}</option>`).join('');
    
    console.log('✅ Opciones cargadas correctamente');
  } catch (error) {
    console.error('❌ Error cargando opciones:', error);
    alert('Error al cargar las opciones del formulario: ' + error.message);
  }
}

loadOptions();

// Manejar envío del formulario
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
  Object.entries(normalized).forEach(([k,v]) => { fd.delete(k); if (v !== null) fd.append(k, v); });

  const hasFiles = Array.from(fd.keys()).some(k => k.startsWith('files'));
  let res;
  
  console.log('📤 Enviando a:', `${baseUrl}/api/events/store`);
  
  if (hasFiles) {
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
    console.log('✅ Evento creado exitosamente');
    window.location.href = '/events';
  } else {
    const data = await res.json().catch(async () => ({ text: await res.text().catch(() => '') }));
    const msg = data?.error || data?.detalle || data?.text || (data?.errors ? Object.entries(data.errors).map(([k,v]) => `${k}: ${v.join ? v.join(', ') : v}`).join('<br>') : 'Error desconocido');
    errorBox.innerHTML = msg;
    errorBox.classList.remove('hidden');
    submitBtn.disabled = false;
    console.error('❌ Error al crear evento:', msg);
  }
});

}); // Fin DOMContentLoaded
