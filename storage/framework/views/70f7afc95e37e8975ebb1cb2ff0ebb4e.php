<?php $__env->startSection('title', 'Eventos'); ?>

<?php $__env->startSection('content'); ?>
<div class="d-flex align-items-center justify-content-between mb-4">
	<div>
		<h1 class="h3 fw-semibold mb-1">Eventos</h1>
		<div id="user-greeting" class="text-muted small"></div>
	</div>
	<div>
		<a href="/admin/dashboard" id="btn-back-dashboard" class="btn btn-outline-primary btn-sm" style="display: none;">Volver al dashboard</a>
	</div>
</div>

<div id="events-list" class="row g-3"></div>

<template id="event-card">
	<div class="col-12 col-sm-6 col-lg-4">
		<div class="card h-100 shadow-sm border-0">
			<div class="ratio ratio-16x9 bg-light rounded-top overflow-hidden">
				<img data-field="img" src="" alt="Imagen del evento" class="w-100 h-100" style="object-fit: cover; display:none;">
				<div data-field="img-placeholder" class="w-100 h-100 d-flex align-items-center justify-content-center text-muted" style="font-size: .85rem;">
					Sin imagen
				</div>
			</div>
			<div class="card-body d-flex flex-column">
				<div class="d-flex justify-content-between align-items-start mb-2">
					<h3 class="h6 mb-0" data-field="nombre"></h3>
					<span class="badge bg-light text-muted" data-field="status"></span>
				</div>
				<p class="text-muted small mb-2" data-field="descripcion"></p>
				<div class="small text-muted mb-3">
					<div><strong>Inicio:</strong> <span data-field="fecha_inicio"></span></div>
					<div><strong>Fin:</strong> <span data-field="fecha_fin"></span></div>
					<div><strong>Audiencia:</strong> <span data-field="audiencia"></span></div>
					<div><strong>Capacidad:</strong> <span data-field="capacidad_max"></span></div>
				</div>
				<div class="mt-auto d-flex gap-2">
					<a class="btn btn-outline-primary btn-sm" data-action="ver">Ver</a>
					<a class="btn btn-outline-secondary btn-sm" data-action="editar" style="display: none;">Editar</a>
					<button class="btn btn-warning btn-sm text-white" data-action="cancelar" style="display: none;">Cancelar</button>
					<button class="btn btn-danger btn-sm" data-action="eliminar" style="display: none;">Eliminar</button>
				</div>
			</div>
		</div>
	</div>
</template>

  <script>
    function formatDate(dateStr) {
      if (!dateStr) return '';
      const d = new Date(dateStr);
      const day = String(d.getDate()).padStart(2, '0');
      const month = String(d.getMonth() + 1).padStart(2, '0');
      const year = d.getFullYear();
      const hours = String(d.getHours()).padStart(2, '0');
      const minutes = String(d.getMinutes()).padStart(2, '0');
      return `${day}/${month}/${year} ${hours}:${minutes}`;
    }

    // Verificar usuario solo para mostrar saludo (los botones se manejan en el header)
    const token = localStorage.getItem('auth_token');
    const user = JSON.parse(localStorage.getItem('user') || '{}');
    if (token && user.name) {
      const greetingEl = document.getElementById('user-greeting');
      if (greetingEl) {
        greetingEl.textContent = `Hola, ${user.name}`;
      }
    }

    // Mostrar botón de volver al dashboard solo para super_admin
    if (user && user.role === 'super_admin') {
      const backBtn = document.getElementById('btn-back-dashboard');
      if (backBtn) {
        backBtn.style.display = 'inline-block';
      }
    }

    async function fetchEvents() {
      const res = await fetch('/api/events');
      const data = await res.json();
      const container = document.getElementById('events-list');
      const tpl = document.getElementById('event-card');
      container.innerHTML = '';
      data.forEach(ev => {
        const node = tpl.content.cloneNode(true);
        node.querySelector('[data-field="nombre"]').textContent = ev.nombre;
        node.querySelector('[data-field="descripcion"]').textContent = ev.descripcion ?? '';
        node.querySelector('[data-field="fecha_inicio"]').textContent = formatDate(ev.fecha_inicio);
        node.querySelector('[data-field="fecha_fin"]').textContent = formatDate(ev.fecha_fin);
        node.querySelector('[data-field="audiencia"]').textContent = ev.audiencia ?? 'general';
        node.querySelector('[data-field="capacidad_max"]').textContent = ev.capacidad_max ?? '';
        const statusBadge = node.querySelector('[data-field="status"]');
        const statusName = (ev.event_status && ev.event_status.status) || (ev.eventStatus && ev.eventStatus.status) || ev.status || '';
        statusBadge.textContent = statusName;
        // aplicar color según estado
        statusBadge.className = 'badge';
        if (statusName === 'Publicado') {
          statusBadge.classList.add('bg-success-subtle', 'text-success');
        } else if (statusName === 'Cancelado') {
          statusBadge.classList.add('bg-warning-subtle', 'text-warning');
        } else if (statusName === 'Finalizado') {
          statusBadge.classList.add('bg-secondary-subtle', 'text-secondary');
        } else if (statusName === 'Lleno' || statusName === 'Ventas cerradas') {
          statusBadge.classList.add('bg-danger-subtle', 'text-danger');
        } else {
          statusBadge.classList.add('bg-light', 'text-muted');
        }

        // Imagen principal si existe; si no, usar primera de images (si está disponible)
        const imgEl = node.querySelector('[data-field="img"]');
        const placeholder = node.querySelector('[data-field="img-placeholder"]');
        let imgUrl = '';
        if (ev.img_principal || ev.imgPrincipal) {
          let principal = ev.img_principal || ev.imgPrincipal;
          // Si viene como arreglo (relación serializada), usar el primer elemento
          if (Array.isArray(principal) && principal.length > 0) {
            principal = principal[0];
          }
          imgUrl = principal.url_imagen || principal.url || principal.path || '';
        } else if (Array.isArray(ev.images) && ev.images.length > 0) {
          const firstImg = ev.images[0];
          imgUrl = firstImg.url_imagen || firstImg.url || firstImg.path || '';
        }
        if (imgUrl) {
          imgEl.src = `/storage/${imgUrl}`;
          imgEl.style.display = 'block';

          // Ocultar completamente placeholder cuando haya imagen
          placeholder.style.display = 'none';
          placeholder.classList.add('d-none');
          placeholder.classList.remove('d-flex');
          placeholder.textContent = '';
        } else {
          imgEl.style.display = 'none';

          // Mostrar placeholder solo cuando no haya imagen
          placeholder.style.display = 'flex';
          placeholder.classList.remove('d-none');
          placeholder.classList.add('d-flex');
          if (!placeholder.textContent.trim()) {
            placeholder.textContent = 'Sin imagen';
          }
        }

        node.querySelector('[data-action="ver"]').href = `/events/${ev.id}`;
        node.querySelector('[data-action="editar"]').href = `/events/${ev.id}/edit`;
        
        // Mostrar botones de admin solo si es super_admin
        if (user.role === 'super_admin') {
          node.querySelector('[data-action="editar"]').style.display = 'inline-block';
          node.querySelector('[data-action="cancelar"]').style.display = 'inline-block';
          node.querySelector('[data-action="eliminar"]').style.display = 'inline-block';
        }
        node.querySelector('[data-action="cancelar"]').addEventListener('click', async () => {
          try {
            if (!confirm('¿Cancelar este evento?')) return;
            const token = localStorage.getItem('auth_token');
            if (!token) {
              alert('Debes iniciar sesión');
              return;
            }
            const res = await fetch(`/api/events/${ev.id}/cancel`, { 
              method: 'POST', 
              headers: { 
                'X-Requested-With': 'XMLHttpRequest',
                'Authorization': `Bearer ${token}`
              } 
            });
            if (res.ok) {
              fetchEvents();
            } else {
              const data = await res.json().catch(() => ({}));
              alert('No se pudo cancelar: ' + (data?.error || data?.detalle || 'Error desconocido'));
            }
          } catch (e) {
            alert('Error de red al cancelar');
          }
        });

        node.querySelector('[data-action="eliminar"]').addEventListener('click', async () => {
          try {
            if (!confirm('¿Está seguro de eliminar este evento? Esta acción no se puede deshacer.')) return;
            const token = localStorage.getItem('auth_token');
            if (!token) {
              alert('Debes iniciar sesión');
              return;
            }
            const res = await fetch(`/api/events/${ev.id}`, { 
              method: 'DELETE', 
              headers: { 
                'X-Requested-With': 'XMLHttpRequest',
                'Authorization': `Bearer ${token}`
              } 
            });
            if (res.ok) {
              fetchEvents();
            } else {
              const data = await res.json().catch(() => ({}));
              alert('No se pudo eliminar: ' + (data?.error || data?.detalle || 'Error desconocido'));
            }
          } catch (e) {
            alert('Error de red al eliminar');
          }
        });

        container.appendChild(node);
      });
    }
    fetchEvents();
  </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('events::layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\api-gestion-eventos\app-modules\events\resources\views/index.blade.php ENDPATH**/ ?>