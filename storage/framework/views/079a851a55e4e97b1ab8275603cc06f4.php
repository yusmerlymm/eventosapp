<?php $__env->startSection('title', 'Gestión de Usuarios'); ?>

<?php $__env->startSection('content'); ?>
<div class="min-h-screen bg-gray-50">
    <!-- Navbar -->
    <nav class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center gap-4">
                    <a href="/admin/dashboard" class="text-blue-600 hover:underline">&larr; Dashboard</a>
                    <h1 class="text-xl font-bold text-gray-900">Gestión de Usuarios</h1>
                </div>
                <div class="flex items-center gap-4">
                    <span class="text-sm text-gray-600" id="current-user-name"></span>
                    <button onclick="logout()" class="text-sm text-red-600 hover:text-red-700">
                        Cerrar Sesión
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b">
                <div class="flex justify-between items-center">
                    <h2 class="text-lg font-semibold">Lista de Usuarios</h2>
                    <div class="flex gap-2">
                        <select id="role-filter" class="border rounded-md px-3 py-2 text-sm">
                            <option value="">Todos los roles</option>
                            <option value="super_admin">Super Admin</option>
                            <option value="comprador">Comprador</option>
                        </select>
                        <button onclick="showCreateModal()" class="bg-blue-600 text-white px-4 py-2 rounded-md text-sm hover:bg-blue-700">
                            Crear Usuario
                        </button>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nombre</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Rol</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Registro</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Compras</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="users-table" class="divide-y divide-gray-200">
                        <!-- Users will be loaded here -->
                    </tbody>
                </table>
            </div>

            <div id="no-users" class="hidden px-6 py-12 text-center text-gray-600">
                No se encontraron usuarios
            </div>
        </div>
    </div>
</div>

<!-- Modal para crear/editar usuario -->
<div id="user-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
        <div class="px-6 py-4 border-b flex justify-between items-center">
            <h3 id="modal-title" class="text-lg font-semibold">Crear Usuario</h3>
            <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        
        <form id="user-form" class="px-6 py-4 space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nombre</label>
                <input type="text" id="user-name" required class="w-full border rounded-md px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" id="user-email" required class="w-full border rounded-md px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            
            <div id="password-field">
                <label class="block text-sm font-medium text-gray-700 mb-1">Contraseña</label>
                <input type="password" id="user-password" class="w-full border rounded-md px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Rol</label>
                <select id="user-role" required class="w-full border rounded-md px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="comprador">Comprador</option>
                    <option value="super_admin">Super Administrador</option>
                </select>
            </div>
            
            <div class="flex gap-3 pt-4">
                <button type="submit" class="flex-1 bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 focus:ring-2 focus:ring-blue-500">
                    Guardar
                </button>
                <button type="button" onclick="closeModal()" class="flex-1 border border-gray-300 text-gray-700 py-2 px-4 rounded-md hover:bg-gray-50">
                    Cancelar
                </button>
            </div>
        </form>
    </div>
</div>

<script>
let allUsers = [];

async function loadUsers() {
    const token = localStorage.getItem('auth_token');
    const user = JSON.parse(localStorage.getItem('user') || '{}');
    
    if (!token || user.role !== 'super_admin') {
        window.location.href = '/login';
        return;
    }
    
    document.getElementById('current-user-name').textContent = user.name;
    
    try {
        const res = await fetch('/api/admin/users', {
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            }
        });
        
        if (!res.ok) throw new Error('Error al cargar usuarios');
        
        allUsers = await res.json();
        renderUsers(allUsers);
    } catch (error) {
        console.error('Error:', error);
        document.getElementById('no-users').classList.remove('hidden');
    }
}

function renderUsers(users) {
    const tbody = document.getElementById('users-table');
    const noUsers = document.getElementById('no-users');
    
    if (users.length === 0) {
        tbody.innerHTML = '';
        noUsers.classList.remove('hidden');
        return;
    }
    
    noUsers.classList.add('hidden');
    tbody.innerHTML = users.map(user => `
        <tr class="hover:bg-gray-50">
            <td class="px-6 py-4 text-sm text-gray-900">${user.id}</td>
            <td class="px-6 py-4 text-sm text-gray-900">${user.name}</td>
            <td class="px-6 py-4 text-sm text-gray-600">${user.email}</td>
            <td class="px-6 py-4">
                <span class="px-2 py-1 text-xs rounded-full ${
                    user.role === 'super_admin' 
                        ? 'bg-purple-100 text-purple-800' 
                        : 'bg-blue-100 text-blue-800'
                }">
                    ${user.role === 'super_admin' ? 'Super Admin' : 'Comprador'}
                </span>
            </td>
            <td class="px-6 py-4 text-sm text-gray-600">${formatDate(user.created_at)}</td>
            <td class="px-6 py-4 text-sm text-gray-600">${user.purchases_count || 0}</td>
            <td class="px-6 py-4">
                <div class="flex gap-2">
                    <button onclick="editUser(${user.id})" class="text-blue-600 hover:text-blue-800 text-sm">
                        Editar
                    </button>
                    <button onclick="deleteUser(${user.id}, '${user.name}')" class="text-red-600 hover:text-red-800 text-sm">
                        Eliminar
                    </button>
                </div>
            </td>
        </tr>
    `).join('');
}

function formatDate(dateStr) {
    const d = new Date(dateStr);
    return d.toLocaleDateString('es-ES', { day: '2-digit', month: '2-digit', year: 'numeric' });
}

function logout() {
    localStorage.removeItem('auth_token');
    localStorage.removeItem('user');
    window.location.href = '/login';
}

// Filtro por rol
document.getElementById('role-filter').addEventListener('change', (e) => {
    const role = e.target.value;
    const filtered = role ? allUsers.filter(u => u.role === role) : allUsers;
    renderUsers(filtered);
});

// Modal functions
let currentUserId = null;

function showCreateModal() {
    currentUserId = null;
    document.getElementById('modal-title').textContent = 'Crear Usuario';
    document.getElementById('user-name').value = '';
    document.getElementById('user-email').value = '';
    document.getElementById('user-password').value = '';
    document.getElementById('user-password').required = true;
    document.getElementById('password-field').classList.remove('hidden');
    document.getElementById('user-role').value = 'comprador';
    document.getElementById('user-modal').classList.remove('hidden');
}

function closeModal() {
    document.getElementById('user-modal').classList.add('hidden');
    currentUserId = null;
}

// Manejar submit del formulario
document.getElementById('user-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const userData = {
        name: document.getElementById('user-name').value,
        email: document.getElementById('user-email').value,
        role: document.getElementById('user-role').value
    };
    
    const password = document.getElementById('user-password').value;
    if (password) {
        userData.password = password;
    }
    
    if (currentUserId) {
        await updateUser(currentUserId, userData);
    } else {
        await createUser(userData);
    }
});

async function createUser(userData) {
    const token = localStorage.getItem('auth_token');
    
    try {
        const res = await fetch('/api/auth/register', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            },
            body: JSON.stringify(userData)
        });
        
        if (res.ok) {
            alert('Usuario creado exitosamente');
            closeModal();
            loadUsers();
        } else {
            const data = await res.json().catch(() => ({}));
            const errors = data.errors || {};
            const errorMessages = Object.values(errors).flat().join('\n');
            const message = errorMessages || data.message || 'No se pudo crear el usuario';
            alert('Error al crear usuario:\n' + message);
        }
    } catch (error) {
        alert('Error de conexión');
    }
}

// Editar usuario
function editUser(userId) {
    const user = allUsers.find(u => u.id === userId);
    if (!user) return;
    
    currentUserId = userId;
    document.getElementById('modal-title').textContent = 'Editar Usuario';
    document.getElementById('user-name').value = user.name;
    document.getElementById('user-email').value = user.email;
    document.getElementById('user-password').value = '';
    document.getElementById('user-password').required = false;
    document.getElementById('password-field').classList.remove('hidden');
    document.getElementById('user-role').value = user.role;
    document.getElementById('user-modal').classList.remove('hidden');
}

async function updateUser(userId, userData) {
    const token = localStorage.getItem('auth_token');
    
    try {
        const res = await fetch(`/api/admin/users/${userId}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            },
            body: JSON.stringify(userData)
        });
        
        if (res.ok) {
            alert('Usuario actualizado exitosamente');
            closeModal();
            loadUsers();
        } else {
            const data = await res.json().catch(() => ({}));
            const errors = data.errors || {};
            const errorMessages = Object.values(errors).flat().join('\n');
            const message = errorMessages || data.message || 'Error al actualizar usuario';
            alert('Error al actualizar usuario:\n' + message);
        }
    } catch (error) {
        alert('Error de conexión');
    }
}

// Eliminar usuario
async function deleteUser(userId, userName) {
    if (!confirm(`¿Está seguro de eliminar al usuario "${userName}"?`)) return;
    
    const token = localStorage.getItem('auth_token');
    
    try {
        const res = await fetch(`/api/admin/users/${userId}`, {
            method: 'DELETE',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            }
        });
        
        if (res.ok) {
            alert('Usuario eliminado exitosamente');
            loadUsers();
        } else {
            alert('Error al eliminar usuario');
        }
    } catch (error) {
        alert('Error de conexión');
    }
}

loadUsers();
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('auth::layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\api-gestion-eventos\app-modules\auth\resources\views/users.blade.php ENDPATH**/ ?>