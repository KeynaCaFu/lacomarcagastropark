<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Data\ProductData;
use App\Data\ProductGalleryData;
use App\Events\ProductStatusUpdated;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    protected $productData;
    protected $galleryData;

    public function __construct(ProductData $productData, ProductGalleryData $galleryData)
    {
        $this->productData = $productData;
        $this->galleryData = $galleryData;
    }

    /**
     * Mostrar lista de Productos con filtros
     */
    public function index(Request $request)
    {
        $filters = [
            'search' => $request->input('buscar'),
            'status' => $this->mapStatusToEnglish($request->input('estado')),
            'category' => $request->input('categoria')
        ];

        // Si el usuario es gerente, filtrar por su local
        $user = auth()->user();
        if ($user->isAdminLocal()) {
            // Obtener el primer local del gerente
            $local = $user->locals()->first();
            if ($local) {
                $filters['local_id'] = $local->local_id;
                $products = $this->productData->all($filters);
                $totals = $this->productData->countTotalsByLocal($local->local_id);
                $categories = $this->productData->getCategoriesByLocal($local->local_id);
            } else {
                // Si el gerente no tiene local asignado, retornar vacío
                $products = [];
                $totals = ['total' => 0, 'available' => 0, 'unavailable' => 0];
                $categories = [];
            }
        } else {
            // Admin global ve todos los productos
            $products = $this->productData->all($filters);
            $totals = $this->productData->countTotals();
            $categories = $this->productData->getAllCategories();
        }

        return view('products.index', compact('products', 'totals', 'categories'));
    }

    /**
     * Mostrar formulario para crear nuevo Producto
     */
    public function create()
    {
        return view('products.create');
    }

    /**
     * Mostrar formulario para editar Producto
     */
    public function edit($id)
    {
        if (!$this->canAccessProduct($id)) {
            return redirect()->route('products.index')
                ->with('error', 'No tienes permiso para editar este producto.');
        }

        $product = $this->productData->find($id);

        if (!$product) {
            return redirect()->route('products.index')
                ->with('warning', 'Producto no encontrado.');
        }

        return view('products.edit', compact('product'));
    }

    /**
     * Mostrar detalles de un Producto
     */
    public function show($id)
    {
        if (!$this->canAccessProduct($id)) {
            return redirect()->route('products.index')
                ->with('error', 'No tienes permiso para ver este producto.');
        }

        $product = $this->productData->find($id);

        if (!$product) {
            return redirect()->route('products.index')
                ->with('warning', 'Producto no encontrado.');
        }

        return view('products.show', compact('product'));
    }

    /**
     * Mostrar galería de imágenes del Producto
     */
    public function gallery($id)
    {
        if (!$this->canAccessProduct($id)) {
            return redirect()->route('products.index')
                ->with('error', 'No tienes permiso para ver la galería de este producto.');
        }

        $product = $this->productData->find($id);

        if (!$product) {
            return redirect()->route('products.index')
                ->with('warning', 'Producto no encontrado.');
        }

        $gallery = $this->galleryData->getByProductId($id);

        return view('products.gallery', compact('product', 'gallery'));
    }

    /**
     * Crear nuevo Producto
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        $localId = null;

        // Si es gerente, obtener su local
        if ($user->isAdminLocal()) {
            $local = $user->locals()->first();
            if ($local) {
                $localId = $local->local_id;
            }
        }

        // Validación base
        $rules = [
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'categoria' => 'nullable|string|max:100',
            'etiqueta' => 'nullable|string|max:100',
            'tipo_producto' => 'nullable|string|max:50',
            'precio' => 'required|numeric|min:0',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'estado' => 'required|string|in:Disponible,No disponible'
        ];

        $messages = [
            'nombre.required' => 'El nombre del producto es obligatorio',
            'precio.required' => 'El precio es obligatorio',
            'precio.numeric' => 'El precio debe ser un número',
            'precio.min' => 'El precio no puede ser negativo',
            'foto.image' => 'El archivo debe ser una imagen',
            'foto.mimes' => 'La imagen debe ser JPG, PNG o GIF',
            'foto.max' => 'La imagen no puede ser mayor a 2MB',
            'estado.in' => 'El estado debe ser Disponible o No disponible'
        ];

        // Crear validador
        $validator = Validator::make($request->all(), $rules, $messages);

        // Agregar validación personalizada para el nombre
        if ($localId) {
            $validator->after(function ($validator) use ($request, $localId) {
                $nombreProducto = $request->input('nombre');
                
                // Verificar si existe otro producto con el mismo nombre en este local
                $exists = DB::table('tbproduct as p')
                    ->join('tblocal_product as lp', 'p.product_id', '=', 'lp.product_id')
                    ->where('p.name', $nombreProducto)
                    ->where('lp.local_id', $localId)
                    ->exists();

                if ($exists) {
                    $validator->errors()->add('nombre', 'Ya existe un producto con este nombre en tu local');
                }
            });
        } else {
            // Para admin global: validar unicidad global
            $validator->after(function ($validator) use ($request) {
                $nombreProducto = $request->input('nombre');
                $exists = DB::table('tbproduct')
                    ->where('name', $nombreProducto)
                    ->exists();

                if ($exists) {
                    $validator->errors()->add('nombre', 'Ya existe un producto con este nombre');
                }
            });
        }

        $validated = $validator->validate();

        // Procesar la foto si se envía
        $photoPath = null;
        if ($request->hasFile('foto')) {
            // Crear directorio si no existe
            $productDir = public_path('images/products');
            if (!File::isDirectory($productDir)) {
                File::makeDirectory($productDir, 0755, true, true);
            }

            $file = $request->file('foto');
            $filename = Str::slug($validated['nombre']) . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move($productDir, $filename);
            $photoPath = 'images/products/' . $filename;
        }

        $data = [
            'name' => $validated['nombre'],
            'description' => $validated['descripcion'] ?? null,
            'category' => $validated['categoria'] ?? null,
            'tag' => $validated['etiqueta'] ?? null,
            'product_type' => $validated['tipo_producto'] ?? null,
            'price' => $validated['precio'],
            'photo' => $photoPath,
            'status' => $this->mapStatusToEnglish($validated['estado'])
        ];

        $product = $this->productData->create($data);

        // Si es gerente, crear automáticamente la relación con su local
        if ($localId) {
            // Crear relación en tblocal_product
            DB::table('tblocal_product')->insert([
                'local_id' => $localId,
                'product_id' => $product->product_id,
                'price' => $validated['precio'],
                'is_available' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        // Agregar imágenes a la galería si se envían
        $gallery = $request->input('gallery', []);
        if (!empty($gallery)) {
            foreach ($gallery as $imageUrl) {
                if (!empty($imageUrl)) {
                    $this->galleryData->add($product->product_id, $imageUrl);
                }
            }
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => '✓ Producto creado exitosamente',
                'product' => $product
            ]);
        }

        return redirect()->route('products.index')
            ->with('success', '✓ Producto creado exitosamente');
    }

    /**
     * Actualizar un Producto
     */
    public function update(Request $request, $id)
    {
        if (!$this->canAccessProduct($id)) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'No tienes permiso para actualizar este producto'], 403);
            }
            return redirect()->route('products.index')->with('error', 'No tienes permiso para actualizar este producto.');
        }

        $product = $this->productData->find($id);

        if (!$product) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Producto no encontrado'], 404);
            }
            return redirect()->route('products.index')->with('error', 'Producto no encontrado');
        }

        // Check if this is a partial update (only status)
        if ($request->has('status') && $request->only('status') === ['status' => $request->input('status')]) {
            // Partial update - only validate status
            $validated = $request->validate([
                'status' => 'required|string|in:Available,Unavailable'
            ]);

            $data = ['status' => $validated['status']];
            $updatedProduct = $this->productData->update($id, $data);

            // Broadcast cambio de estado en tiempo real
            $currentUser = auth()->user();
            $broadcastLocal = $currentUser->isAdminLocal()
                ? $currentUser->locals()->first()
                : $product->locals()->first();

            if ($broadcastLocal) {
                broadcast(new ProductStatusUpdated(
                    (int) $id,
                    $broadcastLocal->local_id,
                    $validated['status'],
                    $product->name
                ))->toOthers();
            }

            return response()->json([
                'success' => true,
                'message' => '✓ Estado actualizado exitosamente',
                'product' => $updatedProduct
            ]);
        }

        // Full update - validate all fields
        $user = auth()->user();
        $localId = null;

        // Si es gerente, obtener su local
        if ($user->isAdminLocal()) {
            $local = $user->locals()->first();
            if ($local) {
                $localId = $local->local_id;
            }
        }

        // Validación base
        $rules = [
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'categoria' => 'nullable|string|max:100',
            'etiqueta' => 'nullable|string|max:100',
            'tipo_producto' => 'nullable|string|max:50',
            'precio' => 'required|numeric|min:0',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'estado' => 'required|string|in:Disponible,No disponible'
        ];

        $messages = [
            'nombre.required' => 'El nombre del producto es obligatorio',
            'precio.required' => 'El precio es obligatorio',
            'precio.numeric' => 'El precio debe ser un número',
            'precio.min' => 'El precio no puede ser negativo',
            'foto.image' => 'El archivo debe ser una imagen',
            'foto.mimes' => 'La imagen debe ser JPG, PNG o GIF',
            'foto.max' => 'La imagen no puede ser mayor a 2MB',
            'estado.in' => 'El estado debe ser Disponible o No disponible'
        ];

        // Crear validador
        $validator = Validator::make($request->all(), $rules, $messages);

        // Agregar validación personalizada para el nombre
        if ($localId) {
            $validator->after(function ($validator) use ($request, $localId, $id) {
                $nombreProducto = $request->input('nombre');
                
                // Verificar si existe OTRO producto con el mismo nombre en este local (excluyendo el actual)
                $exists = DB::table('tbproduct as p')
                    ->join('tblocal_product as lp', 'p.product_id', '=', 'lp.product_id')
                    ->where('p.name', $nombreProducto)
                    ->where('lp.local_id', $localId)
                    ->where('p.product_id', '!=', $id)
                    ->exists();

                if ($exists) {
                    $validator->errors()->add('nombre', 'Ya existe otro producto con este nombre en tu local');
                }
            });
        } else {
            // Para admin global: validar unicidad global (excluyendo el actual)
            $validator->after(function ($validator) use ($request, $id) {
                $nombreProducto = $request->input('nombre');
                $exists = DB::table('tbproduct')
                    ->where('name', $nombreProducto)
                    ->where('product_id', '!=', $id)
                    ->exists();

                if ($exists) {
                    $validator->errors()->add('nombre', 'Ya existe otro producto con este nombre');
                }
            });
        }

        $validated = $validator->validate();

        // Procesar la foto si se envía
        $photoPath = $product->photo; // Mantener la foto actual por defecto
        if ($request->hasFile('foto')) {
            // Eliminar foto anterior si existe
            if ($product->photo) {
                $oldPath = public_path(str_replace('public/', '', $product->photo));
                if (File::exists($oldPath)) {
                    File::delete($oldPath);
                }
            }
            
            // Crear directorio si no existe
            $productDir = public_path('images/products');
            if (!File::isDirectory($productDir)) {
                File::makeDirectory($productDir, 0755, true, true);
            }

            $file = $request->file('foto');
            $filename = Str::slug($validated['nombre']) . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move($productDir, $filename);
            $photoPath = 'images/products/' . $filename;
        }

        $data = [
            'name' => $validated['nombre'],
            'description' => $validated['descripcion'] ?? null,
            'category' => $validated['categoria'] ?? null,
            'tag' => $validated['etiqueta'] ?? null,
            'product_type' => $validated['tipo_producto'] ?? null,
            'price' => $validated['precio'],
            'photo' => $photoPath,
            'status' => $this->mapStatusToEnglish($validated['estado'])
        ];

        $updatedProduct = $this->productData->update($id, $data);

        // Broadcast cambio de estado en tiempo real (solo si el estado realmente cambió)
        $newStatus = $this->mapStatusToEnglish($validated['estado']);
        if ($product->status !== $newStatus) {
            $localForBroadcast = isset($local) ? $local : $product->locals()->first();
            if ($localForBroadcast) {
                broadcast(new ProductStatusUpdated(
                    (int) $id,
                    $localForBroadcast->local_id,
                    $newStatus,
                    $validated['nombre']
                ))->toOthers();
            }
        }

        // Si es gerente, actualizar también el precio en tblocal_product
        if ($localId) {
            DB::table('tblocal_product')
                ->where('local_id', $localId)
                ->where('product_id', $id)
                ->update([
                    'price' => $validated['precio'],
                    'updated_at' => now()
                ]);
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => '✓ Producto actualizado exitosamente',
                'product' => $updatedProduct
            ]);
        }

        return redirect()->route('products.index')
            ->with('success', '✓ Producto actualizado exitosamente');
    }

    /**
     * Eliminar un Producto
     */
    public function destroy(Request $request, $id)
    {
        if (!$this->canAccessProduct($id)) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'No tienes permiso para eliminar este producto'], 403);
            }
            return redirect()->route('products.index')->with('error', 'No tienes permiso para eliminar este producto.');
        }

        $product = $this->productData->find($id);

        if (!$product) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Producto no encontrado'], 404);
            }
            return redirect()->route('products.index')->with('error', 'Producto no encontrado');
        }

        // Eliminar también la galería de imágenes (elimina archivos también)
        $gallery = $this->galleryData->getByProductId($id);
        foreach ($gallery as $item) {
            $oldPath = public_path(str_replace('public/', '', $item->image_url));
            if (File::exists($oldPath)) {
                File::delete($oldPath);
            }
        }
        $this->galleryData->deleteByProductId($id);

        // Eliminar foto principal si existe
        if ($product->photo) {
            $oldPath = public_path(str_replace('public/', '', $product->photo));
            if (File::exists($oldPath)) {
                File::delete($oldPath);
            }
        }
        
        // Eliminar el producto
        $this->productData->delete($id);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => '✓ Producto eliminado exitosamente'
            ]);
        }

        return redirect()->route('products.index')
            ->with('success', '✓ Producto eliminado exitosamente');
    }

    /**
     * Agregar imagen a la galería de un producto
     */
    public function addGalleryImage(Request $request, $id)
    {
        if (!$this->canAccessProduct($id)) {
            return redirect()->route('products.index')
                ->with('error', 'No tienes permiso para agregar imágenes a este producto.');
        }

        $product = $this->productData->find($id);

        if (!$product) {
            return redirect()->route('products.gallery', $id)
                ->with('error', 'Producto no encontrado');
        }

        $validated = $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ], [
            'image.required' => 'Debe seleccionar una imagen',
            'image.image' => 'El archivo debe ser una imagen',
            'image.mimes' => 'La imagen debe ser JPG, PNG o GIF',
            'image.max' => 'La imagen no puede ser mayor a 2MB'
        ]);

        try {
            // Procesar la imagen
            if ($request->hasFile('image')) {
                // Crear directorio si no existe
                $galleryDir = public_path('images/products/gallery');
                if (!File::isDirectory($galleryDir)) {
                    File::makeDirectory($galleryDir, 0755, true, true);
                }

                $file = $request->file('image');
                $filename = 'gallery_' . Str::slug($product->name) . '_' . time() . '_' . rand(1000, 9999) . '.' . $file->getClientOriginalExtension();
                $file->move($galleryDir, $filename);
                $imagePath = 'images/products/gallery/' . $filename;
                
                // Agregar a la galería
                $this->galleryData->add($id, $imagePath);
            }

            return redirect()->route('products.gallery', $id)
                ->with('success', '✓ Imagen agregada exitosamente a la galería');
        } catch (\Exception $e) {
            return redirect()->route('products.gallery', $id)
                ->with('error', 'Error al subir la imagen: ' . $e->getMessage());
        }
    }

    /**
     * Eliminar imagen de la galería
     */
    public function removeGalleryImage(Request $request, $galleryId)
    {
        try {
            // Obtener la imagen antes de eliminarla
            $galleryItem = \App\Models\ProductGallery::find($galleryId);
            
            if ($galleryItem && $galleryItem->image_url) {
                $oldPath = public_path(str_replace('public/', '', $galleryItem->image_url));
                if (File::exists($oldPath)) {
                    File::delete($oldPath);
                }
            }

            $deleted = $this->galleryData->delete($galleryId);

            if (!$deleted) {
                return redirect()->back()
                    ->with('error', 'Imagen no encontrada');
            }

            return redirect()->back()
                ->with('success', '✓ Imagen eliminada de la galería');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al eliminar la imagen: ' . $e->getMessage());
        }
    }

    /**
     * Mapear estado de español a inglés
     */
    private function mapStatusToEnglish($status)
    {
        $map = [
            'Disponible' => 'Available',
            'No disponible' => 'Unavailable',
            'Activo' => 'Available',
            'Inactivo' => 'Unavailable'
        ];

        return $map[$status] ?? $status;
    }

    /**
     * Verificar si el usuario tiene acceso al producto
     * Un gerente solo puede acceder a productos de su local
     */
    private function canAccessProduct($productId)
    {
        $user = auth()->user();

        // Los administradores globales pueden acceder a todos
        if ($user->isAdminGlobal()) {
            return true;
        }

        // Los gerentes solo pueden acceder a productos de su local
        if ($user->isAdminLocal()) {
            $local = $user->locals()->first();
            if (!$local) {
                return false;
            }

            // Verificar si el producto está en el local del gerente
            return \App\Models\Product::byLocal($local->local_id)
                ->where('product_id', $productId)
                ->exists();
        }

        return false;
    }
}
