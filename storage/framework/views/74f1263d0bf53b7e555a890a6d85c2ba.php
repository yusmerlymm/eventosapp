<?php $__env->startSection('title', 'Registro'); ?>

<?php $__env->startSection('content'); ?>
<div class="flex items-center justify-center min-h-screen px-4">
    <div class="w-full max-w-md">
        <div class="bg-white rounded-lg shadow-md p-8">
            <h1 class="text-2xl font-bold text-center mb-6">Crear Cuenta</h1>
            
            <div id="error-message" class="hidden rounded-md bg-red-50 border border-red-200 text-red-700 px-4 py-3 mb-4 text-sm"></div>
            <div id="success-message" class="hidden rounded-md bg-green-50 border border-green-200 text-green-700 px-4 py-3 mb-4 text-sm"></div>
            
            <form id="register-form" class="space-y-4">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nombre completo</label>
                    <input 
                        type="text" 
                        id="name" 
                        name="name" 
                        required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Juan Pérez"
                    >
                </div>
                
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Correo electrónico</label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="tu@email.com"
                    >
                </div>
                
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Contraseña</label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        required
                        minlength="6"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Mínimo 6 caracteres"
                    >
                </div>
                
                <button 
                    type="submit" 
                    class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                >
                    Registrarse
                </button>
            </form>
            
            <div class="mt-4 text-center text-sm text-gray-600">
                ¿Ya tienes cuenta? <a href="/login" class="text-blue-600 hover:underline">Inicia sesión aquí</a>
            </div>
            <div class="mt-2 text-center text-sm text-gray-600">
                <a href="/events" class="text-gray-500 hover:underline">Volver a la página de eventos</a>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('register-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const errorBox = document.getElementById('error-message');
    const successBox = document.getElementById('success-message');
    errorBox.classList.add('hidden');
    successBox.classList.add('hidden');
    
    const formData = new FormData(e.target);
    const data = {
        name: formData.get('name'),
        email: formData.get('email'),
        password: formData.get('password')
    };
    
    try {
        const res = await fetch('/api/auth/register', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify(data)
        });
        
        const result = await res.json();
        
        if (res.ok) {
            successBox.textContent = 'Cuenta creada exitosamente. Redirigiendo...';
            successBox.classList.remove('hidden');
            
            // Guardar token y usuario
            localStorage.setItem('auth_token', result.access_token);
            localStorage.setItem('user', JSON.stringify(result.user));
            
            // Redirigir a eventos después de 2 segundos
            setTimeout(() => {
                window.location.href = '/events';
            }, 2000);
        } else {
            const errors = result.errors || {};
            const errorMessages = Object.values(errors).flat().join(', ') || result.message || 'Error al registrar';
            errorBox.textContent = errorMessages;
            errorBox.classList.remove('hidden');
        }
    } catch (error) {
        errorBox.textContent = 'Error de conexión. Intenta nuevamente.';
        errorBox.classList.remove('hidden');
    }
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('auth::layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\api-gestion-eventos\app-modules\auth\resources\views/register.blade.php ENDPATH**/ ?>