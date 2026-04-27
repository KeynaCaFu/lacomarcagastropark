<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\QrSetting;
use App\Helpers\CartHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    /**
     * Agregar producto al carrito (sesión)
     */
    public function addToCart(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|integer|exists:tbproduct,product_id',
            'local_id' => 'required|integer|exists:tblocal,local_id',
            'quantity' => 'required|integer|min:1',
            'customization' => 'nullable|string|max:500',
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'delivery_address' => 'nullable|string|max:500',
            'additional_notes' => 'nullable|string|max:500',
        ]);

        // Obtener producto para validar que existe en el local
        $product = Product::where('product_id', $validated['product_id'])
            ->whereHas('locals', function ($query) use ($validated) {
                $query->where('tblocal.local_id', $validated['local_id']);
            })
            ->first();

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Producto no disponible en este local'
            ], 422);
        }

        // Obtener carrito actual de sesión
        $cart = session()->get('cart', []);

        // Normalizar customization para comparación consistente
        $normalizedCustomization = CartHelper::normalizeCustomization($validated['customization'] ?? '');

        // Crear identificador único para el item usando customización normalizada
        $itemKey = CartHelper::generateItemKey($product->product_id, $validated['customization'] ?? '');

        // Verificar si el item ya existe en el carrito
        $existingItem = null;
        foreach ($cart as $key => $item) {
            // Comparar por item_key y local_id
            // El item_key ya contiene la customización normalizada, así que detecta duplicados correctamente
            if ($item['item_key'] === $itemKey && $item['local_id'] === $validated['local_id']) {
                $existingItem = $key;
                break;
            }
        }

        // Si existe, incrementar cantidad; si no, agregar nuevo item
        if ($existingItem !== null) {
            $cart[$existingItem]['quantity'] += $validated['quantity'];
            $message = 'Cantidad actualizada en el carrito';
        } else {
            // Guardar la customización original pero usar la normalizada para comparaciones
            $newItem = [
                'item_key' => $itemKey,
                'product_id' => $product->product_id,
                'local_id' => $validated['local_id'],
                'name' => $product->name,
                'description' => $product->description ?? '',
                'price' => $product->price,
                'quantity' => $validated['quantity'],
                'customization' => $validated['customization'] ?? '', // Customización original
                'customization_normalized' => $normalizedCustomization, // Normalizada para referencia
                'photo_url' => $product->photo_url ?? asset('images/product-placeholder.png'),
                'added_at' => now()->toIso8601String(),
                // Datos del cliente
                'customer_name' => $validated['customer_name'],
                'customer_email' => $validated['customer_email'],
                'customer_phone' => $validated['customer_phone'],
                'delivery_address' => $validated['delivery_address'] ?? '',
                'additional_notes' => $validated['additional_notes'] ?? '',
            ];
            $cart[] = $newItem;
            $message = 'Producto agregado al carrito';
        }

        // Guardar carrito en sesión
        session()->put('cart', $cart);

        return response()->json([
            'success' => true,
            'message' => $message,
            'cart_count' => count($cart),
            'cart' => $cart,
        ]);
    }

    /**
     * Ver carrito
     */
    public function view()
    {
        $cart = session()->get('cart', []);
        return view('plaza.carrito.view', compact('cart'));
    }

    /**
     * Obtener carrito como JSON (para drawer)
     */
    public function getCart()
    {
        $cart = session()->get('cart', []);
        return response()->json([
            'success' => true,
            'cart' => $cart
        ]);
    }

    /**
     * Actualizar cantidad de un item en el carrito
     */
    public function updateItemQuantity(Request $request)
    {
        $validated = $request->validate([
            'item_index' => 'required|integer|min:0',
            'quantity' => 'required|integer|min:1',
        ]);

        try {
            $cart = session()->get('cart', []);
            $index = $validated['item_index'];

            if (!isset($cart[$index])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Item no encontrado en el carrito'
                ], 422);
            }

            // Actualizar cantidad
            $cart[$index]['quantity'] = $validated['quantity'];
            session()->put('cart', $cart);

            return response()->json([
                'success' => true,
                'message' => 'Cantidad actualizada',
                'cart' => $cart,
                'cart_count' => count($cart)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remover un item del carrito por item_key
     */
    public function removeItem(Request $request)
    {
        $validated = $request->validate([
            'item_key' => 'required|string',
        ]);

        try {
            $cart = session()->get('cart', []);
            $itemKeyToRemove = $validated['item_key'];
            $found = false;

            // Buscar por item_key
            foreach ($cart as $index => $item) {
                if ($item['item_key'] === $itemKeyToRemove) {
                    unset($cart[$index]);
                    $found = true;
                    break;
                }
            }

            if (!$found) {
                return response()->json([
                    'success' => false,
                    'message' => 'Item no encontrado en el carrito'
                ], 422);
            }

            // Re-indexar el array
            $cart = array_values($cart);
            session()->put('cart', $cart);

            return response()->json([
                'success' => true,
                'message' => 'Item eliminado del carrito',
                'cart' => $cart,
                'cart_count' => count($cart)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Limpiar carrito completamente
     */
    public function clearCart(Request $request)
    {
        try {
            session()->forget('cart');

            return response()->json([
                'success' => true,
                'message' => 'Carrito vaciado',
                'cart' => [],
                'cart_count' => 0
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Confirmar orden cliente (Plaza Gastropark)
     * CA1: Validar QR
     * CA2: Generar Token de Verificación
     * CA3/CA4: Validar ubicación GPS dentro del perímetro
     * CA5: Registrar timestamp de confirmación
     * Reordenar un pedido anterior
     * 
     * Agrega todos los items de un pedido anterior al carrito
     * Valida que los productos sigan disponibles en el local
     * Si algunos productos no están disponibles, devuelve información sobre ellos
     */
    public function reorderOrder(Request $request)
    {
        $validated = $request->validate([
            'order_id' => 'required|integer|exists:tborder,order_id',
        ]);

        try {
            // Obtener la orden con sus items y relaciones
            $order = \App\Models\Order::with(['items.product', 'local'])
                ->findOrFail($validated['order_id']);

            // Verificar que la orden pertenece al usuario autenticado o que es un admin
            $userHasOrder = $order->user()->where('tbuser.user_id', auth()->id())->exists();
            if (!$userHasOrder && !auth()->user()->isAdminGlobal()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tienes permiso para reordenar este pedido'
                ], 403);
            }

            $unavailableProducts = [];
            $cart = session()->get('cart', []);
            $addedCount = 0;

            // Procesar cada item del pedido anterior
            foreach ($order->items as $item) {
                // Verificar que el producto siga disponible en el local
                $product = Product::where('product_id', $item->product_id)
                    ->whereHas('locals', function ($query) use ($order) {
                        $query->where('tblocal.local_id', $order->local_id);
                    })
                    ->first();

                if (!$product) {
                    // Producto no disponible
                    $unavailableProducts[] = [
                        'product_id' => $item->product_id,
                        'product_name' => $item->product->name ?? 'Producto no encontrado',
                        'quantity' => $item->quantity
                    ];
                    continue;
                }

                // Generar item key con customización
                $itemKey = CartHelper::generateItemKey($product->product_id, $item->customization);

                // Buscar si el item ya existe en el carrito
                $existingItem = null;
                foreach ($cart as $key => $cartItem) {
                    if ($cartItem['item_key'] === $itemKey && $cartItem['local_id'] === $order->local_id) {
                        $existingItem = $key;
                        break;
                    }
                }

                // Si existe, incrementar cantidad; si no, agregar nuevo item
                if ($existingItem !== null) {
                    $cart[$existingItem]['quantity'] += $item->quantity;
                } else {
                    // Crear nuevo item en el carrito
                    $newItem = [
                        'item_key' => $itemKey,
                        'product_id' => $product->product_id,
                        'local_id' => $order->local_id,
                        'name' => $product->name,
                        'description' => $product->description ?? '',
                        'price' => $product->price,
                        'quantity' => $item->quantity,
                        'customization' => $item->customization ?? '',
                        'customization_normalized' => CartHelper::normalizeCustomization($item->customization),
                        'photo_url' => $product->photo_url ?? asset('images/product-placeholder.png'),
                        'added_at' => now()->toIso8601String(),
                        // Datos del cliente (se pueden actualizar en el carrito)
                        'customer_name' => auth()->user()->full_name ?? auth()->user()->name ?? '',
                        'customer_email' => auth()->user()->email ?? '',
                        'customer_phone' => auth()->user()->phone ?? '',
                        'delivery_address' => '',
                        'additional_notes' => '',
                    ];
                    $cart[] = $newItem;
                }
                $addedCount++;
            }

            // Guardar carrito actualizado en sesión
            session()->put('cart', $cart);

            $message = "Se agregaron {$addedCount} producto(s) al carrito";
            
            if (!empty($unavailableProducts)) {
                $productNames = implode(', ', array_map(function($p) { 
                    return $p['product_name']; 
                }, $unavailableProducts));
                
                return response()->json([
                    'success' => true,
                    'partial' => true,
                    'message' => $message,
                    'warning' => "Los siguientes productos ya no están disponibles y no fueron agregados: {$productNames}",
                    'unavailable_products' => $unavailableProducts,
                    'cart_count' => count($cart),
                    'cart' => $cart,
                ]);
            }

            return response()->json([
                'success' => true,
                'partial' => false,
                'message' => $message,
                'cart_count' => count($cart),
                'cart' => $cart,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar la reorden: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Confirmar orden desde drawer
     */
    public function confirmOrder(
        Request $request, 
        \App\Services\OrderTokenService $tokenService, 
        \App\Services\LocationService $locationService
    ) {
        $cart = session()->get('cart', []);

        // VALIDACIÓN 1: Carrito vacío
        if (empty($cart)) {
            return response()->json([
                'success' => false,
                'message' => 'Tu carrito está vacío. Agrega productos antes de confirmar.'
            ], 422);
        }

        $validated = $request->validate([
            'qr_key' => 'required|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $user = auth()->user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Debes iniciar sesión para confirmar tu orden.'
            ], 401);
        }

        // VALIDACIÓN 2: QR válido y activo (CA1)
        $qrSetting = QrSetting::where('qr_key', $validated['qr_key'])
            ->where('is_active', true)
            ->first();

        if (!$qrSetting) {
            return response()->json([
                'success' => false,
                'message' => 'El código QR es inválido o ha expirado. Por favor escanea el QR actual de la plaza.'
            ], 403);
        }

        // VALIDACIÓN 3: Ubicación dentro del perímetro (CA3/CA4)
        if (!$locationService->isWithinPlaza($validated['latitude'], $validated['longitude'])) {
            return response()->json([
                'success' => false,
                'message' => 'Intento de pedido fuera del área GPS permitida. Debes estar físicamente en Gastropark para realizar tu pedido.'
            ], 403);
        }

        try {
            DB::beginTransaction();

            $createdOrders = [];
            $confirmedAt = now(); // CA5: Timestamp

            // Agrupar items por local (carrito puede tener múltiples locales)
            $itemsByLocal = [];
            foreach ($cart as $item) {
                $itemsByLocal[$item['local_id']][] = $item;
            }

            // Crear una orden por cada local
            foreach ($itemsByLocal as $localId => $items) {
                // Generar número de orden
                $orderNumber = 'ORD-' . str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
                
                // CA2: Generar token único de verificación
                $verificationToken = $tokenService->generateUniqueToken();

                $totalAmount = array_sum(array_map(fn($i) => $i['price'] * $i['quantity'], $items));
                $totalQuantity = array_sum(array_column($items, 'quantity'));
                $notes = collect($items)->pluck('additional_notes')->filter()->first() ?? null;

                // Insertar en tborder
                $order = Order::create([
                    'order_number' => $orderNumber,
                    'status' => Order::STATUS_PENDING,
                    'origin' => Order::ORIGIN_WEB,
                    'local_id' => $localId,
                    'total_amount' => $totalAmount,
                    'quantity' => $totalQuantity,
                    'additional_notes' => $notes,
                    'verification_token' => $verificationToken,
                    'confirmed_at' => $confirmedAt,
                    'date' => now()->toDateString(),
                    'time' => now()->toTimeString(),
                ]);

                // Asociar local en tblocal_order
                $order->locals()->attach($localId);

                // Asociar usuario en tbuser_order
                $order->user()->attach($user->user_id);

                // Guardar items en tborder_item
                foreach ($items as $item) {
                    OrderItem::create([
                        'order_id' => $order->order_id,
                        'product_id' => $item['product_id'],
                        'quantity' => $item['quantity'],
                        'customization' => $item['customization'] ?? null,
                    ]);
                }

                $createdOrders[] = [
                    'order_number' => $orderNumber,
                    'token' => $verificationToken,
                    'local_id' => $localId,
                ];
            }

            DB::commit();

            // Limpiar sesión
            session()->forget('cart');

            return response()->json([
                'success' => true,
                'message' => '¡Órdenes procesadas con éxito!',
                'orders' => $createdOrders
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar la orden: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener órdenes pendientes del cliente actual
     */
    public function getMyOrders()
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Debes iniciar sesión'
            ], 401);
        }

        // Obtener órdenes del cliente que estén en estado Pending o Preparing
        $orders = Order::whereHas('user', function ($query) use ($user) {
            $query->where('tbuser.user_id', $user->user_id);
        })
        ->whereIn('status', [Order::STATUS_PENDING, Order::STATUS_PREPARATION])
        ->with(['items.product', 'local'])
        ->orderBy('created_at', 'desc')
        ->get();

        $ordersFormatted = $orders->map(function ($order) {
            return [
                'order_id' => $order->order_id,
                'order_number' => $order->order_number,
                'token' => $order->verification_token,
                'status' => $order->status,
                'status_label' => Order::getStatuses()[$order->status] ?? $order->status,
                'total_amount' => $order->total_amount,
                'quantity' => $order->quantity,
                'local_name' => $order->local->name ?? 'Local desconocido',
                'created_at' => $order->created_at->format('Y-m-d H:i:s'),
                'confirmed_at' => $order->confirmed_at ? $order->confirmed_at->format('Y-m-d H:i:s') : null,
                'can_cancel' => $order->status === Order::STATUS_PENDING, // Solo puede cancelar si está Pending
                'items' => $order->items->map(function ($item) {
                    return [
                        'product_id' => $item->product_id,
                        'product_name' => $item->product->name,
                        'quantity' => $item->quantity,
                        'customization' => $item->customization,
                        'price' => $item->product->price,
                    ];
                })
            ];
        });

        return response()->json([
            'success' => true,
            'orders' => $ordersFormatted
        ]);
    }

    /**
     * Obtener historial de órdenes del cliente (Ready, Delivered, Cancelled)
     */
    public function getOrderHistory()
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Debes iniciar sesión'], 401);
        }

        $orders = Order::whereHas('user', function ($query) use ($user) {
            $query->where('tbuser.user_id', $user->user_id);
        })
        ->whereIn('status', [Order::STATUS_READY, Order::STATUS_DELIVERED, Order::STATUS_CANCELLED])
        ->with(['items.product', 'local'])
        ->orderBy('created_at', 'desc')
        ->limit(30)
        ->get();

        $ordersFormatted = $orders->map(function ($order) {
            return [
                'order_id'     => $order->order_id,
                'order_number' => $order->order_number,
                'token'        => $order->verification_token,
                'status'       => $order->status,
                'status_label' => Order::getStatuses()[$order->status] ?? $order->status,
                'total_amount' => $order->total_amount,
                'local_name'   => $order->local->name ?? 'Local desconocido',
                'created_at'   => $order->created_at->format('Y-m-d H:i:s'),
                'can_cancel'   => false,
                'items'        => $order->items->map(function ($item) {
                    return [
                        'product_id'    => $item->product_id,
                        'product_name'  => $item->product->name,
                        'quantity'      => $item->quantity,
                        'customization' => $item->customization,
                        'price'         => $item->product->price,
                    ];
                }),
            ];
        });

        return response()->json(['success' => true, 'orders' => $ordersFormatted]);
    }

    /**
     * Cancelar una orden (solo si está en Pending)
     * Devuelve los items al carrito
     */
    public function cancelOrder(Request $request, $orderId)
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Debes iniciar sesión'
            ], 401);
        }

        $validated = $request->validate([
            'reason' => 'nullable|string|max:500'
        ]);

        try {
            $order = Order::with('items.product', 'local')->findOrFail($orderId);

            // Validar que el orden pertenece al usuario
            $isOwner = $order->user()->where('tbuser.user_id', $user->user_id)->exists();
            if (!$isOwner) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tienes permiso para cancelar esta orden'
                ], 403);
            }

            // Validar que la orden esté en estado Pending
            if ($order->status !== Order::STATUS_PENDING) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede cancelar una orden que ya está en ' . Order::getStatuses()[$order->status] ?? 'otro estado'
                ], 422);
            }

            // Iniciar transacción
            \Illuminate\Support\Facades\DB::beginTransaction();

            // Actualizar estado de la orden
            $order->update([
                'status' => Order::STATUS_CANCELLED,
                'cancellation_reason' => $validated['reason'] ?? 'Cancelada por el cliente'
            ]);

            // Recuperar items y devolverlos al carrito
            $cart = session()->get('cart', []);

            foreach ($order->items as $item) {
                // Crear item para el carrito con datos del producto
                $product = $item->product;
                $cartItem = [
                    'item_key' => CartHelper::generateItemKey($product->product_id, $item->customization ?? ''),
                    'product_id' => $product->product_id,
                    'local_id' => $order->local_id,
                    'name' => $product->name,
                    'description' => $product->description ?? '',
                    'price' => $product->price, // Usar precio actual
                    'quantity' => $item->quantity,
                    'customization' => $item->customization ?? '',
                    'customization_normalized' => CartHelper::normalizeCustomization($item->customization ?? ''),
                    'photo_url' => $product->photo_url ?? asset('images/product-placeholder.png'),
                    'added_at' => now()->toIso8601String(),
                    // Datos del cliente (recuperar de la orden anterior si existe)
                    'customer_name' => $user->full_name ?? $user->name,
                    'customer_email' => $user->email,
                    'customer_phone' => $user->phone ?? '',
                    'delivery_address' => '',
                    'additional_notes' => '',
                ];

                // Verificar si el item ya existe en el carrito (mismo producto, local y customización)
                $existingIndex = null;
                foreach ($cart as $idx => $existing) {
                    if ($existing['item_key'] === $cartItem['item_key'] && $existing['local_id'] === $cartItem['local_id']) {
                        $existingIndex = $idx;
                        break;
                    }
                }

                if ($existingIndex !== null) {
                    // Incrementar cantidad si ya existe
                    $cart[$existingIndex]['quantity'] += $cartItem['quantity'];
                } else {
                    // Agregar como nuevo item
                    $cart[] = $cartItem;
                }
            }

            session()->put('cart', $cart);

            \Illuminate\Support\Facades\DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Orden cancelada exitosamente. Los items han sido devueltos a tu carrito.',
                'cart_count' => count($cart)
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Orden no encontrada'
            ], 404);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al cancelar la orden: ' . $e->getMessage()
            ], 500);
        }
    }
}
