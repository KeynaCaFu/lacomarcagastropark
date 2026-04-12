<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Helpers\CartHelper;
use Illuminate\Http\Request;

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
            'customer_phone' => 'required|string|max:20',
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
     * Confirmar orden desde drawer
     */
    public function confirmOrder(Request $request)
    {
        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|integer',
            'items.*.local_id' => 'required|integer',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        try {
            // TODO: Aquí irá la lógica para crear la orden en base de datos
            // Por ahora, devolver éxito y limpiar sesión

            session()->forget('cart');
            
            return response()->json([
                'success' => true,
                'message' => 'Orden confirmada exitosamente',
                'order_id' => 1  // Temporalmente, se actualizará cuando se implemente BD
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
