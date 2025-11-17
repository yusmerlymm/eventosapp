<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<title>@yield('title', 'Gestión de Eventos')</title>

	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
	@vite(['resources/css/app.css', 'resources/js/app.js'])
	<style>
		:root {
			--bs-primary: #0b1f4d;
			--bs-primary-rgb: 11, 31, 77;
		}
		body {
			background: #f3f6fb;
			color: #1f2933;
			font-family: 'Poppins', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
		}
		.btn,
		.form-control,
		.card,
		.badge,
		input,
		select,
		textarea,
		button,
		a {
			border-radius: .6rem !important;
		}
		a {
			text-decoration: none;
		}
		a:hover {
			text-decoration: underline;
		}
		.navbar {
			background: #0b1f4d;
		}
		.navbar .nav-link,
		.navbar-brand,
		.navbar span {
			color: #fff !important;
		}
		.navbar .btn-outline-light {
			border-color: #fff;
			color: #fff;
		}
		.navbar .btn-outline-light:hover {
			background: #fff;
			color: #0b1f4d;
		}
		.btn-primary {
			background-color: #0b1f4d;
			border-color: #0b1f4d;
		}
		.btn-primary:hover {
			background-color: #091738;
			border-color: #091738;
		}
		.btn-outline-primary {
			color: #0b1f4d;
			border-color: #0b1f4d;
		}
		.btn-outline-primary:hover {
			background-color: #0b1f4d;
			border-color: #0b1f4d;
		}
		.text-primary {
			color: #0b1f4d !important;
		}
		.bg-primary-subtle {
			background-color: rgba(11, 31, 77, 0.08) !important;
		}
	</style>
</head>
<body>
	<header>
		<nav class="navbar navbar-expand-lg shadow-sm">
			<div class="container">
				<a href="/events" class="navbar-brand">Eventos</a>
				<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#eventsNavbar" aria-controls="eventsNavbar" aria-expanded="false" aria-label="Toggle navigation">
					<span class="navbar-toggler-icon"></span>
				</button>
				<div class="collapse navbar-collapse" id="eventsNavbar">
					<div class="me-auto d-flex align-items-center">
						<a href="/events" class="nav-link">Listado</a>
						<span id="header-greeting" class="ms-3 small opacity-75"></span>
					</div>
					<div class="d-flex align-items-center gap-2">
						<a href="/events/create" id="header-btn-crear" style="display: none;" class="btn btn-light btn-sm">Crear</a>
						<a href="/my-purchases" id="header-btn-compras" style="display: none;" class="btn btn-outline-light btn-sm">Mis Compras</a>
						<button id="header-btn-logout" style="display: none;" class="btn btn-outline-light btn-sm">Cerrar Sesión</button>
						<a href="/login" id="header-btn-login" class="btn btn-light btn-sm">Iniciar Sesión</a>
						<a href="/register" id="header-btn-register" class="btn btn-outline-light btn-sm">Registrarse</a>
					</div>
				</div>
			</div>
		</nav>
	</header>

	<main class="container py-4">
		@yield('content')
	</main>

	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
	<script>
		// Configurar header según autenticación
		(function() {
			const token = localStorage.getItem('auth_token');
			const user = JSON.parse(localStorage.getItem('user') || '{}');
			
			if (token && user.name) {
				document.getElementById('header-greeting').textContent = `Hola, ${user.name}`;
				document.getElementById('header-btn-logout').style.display = 'inline-block';
				document.getElementById('header-btn-login').style.display = 'none';
				document.getElementById('header-btn-register').style.display = 'none';
				
				if (user.role === 'super_admin') {
					document.getElementById('header-btn-crear').style.display = 'inline-block';
				} else {
					document.getElementById('header-btn-compras').style.display = 'inline-block';
				}
			}
			
			// Cerrar sesión
			const logoutBtn = document.getElementById('header-btn-logout');
			if (logoutBtn) {
				logoutBtn.addEventListener('click', () => {
					localStorage.removeItem('auth_token');
					localStorage.removeItem('user');
					window.location.href = '/events';
				});
			}
		})();
	</script>
</body>
</html>
