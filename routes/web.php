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
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\TransferController;

// Rutas protegidas (Solo para usuarios logueados)
Route::middleware(['auth'])->group(function () {

    // 1. Conexión del Dashboard real
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // 2. Perfil
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // 3. Módulos Core (El trabajo de tu pareja)
    Route::resource('users', UserController::class);
    Route::resource('roles', RoleController::class);
    Route::resource('brands', BrandController::class);
    Route::resource('categories', CategoryController::class);
    Route::resource('suppliers', SupplierController::class);
    Route::resource('products', ProductController::class);
    Route::resource('movements', MovementController::class);
    
    // Compras
    Route::resource('buys', BuyController::class);
    Route::patch('buys/{buy}/cancel', [BuyController::class, 'cancel'])->name('buys.cancel');
    Route::patch('buys/{buy}/restore', [BuyController::class, 'restore'])->name('buys.restore');

    // Transferencias
    Route::resource('transfers', TransferController::class)->except(['edit', 'update', 'destroy']);
    Route::patch('transfers/{transfer}/approve', [TransferController::class, 'approve'])->name('transfers.approve');
    Route::patch('transfers/{transfer}/ship', [TransferController::class, 'ship'])->name('transfers.ship');
    Route::patch('transfers/{transfer}/receive', [TransferController::class, 'receive'])->name('transfers.receive');
    Route::patch('transfers/{transfer}/reject', [TransferController::class, 'reject'])->name('transfers.reject');

    // Notificaciones
    Route::get('/notifications/fetch', [NotificationController::class, 'fetch'])->name('notifications.fetch');
    Route::post('/notifications/mark-read/{id}', [NotificationController::class, 'markRead'])->name('notifications.mark-read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
});

require __DIR__ . '/auth.php';