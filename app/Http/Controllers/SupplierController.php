<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Data\SupplierData;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class SupplierController extends Controller
{
    protected $supplierData;

    public function __construct(SupplierData $supplierData)
    {
        $this->supplierData = $supplierData;
    }

    /**
     * Mostrar lista de Proveedores con filtros
     */
    public function index(Request $request)
    {
        $filters = [
            'search' => $request->input('buscar'),
            'sort_by' => $request->input('sort_by', 'recent')
        ];

        // Si el usuario es gerente, filtrar por su local
        $user = auth()->user();
        if ($user->isAdminLocal()) {
            // Obtener el primer local del gerente
            $local = $user->locals()->first();
            if ($local) {
                $filters['local_id'] = $local->local_id;
                $suppliers = $this->supplierData->all($filters);
                $totals = $this->supplierData->countTotalsByLocal($local->local_id);
            } else {
                // Si el gerente no tiene local asignado, retornar vacío
                $suppliers = [];
                $totals = ['total' => 0];
            }
        } else {
            // Admin global ve todos los proveedores
            $suppliers = $this->supplierData->all($filters);
            $totals = $this->supplierData->countTotals();
        }

        $currentSort = $request->input('sort_by', 'recent');
        return view('suppliers.index', compact('suppliers', 'totals', 'currentSort'));
    }

    /**
     * Mostrar formulario para crear nuevo Proveedor
     */
    public function create()
    {
        return view('suppliers.create');
    }

    /**
     * Crear nuevo Proveedor
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
    'nombre' => ['required', 'string', 'max:255', 'unique:tbsupplier,name', 'regex:/^[A-Za-zÁÉÍÓÚáéíóúñÑ ]+$/'],
    'telefono' => ['required', 'digits:8'],
    'email' => ['required', 'email', 'max:255', 'unique:tbsupplier,email', 'regex:/^[a-zA-Z0-9._%+\-]+@gmail\.com$/'],
    'imagenes' => 'required|array|min:1',
    'imagenes.*' => 'required|file|mimes:jpeg,png,jpg,pdf|max:5120'
], [
    'nombre.required' => 'El nombre del proveedor es obligatorio',
    'nombre.regex' => 'El nombre solo puede contener letras',
    'nombre.unique' => 'Ya existe un proveedor con este nombre',
    'telefono.required' => 'El teléfono es obligatorio',
    'telefono.digits' => 'El teléfono debe tener exactamente 8 números y no permite letras',
    'email.required' => 'El email es obligatorio',
    'email.email' => 'El email debe ser válido',
    'email.unique' => 'Ya existe un proveedor con este email',
    'email.regex' => 'El correo debe ser @gmail.com',
    'imagenes.required' => 'Debe adjuntar al menos una imagen o PDF de factura',
    'imagenes.array' => 'Las imágenes deben ser una lista',
    'imagenes.min' => 'Debe adjuntar al menos una imagen o PDF',
    'imagenes.*.mimes' => 'Los archivos deben ser: JPEG, PNG, JPG o PDF',
    'imagenes.*.max' => 'Cada archivo no debe superar 5MB'
]);
        $data = [
            'name' => $validated['nombre'],
            'phone' => $validated['telefono'],
            'email' => $validated['email']
        ];

        $supplier = $this->supplierData->create($data);

        // Procesar imágenes/archivos
        if ($request->hasFile('imagenes')) {
    $uploadDir = public_path('images/proveedor');
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    foreach ($request->file('imagenes') as $file) {
        $extension = $file->getClientOriginalExtension();
        $filename = time() . '_' . uniqid() . '.' . $extension;
        $file->move($uploadDir, $filename);

        DB::table('tbsupplier_gallery')->insert([
            'supplier_id' => $supplier->supplier_id,
            'image_path' => 'images/proveedor/' . $filename,
            'description' => $file->getClientOriginalName(),
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }
}
        // Si es gerente, crear automáticamente la relación con su local
        $user = auth()->user();
        if ($user->isAdminLocal()) {
            $local = $user->locals()->first();
            if ($local) {
                // Crear relación en tb_local_supplier
                DB::table('tblocal_supplier')->insert([
                    'local_id' => $local->local_id,
                    'supplier_id' => $supplier->supplier_id,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        }

        return redirect()->route('suppliers.index')
            ->with('success', 'Proveedor creado exitosamente con galería de facturas');
    }

    /**
     * Mostrar detalles de un Proveedor
     */
    public function show($id)
    {
        $supplier = $this->supplierData->find($id);

        if (!$supplier) {
            return redirect()->route('suppliers.index')
                ->with('error', 'Proveedor no encontrado');
        }

        // Verificar acceso
        if (!$this->canAccessSupplier($id)) {
            return redirect()->route('suppliers.index')
                ->with('error', 'No tienes acceso a este proveedor');
        }

        return view('suppliers.show', compact('supplier'));
    }

    /**
     * Verificar si el usuario actual tiene acceso a un proveedor
     */
    private function canAccessSupplier($supplierId)
    {
        $user = auth()->user();

        // Admin global tiene acceso a todo
        if ($user->role_id == 1) { // Admin global
            return true;
        }

        // Si es gerente, verificar que el proveedor pertenece a su local
        if ($user->isAdminLocal()) {
            $local = $user->locals()->first();
            if ($local) {
                return DB::table('tblocal_supplier')
                    ->where('local_id', $local->local_id)
                    ->where('supplier_id', $supplierId)
                    ->exists();
            }
            return false;
        }

        return false;
    }


    /**
 * Mostrar formulario para editar proveedor
 */
public function edit($id)
{
    $supplier = $this->supplierData->find($id);

    if (!$supplier) {
        return redirect()->route('suppliers.index')
            ->with('error', 'Proveedor no encontrado');
    }

    if (!$this->canAccessSupplier($id)) {
        return redirect()->route('suppliers.index')
            ->with('error', 'No tienes acceso a este proveedor');
    }

    return view('suppliers.edit', compact('supplier'));
}

/**
 * Actualizar proveedor
 */
public function update(Request $request, $id)
{
    $supplier = $this->supplierData->find($id);

    if (!$supplier) {
        return redirect()->route('suppliers.index')
            ->with('error', 'Proveedor no encontrado');
    }

    if (!$this->canAccessSupplier($id)) {
        return redirect()->route('suppliers.index')
            ->with('error', 'No tienes acceso a este proveedor');
    }

    $validated = $request->validate([
    'nombre' => [
        'required',
        'string',
        'max:255',
        'regex:/^[A-Za-zÁÉÍÓÚáéíóúñÑ ]+$/',
        'unique:tbsupplier,name,' . $id . ',supplier_id'
    ],
    'telefono' => [
        'required',
        'digits:8',
        'unique:tbsupplier,phone,' . $id . ',supplier_id'
    ],
    'email' => [
        'required',
        'email',
        'max:255',
        'regex:/^[a-zA-Z0-9._%+\-]+@gmail\.com$/',
        'unique:tbsupplier,email,' . $id . ',supplier_id'
    ],
    'imagenes' => ['nullable', 'array'],
    'imagenes.*' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
], [
    'nombre.required' => 'El nombre del proveedor es obligatorio',
    'nombre.regex' => 'El nombre solo puede contener letras y espacios',
    'nombre.unique' => 'Ya existe un proveedor con ese nombre',

    'telefono.required' => 'El teléfono es obligatorio',
    'telefono.digits' => 'El teléfono debe tener exactamente 8 números y no permite letras',
    'telefono.unique' => 'Ya existe un proveedor con ese teléfono',

    'email.required' => 'El correo electrónico es obligatorio',
    'email.email' => 'El correo electrónico no es válido',
    'email.regex' => 'El correo debe ser @gmail.com',
    'email.unique' => 'Ya existe un proveedor con ese correo',

    'imagenes.*.mimes' => 'Los archivos deben ser JPG, JPEG, PNG o PDF',
    'imagenes.*.max' => 'Cada archivo no debe superar los 5MB',
]);
    $data = [
        'name' => $validated['nombre'],
        'phone' => $validated['telefono'],
        'email' => $validated['email'],
    ];

    $this->supplierData->update($id, $data);

    // Agregar nuevas facturas/archivos si se subieron
    if ($request->hasFile('imagenes')) {
        $uploadDir = public_path('images/proveedor');

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        foreach ($request->file('imagenes') as $file) {
            $extension = $file->getClientOriginalExtension();
            $filename = time() . '_' . uniqid() . '.' . $extension;
            $file->move($uploadDir, $filename);

            DB::table('tbsupplier_gallery')->insert([
                'supplier_id' => $supplier->supplier_id,
                'image_path' => 'images/proveedor/' . $filename,
                'description' => $file->getClientOriginalName(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    return redirect()->route('suppliers.show', $id)
        ->with('success', 'Proveedor actualizado exitosamente');
}

/**
 * Eliminar proveedor
 */
public function destroy($id)
{
    $supplier = $this->supplierData->find($id);

    if (!$supplier) {
        return redirect()->route('suppliers.index')
            ->with('error', 'Proveedor no encontrado');
    }

    if (!$this->canAccessSupplier($id)) {
        return redirect()->route('suppliers.index')
            ->with('error', 'No tienes acceso a este proveedor');
    }

    // Eliminar archivos físicos de la galería
    if ($supplier->gallery && count($supplier->gallery) > 0) {
        foreach ($supplier->gallery as $item) {
            if (!empty($item->image_path)) {
                $fullPath = public_path($item->image_path);
                if (File::exists($fullPath)) {
                    File::delete($fullPath);
                }
            }
        }
    }

    // Eliminar galería
    DB::table('tbsupplier_gallery')
        ->where('supplier_id', $id)
        ->delete();

    // Eliminar relación con locales si existe
    DB::table('tblocal_supplier')
        ->where('supplier_id', $id)
        ->delete();

    // Eliminar proveedor
    $this->supplierData->delete($id);

    return redirect()->route('suppliers.index')
        ->with('success', 'Proveedor eliminado exitosamente');
}


public function storeGallery(Request $request, $id)
{
    $supplier = $this->supplierData->find($id);

    if (!$supplier) {
        return redirect()->route('suppliers.index')
            ->with('error', 'Proveedor no encontrado');
    }

    if (!$this->canAccessSupplier($id)) {
        return redirect()->route('suppliers.index')
            ->with('error', 'No tienes acceso a este proveedor');
    }

    $validated = $request->validate([
        'imagenes' => ['required', 'array', 'min:1'],
        'imagenes.*' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
    ], [
        'imagenes.required' => 'Debe adjuntar al menos una imagen o PDF de factura',
        'imagenes.array' => 'El formato de archivos no es válido',
        'imagenes.min' => 'Debe adjuntar al menos una imagen o PDF de factura',
        'imagenes.*.mimes' => 'Los archivos deben ser JPG, JPEG, PNG o PDF',
        'imagenes.*.max' => 'Cada archivo no debe superar los 5MB',
    ]);

    $uploadDir = public_path('images/proveedor');

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    foreach ($request->file('imagenes') as $file) {
        $extension = $file->getClientOriginalExtension();
        $filename = time() . '_' . uniqid() . '.' . $extension;
        $file->move($uploadDir, $filename);

        DB::table('tbsupplier_gallery')->insert([
            'supplier_id' => $supplier->supplier_id,
            'image_path' => 'images/proveedor/' . $filename,
            'description' => $file->getClientOriginalName(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    return redirect()->route('suppliers.show', $id)
        ->with('success', 'Facturas agregadas exitosamente');
}
    








    
}

