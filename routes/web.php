<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\BuyController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MovementController;
use App\Http\Controllers\NotificationController;
use Inertia\Inertia;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\TransferController;
use App\Models\Movement;
use Illuminate\Notifications\DatabaseNotificationCollection;

// Rutas protegidas (Solo para usuarios logueados)
Route::middleware(['auth'])->group(function () {

    Route::get('/', function () {
        return view('pages.dashboard.ecommerce', ['title' => 'InvenCoo']);
    })->name('dashboard');


    // profile pages
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');


    Route::resource('users', UserController::class);
    Route::resource('roles', RoleController::class);
    Route::resource('brands', BrandController::class);


    Route::resource('categories', CategoryController::class);
    Route::resource('suppliers', SupplierController::class);
    Route::resource('products', ProductController::class);
    Route::resource('movements', MovementController::class);
    Route::resource('buys', BuyController::class);
Route::patch('buys/{buy}/cancel', [BuyController::class, 'cancel'])->name('buys.cancel');
Route::patch('buys/{buy}/restore', [BuyController::class, 'restore'])->name('buys.restore');


     Route::resource('transfers', TransferController::class)->except(['edit', 'update', 'destroy']);
    Route::patch('transfers/{transfer}/approve', [TransferController::class, 'approve'])->name('transfers.approve');
    Route::patch('transfers/{transfer}/ship', [TransferController::class, 'ship'])->name('transfers.ship');
    Route::patch('transfers/{transfer}/receive', [TransferController::class, 'receive'])->name('transfers.receive');
    Route::patch('transfers/{transfer}/reject', [TransferController::class, 'reject'])->name('transfers.reject');

    Route::middleware(['auth'])->group(function () {
    Route::get('/notifications/fetch', [NotificationController::class, 'fetch'])->name('notifications.fetch');
    Route::post('/notifications/mark-read/{id}', [NotificationController::class, 'markRead'])->name('notifications.mark-read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
});
    // pages

    Route::get('/blank', function () {
        return view('pages.blank', ['title' => 'Blank']);
    })->name('blank');

    // error pages
    Route::get('/error-404', function () {
        return view('pages.errors.error-404', ['title' => 'Error 404']);
    })->name('error-404');

    // chart pages
    Route::get('/line-chart', function () {
        return view('pages.chart.line-chart', ['title' => 'Line Chart']);
    })->name('line-chart');

    Route::get('/bar-chart', function () {
        return view('pages.chart.bar-chart', ['title' => 'Bar Chart']);
    })->name('bar-chart');


    // authentication pages
    Route::get('/signin', function () {
        return view('pages.auth.signin', ['title' => 'Sign In']);
    })->name('signin');


    // ui elements pages
    Route::get('/alerts', function () {
        return view('pages.ui-elements.alerts', ['title' => 'Alerts']);
    })->name('alerts');

    Route::get('/avatars', function () {
        return view('pages.ui-elements.avatars', ['title' => 'Avatars']);
    })->name('avatars');

    Route::get('/badge', function () {
        return view('pages.ui-elements.badges', ['title' => 'Badges']);
    })->name('badges');

    Route::get('/buttons', function () {
        return view('pages.ui-elements.buttons', ['title' => 'Buttons']);
    })->name('buttons');

    Route::get('/image', function () {
        return view('pages.ui-elements.images', ['title' => 'Images']);
    })->name('images');

    Route::get('/videos', function () {
        return view('pages.ui-elements.videos', ['title' => 'Videos']);
    })->name('videos');


});








require __DIR__ . '/auth.php';
