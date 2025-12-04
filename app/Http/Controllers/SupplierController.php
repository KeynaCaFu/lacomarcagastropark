<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Data\SupplierData;
use App\Data\SupplyData;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class SupplierController extends Controller
{
    protected $supplierData;
    protected $supplyData;

    public function __construct(SupplierData $supplierData, SupplyData $supplyData)
    {
        $this->supplierData = $supplierData;
        $this->supplyData = $supplyData;
    }

    /**
     * Mostrar lista de Proveedores con filtros
     */
    public function index(Request $request)
    {
        // Mapear nombres de filtros 
        $filters = [
            'search' => $request->input('buscar'), 
            'status' => $this->mapStatusToEnglish($request->input('estado')) 
        ];

        $suppliers = $this->supplierData->all($filters);
        $totals = $this->supplierData->countTotals();
        // Solo cargar nombre e ID de Insumos para el modal de crear
        $supplies = $this->supplyData->allMinimal();

        return view('suppliers.index', compact('suppliers', 'totals', 'supplies'));
    }

    /**
     * Mostrar detalles de un Proveedor
     */
    public function show($id)
    {
        $supplier = $this->supplierData->find($id);
        
        if (!$supplier) {
            return redirect()->route('suppliers.index')
                ->with('warning', 'Proveedor no encontrado.');
        }
        
        return view('suppliers.show', compact('supplier'));
    }

    /**
     * Crear nuevo Proveedor
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255|unique:tbsuppliers,name',
            'telefono' => 'nullable|string|max:20',
            'correo' => 'nullable|email|max:100|unique:tbsuppliers,email',
            'direccion' => 'nullable|string|max:500',
            'total_compras' => 'nullable|numeric|min:0',
            'estado' => 'required|string|in:Activo,Inactivo'
        ], [
            'nombre.required' => 'El nombre del proveedor es obligatorio',
            'nombre.unique' => 'Ya existe un proveedor con este nombre',
            'correo.email' => 'El formato del correo electrónico no es válido',
            'correo.unique' => 'Este correo ya está registrado para otro proveedor',
            'telefono.max' => 'El teléfono no puede tener más de 20 caracteres',
            'direccion.max' => 'La dirección no puede tener más de 500 caracteres',
            'total_compras.min' => 'El total de compras no puede ser negativo',
            'estado.in' => 'El estado debe ser Activo o Inactivo'
        ]);

        // Mapear datos de español a inglés
        $data = [
            'name' => $validated['nombre'],
            'phone' => $validated['telefono'] ?? null,
            'email' => $validated['correo'] ?? null,
            'address' => $validated['direccion'] ?? null,
            'total_purchases' => $validated['total_compras'] ?? 0,
            'status' => $this->mapStatusToEnglish($validated['estado'])
        ];

        $supplies = $request->input('insumos', []);
        
        // Advertencia si se crea proveedor inactivo con insumos
        if ($data['status'] === 'Inactive' && count($supplies) > 0) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'warning' => true,
                    'message' => '⚠️ Está creando un proveedor INACTIVO con insumos asociados. Se recomienda activarlo o no asignar insumos hasta que esté activo.'
                ], 422);
            }
        }
        
        $id = $this->supplierData->create($data, $supplies);

        // Si es una petición AJAX, devolver JSON
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Proveedor creado exitosamente',
                'id' => $id
            ]);
        }

        return redirect()->route('suppliers.index')
            ->with('success', 'Proveedor creado exitosamente');
    }

    /**
     * Actualizar Proveedor existente
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255|unique:tbsuppliers,name,' . $id . ',supplier_id',
            'telefono' => 'nullable|string|max:20',
            'correo' => 'nullable|email|max:100|unique:tbsuppliers,email,' . $id . ',supplier_id',
            'direccion' => 'nullable|string|max:500',
            'total_compras' => 'nullable|numeric|min:0',
            'estado' => 'required|string|in:Activo,Inactivo'
        ], [
            'nombre.required' => 'El nombre del proveedor es obligatorio',
            'nombre.unique' => 'Ya existe otro proveedor con este nombre',
            'correo.email' => 'El formato del correo electrónico no es válido',
            'correo.unique' => 'Este correo ya está registrado para otro proveedor',
            'telefono.max' => 'El teléfono no puede tener más de 20 caracteres',
            'direccion.max' => 'La dirección no puede tener más de 500 caracteres',
            'total_compras.min' => 'El total de compras no puede ser negativo',
            'estado.in' => 'El estado debe ser Activo o Inactivo'
        ]);

        // Mapear datos de español a inglés
        $data = [
            'name' => $validated['nombre'],
            'phone' => $validated['telefono'] ?? null,
            'email' => $validated['correo'] ?? null,
            'address' => $validated['direccion'] ?? null,
            'total_purchases' => $validated['total_compras'] ?? 0,
            'status' => $this->mapStatusToEnglish($validated['estado'])
        ];

        $supplies = $request->input('insumos', null);
        
        // Validar consistencia: proveedor inactivo con insumos asociados
        if ($data['status'] === 'Inactive' && $supplies !== null && count($supplies) > 0) {
            $message = '⚠️ Está estableciendo el proveedor como INACTIVO pero tiene insumos asociados. Esto puede causar inconsistencias. Se recomienda desasociar los insumos o mantener el proveedor activo.';
            
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'warning' => true,
                    'message' => $message
                ], 422);
            }
        }
        
        $this->supplierData->update($id, $data, $supplies);

        // Si es una petición AJAX, devolver JSON
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Proveedor actualizado exitosamente'
            ]);
        }

        return redirect()->route('suppliers.index')
            ->with('success', 'Proveedor actualizado exitosamente');
    }

    /**
     * Eliminar Proveedor
     */
    public function destroy($id)
    {
        // Verificar si el proveedor existe
        $supplier = $this->supplierData->find($id);
        
        if (!$supplier) {
            if (request()->wantsJson() || request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Proveedor no encontrado'
                ], 404);
            }
            return redirect()->route('suppliers.index')
                ->with('error', 'Proveedor no encontrado');
        }

        // Verificar si el proveedor está activo y tiene insumos asociados
        $hasActiveSupplies = $supplier->status === 'Active' && $supplier->supplies->count() > 0;
        
        if ($hasActiveSupplies) {
            // Advertir sobre dependencias antes de eliminar
            $message = "⚠️ Este proveedor activo tiene {$supplier->supplies->count()} insumo(s) asociado(s). Al eliminarlo, se desvincularán todos los insumos. ¿Está seguro de continuar?";
            
            // Si es solicitud AJAX, devolver advertencia para confirmación adicional
            if (request()->wantsJson() || request()->ajax()) {
                // Verificar si viene una confirmación explícita
                if (!request()->input('confirmed')) {
                    return response()->json([
                        'success' => false,
                        'requires_confirmation' => true,
                        'message' => $message,
                        'supplies_count' => $supplier->supplies->count()
                    ], 409);
                }
            }
        }

        // Crear snapshot antes de eliminar
        $snapshot = $this->supplierData->snapshotForRestore($id);
        $token = (string) Str::uuid();
        Cache::put('supplier_restore_' . $token, $snapshot, now()->addSeconds(10));

        // Eliminar definitivamente 
        $this->supplierData->delete($id);

        // Generar URL firmada temporal para restaurar (10 segundos) usando token
        $restoreUrl = URL::temporarySignedRoute('suppliers.restore', now()->addSeconds(10), ['token' => $token]);
        
        // Si es una petición AJAX, devolver JSON
        if (request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Proveedor eliminado exitosamente',
                'restore_url' => $restoreUrl
            ]);
        }

        return redirect()->route('suppliers.index')
            ->with('success', 'Proveedor eliminado exitosamente')
            ->with('restore_url', $restoreUrl);
    }

    /**
     * Restaurar Proveedor eliminado
     */
    public function restore(Request $request, $token)
    {
        // La ruta está protegida por middleware 'signed'
        $cacheKey = 'supplier_restore_' . $token;
        $snapshot = Cache::pull($cacheKey);

        if (!$snapshot) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'El enlace para deshacer ha expirado o ya fue usado.'
                ], 410);
            }
            return redirect()->route('suppliers.index')
                ->with('warning', 'El enlace para deshacer ha expirado o ya fue usado.');
        }

        $supplier = $this->supplierData->recreateFromSnapshot($snapshot);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Proveedor restaurado correctamente',
                'supplier_id' => $supplier->supplier_id,
            ]);
        }

        return redirect()->route('suppliers.index')
            ->with('success', 'Proveedor restaurado correctamente');
    }

    /**
     * Cargar contenido del modal de detalles
     */
    public function showModal($id)
    {
        // Solo cargar el Proveedor con datos mínimos de insumos
        $supplier = $this->supplierData->findForModal($id);
        
        if (!$supplier) {
            return response()->json(['error' => 'Proveedor no encontrado'], 404);
        }
        
        return view('suppliers.partials.show-modal', compact('supplier'));
    }

    /**
     * Cargar contenido del modal de editar
     */
    public function editModal($id)
    {
        // Solo cargar el Proveedor con IDs de insumos asociados
        $supplier = $this->supplierData->findForEdit($id);
        
        if (!$supplier) {
            return response()->json(['error' => 'Proveedor no encontrado'], 404);
        }
        
        // Solo cargar nombre e ID de insumos (no todos sus datos)
        $supplies = $this->supplyData->allMinimal();
        
        return view('suppliers.partials.edit-modal', compact('supplier', 'supplies'));
    }

    /**
     * Verificar si un email ya está registrado
     */
    public function checkEmail(Request $request)
    {
        $email = $request->input('email');
        $supplierId = $request->input('supplier_id');
        
        $query = $this->supplierData->getModel()->where('email', $email);
        
        // Si estamos editando, excluir el proveedor actual
        if ($supplierId) {
            $query->where('supplier_id', '!=', $supplierId);
        }
        
        $exists = $query->exists();
        
        return response()->json([
            'exists' => $exists,
            'message' => $exists ? 'Este correo ya está registrado' : 'Correo disponible'
        ]);
    }

    /**
     * Mapear estado de español a inglés
     */
    private function mapStatusToEnglish($status)
    {
        if (!$status) return null;
        
        $statusMap = [
            'Activo' => 'Active',
            'Inactivo' => 'Inactive'
        ];
        
        return $statusMap[$status] ?? $status;
    }
}
