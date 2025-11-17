@extends('auth::layout')

@section('title', 'Iniciar Sesión')

@section('content')
<div class="container d-flex align-items-center justify-content-center" style="min-height: 100vh;">
	<div class="row w-100 justify-content-center">
		<div class="col-12 col-md-6 col-lg-4">
			<div class="card shadow border-0">
				<div class="card-body p-4 p-sm-5">
					<h1 class="h4 fw-semibold text-center mb-3 text-primary">Iniciar Sesión</h1>
					<p class="text-muted small text-center mb-4">Accede para gestionar tus eventos y compras</p>
					<div id="error-message" class="alert alert-danger d-none small mb-3"></div>
					<form id="login-form" class="d-flex flex-column gap-3">
						<div class="mb-2">
							<label for="email" class="form-label small mb-1">Correo electrónico</label>
							<input
								type="email"
								id="email"
								name="email"
								required
								class="form-control"
								placeholder="tu@email.com"
							>
						</div>
						<div class="mb-2">
							<label for="password" class="form-label small mb-1">Contraseña</label>
							<input
								type="password"
								id="password"
								name="password"
								required
								class="form-control"
								placeholder="••••••••"
							>
						</div>
						<button type="submit" class="btn btn-primary w-100 mt-2">
							Ingresar
						</button>
					</form>
					<div class="mt-3 text-center small text-muted">
						¿No tienes cuenta? <a href="/register" class="text-primary">Regístrate aquí</a>
					</div>
					<div class="mt-2 text-center small">
						<a href="/events" class="text-decoration-none text-secondary">Volver a la página de eventos</a>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<script>
document.getElementById('login-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const errorBox = document.getElementById('error-message');
    errorBox.classList.add('d-none');
    
    const formData = new FormData(e.target);
    const data = {
        email: formData.get('email'),
        password: formData.get('password')
    };
    
    try {
        const res = await fetch('/api/auth/login', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify(data)
        });
        
        const result = await res.json();
        
        if (res.ok) {
            // Guardar token y usuario
            localStorage.setItem('auth_token', result.access_token);
            localStorage.setItem('user', JSON.stringify(result.user));
            
            // Redirigir según rol
            if (result.user.role === 'super_admin') {
                window.location.href = '/admin/dashboard';
            } else {
                window.location.href = '/events';
            }
        } else {
            errorBox.textContent = result.message || 'Error al iniciar sesión';
            errorBox.classList.remove('d-none');
        }
    } catch (error) {
        errorBox.textContent = 'Error de conexión. Intenta nuevamente.';
        errorBox.classList.remove('d-none');
    }
});
</script>
@endsection
