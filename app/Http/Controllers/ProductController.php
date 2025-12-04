<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Data\ProductData;
use App\Data\ProductGalleryData;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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

        $products = $this->productData->all($filters);
        $totals = $this->productData->countTotals();
        $categories = $this->productData->getAllCategories();

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
        $validated = $request->validate([
            'nombre' => 'required|string|max:255|unique:tbproduct,name',
            'descripcion' => 'nullable|string',
            'categoria' => 'nullable|string|max:100',
            'etiqueta' => 'nullable|string|max:100',
            'tipo_producto' => 'nullable|string|max:50',
            'precio' => 'required|numeric|min:0',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'estado' => 'required|string|in:Disponible,No disponible'
        ], [
            'nombre.required' => 'El nombre del producto es obligatorio',
            'nombre.unique' => 'Ya existe un producto con este nombre',
            'precio.required' => 'El precio es obligatorio',
            'precio.numeric' => 'El precio debe ser un número',
            'precio.min' => 'El precio no puede ser negativo',
            'foto.image' => 'El archivo debe ser una imagen',
            'foto.mimes' => 'La imagen debe ser JPG, PNG o GIF',
            'foto.max' => 'La imagen no puede ser mayor a 2MB',
            'estado.in' => 'El estado debe ser Disponible o No disponible'
        ]);

        // Procesar la foto si se envía
        $photoPath = null;
        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            $filename = Str::slug($validated['nombre']) . '_' . time() . '.' . $file->getClientOriginalExtension();
            $photoPath = $file->storeAs('products', $filename, 'public');
        }

        $data = [
            'name' => $validated['nombre'],
            'description' => $validated['descripcion'] ?? null,
            'category' => $validated['categoria'] ?? null,
            'tag' => $validated['etiqueta'] ?? null,
            'product_type' => $validated['tipo_producto'] ?? null,
            'price' => $validated['precio'],
            'photo' => $photoPath ? '/storage/' . $photoPath : null,
            'status' => $this->mapStatusToEnglish($validated['estado'])
        ];

        $product = $this->productData->create($data);

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
        $product = $this->productData->find($id);

        if (!$product) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Producto no encontrado'], 404);
            }
            return redirect()->route('products.index')->with('error', 'Producto no encontrado');
        }

        $validated = $request->validate([
            'nombre' => 'required|string|max:255|unique:tbproduct,name,' . $id . ',product_id',
            'descripcion' => 'nullable|string',
            'categoria' => 'nullable|string|max:100',
            'etiqueta' => 'nullable|string|max:100',
            'tipo_producto' => 'nullable|string|max:50',
            'precio' => 'required|numeric|min:0',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'estado' => 'required|string|in:Disponible,No disponible'
        ], [
            'nombre.required' => 'El nombre del producto es obligatorio',
            'nombre.unique' => 'Ya existe otro producto con este nombre',
            'precio.required' => 'El precio es obligatorio',
            'precio.numeric' => 'El precio debe ser un número',
            'precio.min' => 'El precio no puede ser negativo',
            'foto.image' => 'El archivo debe ser una imagen',
            'foto.mimes' => 'La imagen debe ser JPG, PNG o GIF',
            'foto.max' => 'La imagen no puede ser mayor a 2MB',
            'estado.in' => 'El estado debe ser Disponible o No disponible'
        ]);

        // Procesar la foto si se envía
        $photoPath = $product->photo; // Mantener la foto actual por defecto
        if ($request->hasFile('foto')) {
            // Eliminar foto anterior si existe
            if ($product->photo && Storage::disk('public')->exists(str_replace('/storage/', '', $product->photo))) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $product->photo));
            }
            
            $file = $request->file('foto');
            $filename = Str::slug($validated['nombre']) . '_' . time() . '.' . $file->getClientOriginalExtension();
            $photoPath = '/storage/' . $file->storeAs('products', $filename, 'public');
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

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => '✓ Producto actualizado exitosamente',
                'product' => $updatedProduct
            ]);
        }

        return redirect()->route('products.show', $id)
            ->with('success', '✓ Producto actualizado exitosamente');
    }

    /**
     * Eliminar un Producto
     */
    public function destroy(Request $request, $id)
    {
        $product = $this->productData->find($id);

        if (!$product) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Producto no encontrado'], 404);
            }
            return redirect()->route('products.index')->with('error', 'Producto no encontrado');
        }

        // Eliminar también la galería de imágenes
        $this->galleryData->deleteByProductId($id);
        
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
                $file = $request->file('image');
                $filename = 'gallery_' . Str::slug($product->name) . '_' . time() . '.' . $file->getClientOriginalExtension();
                $imagePath = '/storage/' . $file->storeAs('products/gallery', $filename, 'public');
                
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
}
