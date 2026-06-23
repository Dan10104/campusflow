<?php

use App\Http\Controllers\BlockchainController;
use App\Http\Controllers\CursoController;
use App\Http\Controllers\Estudiante_CursoController;
use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ActivoController;
use App\Http\Controllers\AulaController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReservaController;
use App\Http\Controllers\PrestamoController;
use App\Http\Controllers\AdminReservaController;
use App\Http\Controllers\LegalPolicyController;

require __DIR__.'/auth.php';

Route::get('/', function () {
    return Inertia::render('Cursos', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
})->name('welcome');

Route::get('/terminos-y-condiciones', [LegalPolicyController::class, 'terms'])
    ->name('policies.terms');

Route::get('/politica-de-privacidad', [LegalPolicyController::class, 'privacy'])
    ->name('policies.privacy');

Route::middleware('auth')->group(function () {
    Route::get('/politicas/aceptar', [LegalPolicyController::class, 'accept'])
        ->name('policies.accept');

    Route::post('/politicas/aceptar', [LegalPolicyController::class, 'store'])
        ->name('policies.accept.store');
});

// Ruta para enviar correos de contacto
Route::post('/contact', [App\Http\Controllers\ContactController::class, 'send'])->name('contact.send');

// Rutas web que devuelven vistas Inertia
Route::get('/roles', [RoleController::class, 'index'])->name('roles');


Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/reporte/{tipo}', [DashboardController::class, 'exportar'])->name('reporte.exportar');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');



});

Route::get('/funcionalidades/search', function (Illuminate\Http\Request $request) {
    $search = $request->query('q');

    return \App\Models\Permiso::where('nombre', 'like', "%$search%")->get();
})->name('funcionalidades.search');

Route::middleware(['auth', 'verified'])
    ->group(function () {
        Route::get('roles/{role}/functionalities', [RoleController::class, 'editFunctionalities'])->name('roles.functionalities.edit');
        Route::put('roles/{role}/functionalities', [RoleController::class, 'updateFunctionalities'])->name('roles.functionalities.update');
        Route::resource('roles', RoleController::class);
    });

    Route::middleware(['auth','verified'])->group(function(){
        Route::get('users/export', [UserController::class, 'export'])
            ->name('users.export');
        Route::get('/users/report', [UserController::class, 'report'])->name('users.report');        
        Route::resource('users', UserController::class);

    });

    Route::middleware(['auth'])->group(function () {
        // Historial blockchain de reservas
        Route::get('/blockchain/reserva/{reservaId}', [BlockchainController::class, 'showReservaHistory'])
            ->name('blockchain.reserva.history');
        
        // Historial blockchain de activos
        Route::get('/blockchain/activo/{activoCodigo}', [BlockchainController::class, 'showActivoHistory'])
            ->name('blockchain.activo.history');
        
        // Verificar transacción (API endpoint)
        Route::post('/api/blockchain/verify', [BlockchainController::class, 'verificarTransaccion'])
            ->name('blockchain.verify');

        // [NUEVO] IoT Dashboard
        Route::get('/iot/dashboard', [App\Http\Controllers\IoTController::class, 'index'])
            ->name('iot.dashboard');
    });

    Route::middleware(['auth', 'verified'])->group(function () {
        
        // ============================================
        // REPORTES
        // ============================================
        // Route::get('/users/report', [UserController::class, 'report'])->name('users.report'); // Moved to fix conflict
        Route::get('/activos/report', [ActivoController::class, 'report'])->name('activos.report');
        Route::get('/aulas/report', [AulaController::class, 'report'])->name('aulas.report');
        Route::get('/aulas/{codigo}/calendario/report', [AulaController::class, 'reportCalendario'])->name('aulas.calendario.report');
    
        // ============================================
        // ACTIVOS DISPONIBLES
        // ============================================
        
        Route::prefix('activos')->name('activos.')->group(function () {
            // Lista de activos disponibles
            Route::get('/disponibles', [ActivoController::class, 'disponibles'])
                ->name('disponibles');
            Route::get('/crear', [ActivoController::class, 'create'])->name('create');
            Route::post('/', [ActivoController::class, 'store'])->name('store');
            // Ver detalles de un activo
            Route::get('/{codigo}', [ActivoController::class, 'show'])
                ->name('show');
        });
        
        // ============================================
        // AULAS DISPONIBLES
        // ============================================
        
        Route::prefix('aulas')->name('aulas.')->group(function () {
            // Lista de aulas disponibles
            Route::get('/disponibles', [AulaController::class, 'disponibles'])
                ->name('disponibles');
            
            // Ver calendario de disponibilidad de un aula
            Route::get('/{codigo}/calendario', [AulaController::class, 'calendario'])
                ->name('calendario');
        });
        
        // ============================================
        // RESERVAS (Mis Reservas y Check-in)
        // ============================================
        
        Route::prefix('reservas')->name('reservas.')->group(function () {
            // Mis reservas (para check-in/check-out)
            Route::get('/', [ReservaController::class, 'index'])
                ->name('index');
            
            // Escanear QR (Vista)
            Route::get('/escanear', [ReservaController::class, 'escanear'])
                ->name('escanear');
            
            // Crear nueva reserva (formulario)
            Route::get('/crear', [ReservaController::class, 'crear'])
                ->name('crear');
            
            // Almacenar reserva
            Route::post('/', [ReservaController::class, 'store'])
                ->name('store');
            
            // Ver detalles de reserva
            Route::get('/{id}', [ReservaController::class, 'show'])
                ->name('show');
            
            // Check-in de reserva
            Route::post('/{id}/checkin', [ReservaController::class, 'checkin'])
                ->name('checkin');
            
            // Check-out de reserva
            Route::post('/{id}/checkout', [ReservaController::class, 'checkout'])
                ->name('checkout');
            
            // Cancelar reserva
            Route::delete('/{id}', [ReservaController::class, 'destroy'])
                ->name('destroy');


        });

        // ============================================
        // REPORTES
        // ============================================

        
        // ============================================
        // ADMIN: CENTRO DE APROBACIONES
        // ============================================
        Route::prefix('admin')->name('admin.')->group(function() {
            Route::get('/aprobaciones', [App\Http\Controllers\AdminController::class, 'index'])
                ->name('aprobaciones');

            Route::get('/reservas', [AdminReservaController::class, 'index'])
                ->name('reservas.index');

            Route::get('/reservas/{id}', [AdminReservaController::class, 'show'])
                ->name('reservas.show');
            
            Route::post('/reservas/{id}/aprobar', [App\Http\Controllers\AdminController::class, 'aprobarReserva'])
                ->name('reservas.aprobar');
                
            Route::post('/reservas/{id}/rechazar', [App\Http\Controllers\AdminController::class, 'rechazarReserva'])
                ->name('reservas.rechazar');
        });
        
        // ============================================
        
        // ============================================
        // PRÉSTAMOS DE ACTIVOS
        // ============================================
        
        Route::prefix('prestamos')->name('prestamos.')->group(function () {
            // Mis préstamos
            Route::get('/', [PrestamoController::class, 'index'])
                ->name('index');
            
            // Solicitar préstamo (formulario)
            Route::get('/crear', [PrestamoController::class, 'crear'])
                ->name('crear');
            
            // Almacenar solicitud de préstamo
            Route::post('/', [PrestamoController::class, 'store'])
                ->name('store');
            
            // Ver detalles de préstamo
            Route::get('/{id}', [PrestamoController::class, 'show'])
                ->name('show');
                
            // [NUEVO] Registrar Entrega (Check-out físico)
            Route::post('/{id}/entregar', [PrestamoController::class, 'entregar'])
                ->name('entregar');

            Route::post('/{id}/rechazar', [PrestamoController::class, 'rechazar'])
                ->name('rechazar');

            // [NUEVO] Registrar Devolución (Check-in físico)
            Route::post('/{id}/devolver', [PrestamoController::class, 'devolver'])
                ->name('devolver');
        });
        
    });
