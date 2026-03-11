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
            'nombre' => 'required|string|max:255',
            'telefono' => 'required|string|max:20',
            'email' => 'required|email|max:255'
        ], [
            'nombre.required' => 'El nombre del proveedor es obligatorio',
            'telefono.required' => 'El teléfono es obligatorio',
            'email.required' => 'El email es obligatorio',
            'email.email' => 'El email debe ser válido'
        ]);

        $data = [
            'name' => $validated['nombre'],
            'phone' => $validated['telefono'],
            'email' => $validated['email']
        ];

        $supplier = $this->supplierData->create($data);

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
            ->with('success', '✓ Proveedor creado exitosamente');
    }
}
