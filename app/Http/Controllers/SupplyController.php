<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Data\SupplyData;
use App\Data\SupplierData;

class SupplyController extends Controller
{
    protected $supplyData;
    protected $supplierData;

    public function __construct(SupplyData $supplyData, SupplierData $supplierData)
    {
        $this->supplyData = $supplyData;
        $this->supplierData = $supplierData;
    }

    /**
     * Mostrar lista de insumos con filtros
     */
    public function index(Request $request)
    {
        // Mapear nombres de filtros de español a inglés
        $filters = [
            'search' => $request->input('buscar'), 
            'status' => $this->mapStatusToEnglish($request->input('estado')), 
            'stock' => $this->mapStockFilter($request->input('stock')), 
            'expiration' => $this->mapExpirationFilter($request->input('vencimiento')) 
        ];

        $supplies = $this->supplyData->all($filters);
        $totals = $this->supplyData->countTotals();
        // Solo cargar nombre e ID de proveedor para el modal de crear
        $suppliers = $this->supplierData->allActiveMinimal();

        return view('supplies.index', compact('supplies', 'totals', 'suppliers'));
    }

    /**
     * Mostrar detalles de un insumo
     */
    public function show($id)
    {
        $supply = $this->supplyData->find($id);
        
        if (!$supply) {
            return redirect()->route('supplies.index')
                ->with('warning', 'Insumo no encontrado.');
        }
        
        return view('supplies.show', compact('supply'));
    }

    /**
     * Mostrar formulario de creación de insumo
     */
    public function create()
    {
        // Proveedores activos mínimos para checkboxes
        $suppliers = $this->supplierData->allActiveMinimal();
        return view('supplies.create', compact('suppliers'));
    }

    /**
     * Crear nuevo insumo
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'unidad_medida' => 'required|string|max:50',
            'stock_actual' => 'required|integer|min:0',
            'stock_minimo' => 'required|integer|min:0',
            'cantidad' => 'required|integer|min:1',
            'precio' => 'required|numeric|min:0',
            'fecha_vencimiento' => 'nullable|date',
            'estado' => 'required|string',
        ]);

        // Mapear datos de español a inglés
        $data = [
            'name' => $validated['nombre'],
            'unit_of_measure' => $validated['unidad_medida'],
            'current_stock' => $validated['stock_actual'],
            'minimum_stock' => $validated['stock_minimo'],
            'quantity' => $validated['cantidad'],
            'price' => $validated['precio'],
            'expiration_date' => $validated['fecha_vencimiento'],
            'status' => $this->mapStatusToEnglish($validated['estado'])
        ];

        $suppliers = $request->input('proveedores', []);
        $id = $this->supplyData->create($data, $suppliers);

        // Si es una petición AJAX, devolver JSON
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Insumo creado exitosamente',
                'id' => $id
            ]);
        }

        return redirect()->route('supplies.index')
            ->with('success', 'Insumo creado exitosamente');
    }

    /**
     * Actualizar insumo existente
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'unidad_medida' => 'required|string|max:50',
            'stock_actual' => 'required|integer|min:0',
            'stock_minimo' => 'required|integer|min:0',
            'cantidad' => 'required|integer|min:1',
            'precio' => 'required|numeric|min:0',
            'fecha_vencimiento' => 'nullable|date',
            'estado' => 'required|string',
        ]);

        // Mapear datos de español a inglés
        $data = [
            'name' => $validated['nombre'],
            'unit_of_measure' => $validated['unidad_medida'],
            'current_stock' => $validated['stock_actual'],
            'minimum_stock' => $validated['stock_minimo'],
            'quantity' => $validated['cantidad'],
            'price' => $validated['precio'],
            'expiration_date' => $validated['fecha_vencimiento'],
            'status' => $this->mapStatusToEnglish($validated['estado'])
        ];

        $suppliers = $request->input('proveedores', null);
        $this->supplyData->update($id, $data, $suppliers);

        // Si es una petición AJAX, devolver JSON
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Insumo actualizado exitosamente'
            ]);
        }

        return redirect()->route('supplies.index')
            ->with('success', 'Insumo actualizado exitosamente');
    }

    /**
     * Eliminar insumo existente
     */
    public function destroy($id)
    {
        $this->supplyData->delete($id);
        
        // Si es una petición AJAX, devolver JSON
        if (request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Insumo eliminado exitosamente'
            ]);
        }
        
        return redirect()->route('supplies.index')
            ->with('success', 'Insumo eliminado exitosamente');
    }

    /**
     * Cargar contenido del modal de detalles
     */
    public function showModal($id)
    {
        // Solo cargar el insumo con datos completos de proveedores
        $supply = $this->supplyData->findForModal($id);
        
        if (!$supply) {
            return response()->json(['error' => 'Insumo no encontrado'], 404);
        }
        
        return view('supplies.partials.show-modal', compact('supply'));
    }

    /**
     * Cargar contenido del modal de editar
     */
    public function editModal($id)
    {
        // Solo cargar el insumo con IDs de suppliers 
        $supply = $this->supplyData->findForEdit($id);
        
        if (!$supply) {
            return response()->json(['error' => 'Insumo no encontrado'], 404);
        }
        
        // Solo cargar nombre e ID de proveedor activos 
        $suppliers = $this->supplierData->allActiveMinimal();
        
        return view('supplies.partials.edit-modal', compact('supply', 'suppliers'));
    }

    /**
     * Mapear estado de español a inglés
     */
    private function mapStatusToEnglish($status)
    {
        if (!$status) return null;
        
        $statusMap = [
            'Disponible' => 'Available',
            'Agotado' => 'Out of Stock',
            'Vencido' => 'Expired'
        ];
        
        return $statusMap[$status] ?? $status;
    }

    /**
     * Mapear filtro de stock
     */
    private function mapStockFilter($stock)
    {
        if (!$stock) return null;
        
        $stockMap = [
            'bajo' => 'low'
        ];
        
        return $stockMap[$stock] ?? $stock;
    }

    /**
     * Mapear filtro de vencimiento
     */
    private function mapExpirationFilter($expiration)
    {
        if (!$expiration) return null;
        
        $expirationMap = [
            'por_vencer' => 'expiring_soon',
            'vencidos' => 'expired',
            'buenos' => 'good'
        ];
        
        return $expirationMap[$expiration] ?? $expiration;
    }
}
