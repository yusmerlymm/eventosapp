<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Modules\Auth\Http\Controllers\AuthController;
use Modules\Auth\Http\Controllers\PasswordResetController;
use Modules\Auth\Http\Controllers\EmailVerificationController;

// API Routes
Route::middleware('api')->prefix('api/auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

    // Password Reset
    Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLink']);
    Route::post('/reset-password', [PasswordResetController::class, 'reset']);

    // Email Verification
    Route::post('/email/resend', [EmailVerificationController::class, 'resend'])
        ->middleware('auth:sanctum')
        ->name('verification.send');

    Route::post('/verify-consume', [EmailVerificationController::class, 'consume']);
});

// ruta para obtener el perfil de usuario autenticado
Route::middleware(['api', 'auth:sanctum'])->get('/api/user', [AuthController::class, 'profile']);

// API Admin Routes
Route::middleware(['api', 'auth:sanctum', 'role:super_admin'])->prefix('api/admin')->group(function () {
    Route::get('/users', function () {
        $users = \Modules\Auth\Models\User::with('purchases')->orderBy('created_at', 'desc')->get();
        // Calcular purchases_count manualmente para asegurar que el frontend lo reciba correctamente
        $users->each(function ($user) {
            $user->purchases_count = $user->purchases->count();
        });
        return $users;
    });
    
    Route::put('/users/{id}', function ($id) {
        $user = \Modules\Auth\Models\User::findOrFail($id);
        $validated = request()->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'role' => 'required|in:super_admin,comprador',
        ]);
        $user->update($validated);
        return response()->json(['message' => 'Usuario actualizado', 'user' => $user]);
    });
    
    Route::delete('/users/{id}', function ($id) {
        $user = \Modules\Auth\Models\User::findOrFail($id);
        $user->delete();
        return response()->json(['message' => 'Usuario eliminado']);
    });

    // Reporte de ventas por evento con filtros diario/semanal/mensual
    Route::get('/reports/sales', function (Request $request) {
        $range = $request->query('range', 'daily');

        $now = now();
        if ($range === 'weekly') {
            $from = $now->copy()->subDays(6)->startOfDay();
            $label = 'Últimos 7 días';
        } elseif ($range === 'monthly') {
            $from = $now->copy()->subDays(29)->startOfDay();
            $label = 'Últimos 30 días';
        } else {
            // daily por defecto
            $from = $now->copy()->startOfDay();
            $label = 'Hoy';
        }
        $to = $now->copy()->endOfDay();

        $rows = \Illuminate\Support\Facades\DB::table('purchases')
            ->join('events', 'purchases.event_id', '=', 'events.id')
            ->join('purchase_items', 'purchase_items.purchase_id', '=', 'purchases.id')
            ->whereBetween('purchases.created_at', [$from, $to])
            ->groupBy('events.id', 'events.nombre')
            ->select(
                'events.id as event_id',
                'events.nombre as event_name',
                \Illuminate\Support\Facades\DB::raw('SUM(purchase_items.cantidad) as total_tickets'),
                \Illuminate\Support\Facades\DB::raw('SUM(purchase_items.subtotal) as total_amount')
            )
            ->orderByDesc('total_amount')
            ->get();

        $summary = [
            'total_tickets' => (int) $rows->sum('total_tickets'),
            'total_amount' => (float) $rows->sum('total_amount'),
            'events_count' => $rows->count(),
            'label' => $label,
        ];

        return response()->json([
            'summary' => $summary,
            'events' => $rows,
        ]);
    });

    // Ruta para generar el PDF del informe de ventas
    Route::get('/reports/sales/pdf', [\Modules\Auth\Http\Controllers\ReportController::class, 'salesReportPdf']);
});

// Web Routes - Vistas
Route::middleware('web')->group(function () {
    Route::get('/login', function () { return view('auth::login'); })->name('login');
    Route::get('/register', function () { return view('auth::register'); })->name('register');
    Route::get('/admin/dashboard', function () { return view('auth::dashboard'); });
    Route::get('/admin/users', function () { return view('auth::users'); })->middleware('role:super_admin');
    Route::get('/admin/reports', function () { return view('auth::reports'); })->middleware('role:super_admin');
});


