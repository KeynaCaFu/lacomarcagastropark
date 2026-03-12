<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Data\SupplierData;
use Illuminate\Support\Facades\DB;

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
            'search' => $request->input('buscar')
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

        return view('suppliers.index', compact('suppliers', 'totals'));
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
            'nombre' => 'required|string|max:255|unique:tb_supplier,name',
            'telefono' => 'required|string|max:20',
            'email' => 'required|email|max:255|unique:tb_supplier,email',
            'imagenes' => 'required|array|min:1',
            'imagenes.*' => 'required|file|mimes:jpeg,png,jpg,pdf|max:5120'
        ], [
            'nombre.required' => 'El nombre del proveedor es obligatorio',
            'nombre.unique' => 'Ya existe un proveedor con este nombre',
            'telefono.required' => 'El teléfono es obligatorio',
            'email.required' => 'El email es obligatorio',
            'email.email' => 'El email debe ser válido',
            'email.unique' => 'Ya existe un proveedor con este email',
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
            $uploadDir = public_path('proveedor');
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            foreach ($request->file('imagenes') as $file) {
                $extension = $file->getClientOriginalExtension();
                $filename = time() . '_' . uniqid() . '.' . $extension;
                $file->move($uploadDir, $filename);

                // Guardar en bd
                DB::table('tb_supplier_gallery')->insert([
                    'supplier_id' => $supplier->supplier_id,
                    'image_path' => 'proveedor/' . $filename,
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
                DB::table('tb_local_supplier')->insert([
                    'local_id' => $local->local_id,
                    'supplier_id' => $supplier->supplier_id,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        }

        return redirect()->route('suppliers.index')
            ->with('success', '✓ Proveedor creado exitosamente con galería de facturas');
    }

    /**
     * Mostrar detalles de un Proveedor
     */
    public function show($id)
    {
        $supplier = $this->supplierData->find($id);

        if (!$supplier) {
            return redirect()->route('suppliers.index')
                ->with('error', '✗ Proveedor no encontrado');
        }

        // Verificar acceso
        if (!$this->canAccessSupplier($id)) {
            return redirect()->route('suppliers.index')
                ->with('error', '✗ No tienes acceso a este proveedor');
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
                return DB::table('tb_local_supplier')
                    ->where('local_id', $local->local_id)
                    ->where('supplier_id', $supplierId)
                    ->exists();
            }
            return false;
        }

        return false;
    }
}
