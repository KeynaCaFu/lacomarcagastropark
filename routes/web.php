<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LocalDashboardController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\ClienteController;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Welcome page (without authentication)
// Home: redirige según sesión (login o dashboard correspondiente)
Route::get('/', function () {
    if (auth()->check()) {
        $user = auth()->user();
        
        // Asegurar que la relación role esté cargada
        if (!$user->relationLoaded('role')) {
            $user->load('role');
        }
        
        if ($user->isAdminGlobal()) {
            return redirect()->route('admin.dashboard');
        } elseif ($user->isClient()) {
            return redirect()->route('client.welcome');
        }
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
})->name('home');

// Redirect legacy routes to login
Route::get('/entrar/admin/local', function () {
    return redirect()->route('login');
})->name('enter.local');

Route::get('/entrar/admin/global', function () {
    return redirect()->route('login');
})->name('enter.global');

// Dashboard - requires authentication
Route::get('/dashboard', function () {
    $user = auth()->user();
    
    // Asegurar que la relación role esté cargada
    if (!$user->relationLoaded('role')) {
        $user->load('role');
    }
    
    if ($user->isAdminGlobal()) {
        return redirect()->route('admin.dashboard');
    }
    
    if ($user->isClient()) {
        return redirect()->route('client.welcome');
    }
    
    // For local managers, show the dashboard
    return app(LocalDashboardController::class)->index(request());
})->middleware(['auth', 'verified'])->name('dashboard');

// RUTAS PARA ADMIN GLOBAL
Route::middleware(['auth', 'verified', 'admin.global'])->group(function () {
    Route::get('/admin', [AdminDashboardController::class, 'index'])->name('admin.dashboard');

    // Usuarios
    Route::prefix('usuarios')->name('users.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::post('/', [UserController::class, 'store'])->name('store');
        Route::get('/{user}', [UserController::class, 'show'])->name('show');
        Route::match(['put', 'post'], '/{user}', [UserController::class, 'update'])->name('update');
        Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
        Route::get('/{user}/edit-modal', [UserController::class, 'editModal'])->name('edit.modal');
    });

    // Eventos
    Route::prefix('eventos')->name('eventos.')->group(function () {
        Route::get('/', [EventController::class, 'index'])->name('index');
        Route::get('/create', [EventController::class, 'create'])->name('create');
        Route::post('/', [EventController::class, 'store'])->name('guardar');
        // Modales (AJAX) - RUTAS EXPLÍCITAS SIN BINDING IMPLÍCITO
        Route::get('/modal-show/{event_id}', [EventController::class, 'showModal'])->name('show.modal')->where('event_id', '[0-9]+');
        Route::get('/modal-edit/{event_id}', [EventController::class, 'editModal'])->name('edit.modal')->where('event_id', '[0-9]+');
        // Rutas generales - usando binding implícito con {evento}
        Route::get('/{evento}', [EventController::class, 'show'])->name('show');
        Route::get('/{evento}/edit', [EventController::class, 'edit'])->name('editar');
        Route::put('/{evento}', [EventController::class, 'update'])->name('actualizar');
        Route::delete('/{evento}', [EventController::class, 'destroy'])->name('eliminar');
    });
});

// ============================================================================
// RUTAS PARA ADMIN LOCAL (Gerentes)
// ============================================================================
Route::middleware(['auth', 'verified', 'admin.local'])->group(function () {
    // Productos
    Route::prefix('productos')->name('products.')->group(function () {
        Route::get('/', [ProductController::class, 'index'])->name('index');
        Route::get('/create', [ProductController::class, 'create'])->name('create');
        Route::post('/', [ProductController::class, 'store'])->name('store');
        Route::get('/{id}', [ProductController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [ProductController::class, 'edit'])->name('edit');
        Route::put('/{id}', [ProductController::class, 'update'])->name('update');
        Route::delete('/{id}', [ProductController::class, 'destroy'])->name('destroy');
        // Galería de imágenes
        Route::get('/{id}/gallery', [ProductController::class, 'gallery'])->name('gallery');
        Route::post('/{id}/gallery', [ProductController::class, 'addGalleryImage'])->name('gallery.add');
        Route::delete('/gallery/{galleryId}', [ProductController::class, 'removeGalleryImage'])->name('gallery.remove');
        // Modales (AJAX)
        Route::get('/{id}/show-modal', [ProductController::class, 'showModal'])->name('show.modal');
        Route::get('/{id}/edit-modal', [ProductController::class, 'editModal'])->name('edit.modal');
    });
});

// ============================================================================
// RUTAS PARA CLIENTE
// ============================================================================
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/client-welcome', [ClienteController::class, 'index'])->name('client.welcome');
    Route::get('/client-profile', [ClienteController::class, 'editProfile'])->name('client.profile.edit');
    Route::patch('/client-profile', [ClienteController::class, 'updateProfile'])->name('client.profile.update');
});

// Profile routes (authenticated users)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
