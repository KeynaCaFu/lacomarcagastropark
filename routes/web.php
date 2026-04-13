<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LocalDashboardController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\PlazaController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ReceiptController;
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

// Welcome page -  La Comarca Gastro Parck
// Si está sin autenticar → muestra plaza
// Si es cliente → muestra plaza (con sesión)
// Si es admin/gerente → redirige a dashboard
Route::get('/', function () {
    if (auth()->check()) {
        $user = auth()->user();
        
        // Asegurar que la relación role esté cargada
        if (!$user->relationLoaded('role')) {
            $user->load('role');
        }
        
        // Solo admins se redirigen a sus dashboards
        if ($user->isAdminGlobal()) {
            return redirect()->route('admin.dashboard');
        } elseif ($user->isAdminLocal()) {
            return redirect()->route('dashboard');
        } elseif ($user->isClient()) {
            return redirect()->route('plaza.index');
        }
    }
    // Mostrar plaza a todos (autenticados o no)
    return redirect()->route('plaza.index');
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
        return redirect()->route('plaza.index');
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

    // Locales
    Route::prefix('locales')->name('locales.')->group(function () {
        Route::get('/', [\App\Http\Controllers\LocalController::class, 'indexAdmin'])->name('index');
        Route::post('/', [\App\Http\Controllers\LocalController::class, 'store'])->name('store');
        Route::put('/{localId}', [\App\Http\Controllers\LocalController::class, 'updateAdmin'])->name('update');
        Route::put('/{localId}/status', [\App\Http\Controllers\LocalController::class, 'updateStatus'])->name('update.status');
        Route::delete('/{localId}', [\App\Http\Controllers\LocalController::class, 'destroy'])->name('destroy');
    });
});

// ============================================================================
// RUTAS PARA ADMIN LOCAL (Gerentes)
// ============================================================================
Route::middleware(['auth', 'verified', 'admin.local'])->group(function () {

    // Mi Local
    Route::prefix('mi-local')->name('local.')->group(function () {
        Route::get('/', [\App\Http\Controllers\LocalController::class, 'index'])->name('index');
        Route::get('/editar', [\App\Http\Controllers\LocalController::class, 'edit'])->name('edit');
        Route::put('/actualizar', [\App\Http\Controllers\LocalController::class, 'update'])->name('update');
        Route::get('/galeria', [\App\Http\Controllers\LocalController::class, 'gallery'])->name('gallery');
        Route::post('/galeria/subir', [\App\Http\Controllers\LocalController::class, 'galleryUpload'])->name('gallery.upload');
        Route::delete('/galeria/{id}', [\App\Http\Controllers\LocalController::class, 'galleryDelete'])->name('gallery.delete');
        Route::get('/horario', [\App\Http\Controllers\ScheduleController::class, 'schedule'])->name('schedule');
        Route::post('/horario', [\App\Http\Controllers\ScheduleController::class, 'storeSchedule'])->name('schedule.store');
        Route::put('/horario/{scheduleId}', [\App\Http\Controllers\ScheduleController::class, 'updateSchedule'])->name('schedule.update');
        Route::delete('/horario/{scheduleId}', [\App\Http\Controllers\ScheduleController::class, 'destroySchedule'])->name('schedule.destroy');
    });

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

    // Proveedores
    Route::prefix('proveedores')->name('suppliers.')->group(function () {

    Route::get('/', [SupplierController::class, 'index'])->name('index');
    Route::get('/create', [SupplierController::class, 'create'])->name('create');
    Route::post('/', [SupplierController::class, 'store'])->name('store');

    Route::get('/{id}', [SupplierController::class, 'show'])->name('show');
    Route::get('/{id}/edit', [SupplierController::class, 'edit'])->name('edit');
    Route::put('/{id}', [SupplierController::class, 'update'])->name('update');
    Route::delete('/{id}', [SupplierController::class, 'destroy'])->name('destroy');

    
    Route::post('/{id}/galeria', [SupplierController::class, 'storeGallery'])->name('gallery.store');
    Route::delete('/{supplierId}/galeria/{galleryId}', [SupplierController::class, 'deleteGallery'])->name('gallery.delete');



   
});

// RESEÑAS (GERENTE)
Route::prefix('resenas')->name('reviews.')->group(function () {
    Route::get('/', [ReviewController::class, 'index'])->name('index');
    Route::post('/{id}/responder', [ReviewController::class, 'respond'])->name('respond');
    Route::put('/respuesta/{reviewId}', [ReviewController::class, 'updateResponse'])->name('response.update');
    Route::delete('/respuesta/{reviewId}', [ReviewController::class, 'deleteResponse'])->name('response.delete');
});

  
    // ÓRDENES (GERENTE) - Ver, filtrar y cambiar estado
    Route::prefix('ordenes')->name('orders.')->group(function () {
        Route::get('/', [OrderController::class, 'index'])->name('index');
        Route::get('/api/pendientes-count', [OrderController::class, 'getPendingCount'])->name('pending-count');
        Route::get('/api/productos-local', [OrderController::class, 'getLocalProducts'])->name('local-products');
        Route::get('/api/buscar-clientes', [OrderController::class, 'searchCustomers'])->name('search-customers');
        Route::post('/crear', [OrderController::class, 'store'])->name('store');
        Route::get('/{order}', [OrderController::class, 'show'])->name('show');
        Route::post('/{order}/cambiar-estado', [OrderController::class, 'changeStatus'])->name('change-status');
        Route::post('/{order}/actualizar', [OrderController::class, 'update'])->name('update');
        Route::get('/{order}/edit', [OrderController::class, 'edit'])->name('edit');   
        Route::delete('/{order}', [OrderController::class, 'destroy'])->name('destroy');
        
        // Rutas para comprobantes
        Route::get('/{order}/comprobante/descargar', [ReceiptController::class, 'downloadReceipt'])->name('receipt.download');
        Route::post('/{order}/comprobante/regenerar', [ReceiptController::class, 'regenerateReceiptAction'])->name('receipt.regenerate');
        Route::get('/{order}/comprobante/ver', [ReceiptController::class, 'viewReceipt'])->name('receipt.view');
        Route::post('/{order}/comprobante/reenviar', [ReceiptController::class, 'resendReceipt'])->name('receipt.resend');
        Route::post('/{order}/comprobante/validar-cliente', [ReceiptController::class, 'checkValidClient'])->name('receipt.check-client');
        Route::get('/comprobante/historial', [ReceiptController::class, 'viewOrderHistory'])->name('receipt.history');
        Route::get('/comprobante/buscar', [ReceiptController::class, 'searchOrderHistory'])->name('receipt.search');
    });

    // REPORTES (GERENTE) - Análisis de pedidos online vs presenciales
    Route::prefix('reportes')->name('reports.')->group(function () {
        // Vistas
        Route::get('/pedidos', [\App\Http\Controllers\ReportController::class, 'index'])->name('orders');
        Route::get('/productos', [\App\Http\Controllers\ReportController::class, 'products'])->name('products');
        
        // APIs
        Route::get('/api/pedidos', [\App\Http\Controllers\ReportController::class, 'getData'])->name('orders.data');
        Route::get('/api/productos', [\App\Http\Controllers\ReportController::class, 'getProductData'])->name('api.products');
        
        // Exportación
        Route::get('/descargar/html', [\App\Http\Controllers\ReportController::class, 'downloadHTML'])->name('download-html');
        Route::get('/exportar/pdf', [\App\Http\Controllers\ReportController::class, 'exportPDF'])->name('export-pdf');
        Route::get('/exportar/excel', [\App\Http\Controllers\ReportController::class, 'exportExcel'])->name('export-excel');
    });
});

// ============================================================================
// RUTAS PARA CLIENTE
// ============================================================================
Route::middleware(['auth', 'verified'])->group(function () {
    // Redirigir client-welcome a la plaza (ruta antigua, mantenida por compatibilidad)
    Route::get('/client-welcome', function () {
        return redirect()->route('plaza.index');
    })->name('client.welcome');
    
    Route::get('/client-profile', [ClienteController::class, 'editProfile'])->name('client.profile.edit');
    Route::patch('/client-profile', [ClienteController::class, 'updateProfile'])->name('client.profile.update');
});

// Profile routes (authenticated users)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


// RUTAS PARA PLAZA PÚBLICA (Admins/Gerentes pueden verla sin afectar su sesión)
Route::prefix('plaza')->name('plaza.')->middleware('preserve.admin.session')->group(function () {
    Route::get('/', [\App\Http\Controllers\PlazaController::class, 'index'])->name('index');
    Route::get('api/productos', [\App\Http\Controllers\PlazaController::class, 'getProductosByCategory'])->name('get.productos');
    Route::get('{id}/data', [\App\Http\Controllers\PlazaController::class, 'getLocalData'])->name('local.data')->where('id', '[0-9]+');
    Route::get('{id}', [\App\Http\Controllers\PlazaController::class, 'show'])->name('show')->where('id', '[0-9]+');
    Route::get('{local_id}/producto/{product_id}', [\App\Http\Controllers\PlazaController::class, 'showProduct'])->name('product.detail')->where(['local_id' => '[0-9]+', 'product_id' => '[0-9]+']);
    
    // Carrito API (las rutas existen para todos, el controller maneja autenticación)
    Route::post('carrito/agregar', [\App\Http\Controllers\CartController::class, 'addToCart'])->name('add.cart');
    Route::get('carrito/api/get', [\App\Http\Controllers\CartController::class, 'getCart'])->name('cart.get');
    Route::post('carrito/api/actualizar-cantidad', [\App\Http\Controllers\CartController::class, 'updateItemQuantity'])->name('cart.update.qty');
    Route::post('carrito/api/remover', [\App\Http\Controllers\CartController::class, 'removeItem'])->name('cart.remove');
    Route::post('carrito/api/limpiar', [\App\Http\Controllers\CartController::class, 'clearCart'])->name('cart.clear');
    Route::post('carrito/api/confirmar', [\App\Http\Controllers\CartController::class, 'confirmOrder'])->name('order.create');
});

// ==========================================
// RUTAS DE PRUEBA - Errores de Conexión
// Solo en modo desarrollo - Comentar en producción
// ==========================================
if (app()->environment('local')) {
    Route::prefix('test')->group(function () {
        // Prueba de error de base de datos
        Route::get('db-error', function () {
            return view('errors.db-connection');
        })->name('test.db-error');

        // Prueba de error de internet
        Route::get('internet-error', function () {
            return view('errors.no-internet');
        })->name('test.internet-error');

        // Prueba de error de conexión genérico
        Route::get('connection-error', function () {
            return view('errors.connection-error', [
                'code' => 503,
                'title' => 'Error de Conexión',
                'message' => 'Esta es una prueba de vista de error genérico.'
            ]);
        })->name('test.connection-error');

        // Forzar un error de BD real
        Route::get('trigger-db-error', function () {
            try {
                // Intentar una consulta que fallará porque la tabla no existe
                DB::select('SELECT * FROM tabla_que_no_existe_12345');
            } catch (\Exception $e) {
                // Capturar y relanzar para que el Handler lo procese
                throw $e;
            }
        })->name('test.trigger-db-error');
    });
}

require __DIR__.'/auth.php';
