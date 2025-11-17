<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $__env->yieldContent('title', 'Gestión de Eventos'); ?></title>

    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
    <style>
        :root {
            --bs-primary: #0b1f4d;
            --bs-primary-rgb: 11, 31, 77;
        }
        body {
            background: #f3f6fb;
            min-height: 100vh;
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
        .navbar-brand {
            font-weight: 600;
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
    <div class="min-vh-100 d-flex flex-column">
        <?php echo $__env->yieldContent('content'); ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\api-gestion-eventos\app-modules\auth\resources\views/layout.blade.php ENDPATH**/ ?>