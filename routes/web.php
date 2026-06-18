<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\BuyController;
use App\Http\Controllers\MovementController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PurchaseRequestController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\TransferController;

// Rutas protegidas (Solo para usuarios autenticados)
Route::middleware(['auth'])->group(function () {

    // 1. Dashboard Principal
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // 2. Perfil de Usuario
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // 3. Módulos de Recursos Estándar (CRUD)
    // El control de acceso granular se delega al controlador implementando HasMiddleware
    Route::resource('users', UserController::class);
    Route::resource('roles', RoleController::class);
    Route::resource('brands', BrandController::class);
    Route::resource('categories', CategoryController::class);
    Route::resource('suppliers', SupplierController::class);
    Route::resource('products', ProductController::class);
    Route::resource('movements', MovementController::class);

    // 4. Módulo de Compras Directas
    Route::resource('buys', BuyController::class);
    Route::patch('buys/{buy}/cancel', [BuyController::class, 'cancel'])
        ->name('buys.cancel')
        ->middleware('can:anular compra');
    Route::patch('buys/{buy}/restore', [BuyController::class, 'restore'])
        ->name('buys.restore')
        ->middleware('can:crear compra');

    // 5. Módulo de Transferencias entre Sucursales
    Route::resource('transfers', TransferController::class)->except(['edit', 'update', 'destroy']);
    Route::patch('transfers/{transfer}/approve', [TransferController::class, 'approve'])
        ->name('transfers.approve')
        ->middleware('can:Aprobar');
    Route::patch('transfers/{transfer}/ship', [TransferController::class, 'ship'])
        ->name('transfers.ship')
        ->middleware('can:editar transferencia');
    Route::patch('transfers/{transfer}/receive', [TransferController::class, 'receive'])
        ->name('transfers.receive')
        ->middleware('can:editar transferencia');
    Route::patch('transfers/{transfer}/reject', [TransferController::class, 'reject'])
        ->name('transfers.reject')
        ->middleware('can:editar transferencia');

    // 6. Módulo de Solicitudes de Compra
    Route::resource('purchases', PurchaseRequestController::class)->except(['edit', 'update', 'destroy']);
    Route::patch('purchases/{purchaseRequest}/approve', [PurchaseRequestController::class, 'approve'])
        ->name('purchases.approve')
        ->middleware('can:Aprobar');
    Route::patch('purchases/{purchaseRequest}/reject', [PurchaseRequestController::class, 'reject'])
        ->name('purchases.reject')
        ->middleware('can:Aprobar');
    Route::patch('purchases/{purchaseRequest}/process', [PurchaseRequestController::class, 'process'])
        ->name('purchases.process')
        ->middleware('can:crear compra');

    // 7. Centro de Reportes (Protección unificada de extremo a extremo)
    Route::prefix('reports')
        ->name('reports.')
        ->middleware('can:ver reportes')
        ->group(function () {
            Route::get('/', [ReportController::class, 'index'])->name('index');
            Route::get('/movements', [ReportController::class, 'exportMovements'])->name('movements');
            Route::get('/products', [ReportController::class, 'exportProducts'])->name('products');
        });

    // 8. API interna de Notificaciones
    Route::get('/notifications/fetch', [NotificationController::class, 'fetch'])->name('notifications.fetch');
    Route::post('/notifications/mark-read/{id}', [NotificationController::class, 'markRead'])->name('notifications.mark-read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');

    Route::fallback(function () {
    return response()->view('pages.errors.error-404', ['title' => 'Página no encontrada'], 404);
    });


});

require __DIR__ . '/auth.php';
