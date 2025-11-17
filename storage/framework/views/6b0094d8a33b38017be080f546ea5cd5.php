<?php $__env->startSection('title', 'Dashboard - Admin'); ?>

<?php $__env->startSection('content'); ?>
<div class="min-vh-100 bg-light">
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="/admin/dashboard">Panel de Administración</a>
            <div class="ms-auto d-flex align-items-center gap-3">
                <span class="small text-muted" id="user-name"></span>
                <button onclick="logout()" class="btn btn-outline-danger btn-sm">Cerrar Sesión</button>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container py-4">
        <div class="row g-3 mb-4">
            <!-- Card: Total Eventos -->
            <div class="col-12 col-md-6 col-lg-3">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted small mb-1">Total Eventos</p>
                            <p class="h4 mb-0" id="total-events">0</p>
                        </div>
                        <div class="bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                            <i class="bi bi-calendar-event"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Card: Eventos Activos -->
            <div class="col-12 col-md-6 col-lg-3">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted small mb-1">Eventos Activos</p>
                            <p class="h4 mb-0 text-success" id="active-events">0</p>
                        </div>
                        <div class="bg-success-subtle text-success rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                            <i class="bi bi-check2-circle"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Card: Total Usuarios -->
            <div class="col-12 col-md-6 col-lg-3">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted small mb-1">Total Usuarios</p>
                            <p class="h4 mb-0 text-primary" id="total-users">0</p>
                        </div>
                        <div class="bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                            <i class="bi bi-people"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Card: Eventos Cancelados -->
            <div class="col-12 col-md-6 col-lg-3">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted small mb-1">Cancelados</p>
                            <p class="h4 mb-0 text-danger" id="cancelled-events">0</p>
                        </div>
                        <div class="bg-danger-subtle text-danger rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                            <i class="bi bi-x-circle"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Quick Actions -->
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h2 class="h6 fw-semibold mb-3">Acciones Rápidas</h2>
                <div class="row g-3">
                    <div class="col-12 col-md-4">
                        <a href="/events/create" class="d-flex align-items-center gap-3 p-3 text-decoration-none border rounded-3 bg-white h-100">
                            <div class="bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                                <i class="bi bi-plus-lg"></i>
                            </div>
                            <div>
                                <p class="fw-semibold mb-0">Crear Evento</p>
                                <p class="text-muted small mb-0">Nuevo evento</p>
                            </div>
                        </a>
                    </div>
                    <div class="col-12 col-md-4">
                        <a href="/events" class="d-flex align-items-center gap-3 p-3 text-decoration-none border rounded-3 bg-white h-100">
                            <div class="bg-success-subtle text-success rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                                <i class="bi bi-list-ul"></i>
                            </div>
                            <div>
                                <p class="fw-semibold mb-0">Ver Eventos</p>
                                <p class="text-muted small mb-0">Gestionar eventos</p>
                            </div>
                        </a>
                    </div>
                    <div class="col-12 col-md-4">
                        <a href="/admin/users" class="d-flex align-items-center gap-3 p-3 text-decoration-none border rounded-3 bg-white h-100">
                            <div class="bg-info-subtle text-info rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                                <i class="bi bi-people"></i>
                            </div>
                            <div>
                                <p class="fw-semibold mb-0">Ver Usuarios</p>
                                <p class="text-muted small mb-0">Gestionar usuarios</p>
                            </div>
                        </a>
                    </div>
                    <div class="col-12 col-md-4">
                        <a href="/admin/reports" class="d-flex align-items-center gap-3 p-3 text-decoration-none border rounded-3 bg-white h-100">
                            <div class="bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                                <i class="bi bi-graph-up"></i>
                            </div>
                            <div>
                                <p class="fw-semibold mb-0">Reportes</p>
                                <p class="text-muted small mb-0">Ventas por evento</p>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
async function loadDashboardData() {
    const token = localStorage.getItem('auth_token');
    const user = JSON.parse(localStorage.getItem('user') || '{}');
    
    if (!token || user.role !== 'super_admin') {
        window.location.href = '/login';
        return;
    }
    
    document.getElementById('user-name').textContent = user.name;
    
    try {
        // Cargar eventos
        const eventsRes = await fetch('/api/events');
        const events = await eventsRes.json();
        
        document.getElementById('total-events').textContent = events.length;
        
        const activeEvents = events.filter(e => e.event_status?.status === 'Publicado' || e.status === 2);
        document.getElementById('active-events').textContent = activeEvents.length;
        
        const cancelledEvents = events.filter(e => e.event_status?.status === 'Cancelado');
        document.getElementById('cancelled-events').textContent = cancelledEvents.length;
        
        // Cargar usuarios
        const usersRes = await fetch('/api/admin/users', {
            headers: { 'Authorization': `Bearer ${token}` }
        });
        const users = await usersRes.json();
        document.getElementById('total-users').textContent = users.length;
        
    } catch (error) {
        console.error('Error loading dashboard data:', error);
    }
}

function logout() {
    localStorage.removeItem('auth_token');
    localStorage.removeItem('user');
    window.location.href = '/login';
}

loadDashboardData();
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('auth::layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\api-gestion-eventos\app-modules\auth\resources\views/dashboard.blade.php ENDPATH**/ ?>