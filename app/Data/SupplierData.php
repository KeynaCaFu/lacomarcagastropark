<?php

namespace App\Data;

use App\Models\Supplier;
use Illuminate\Support\Facades\DB;

class SupplierData
{
    protected $table = 'tbsuppliers';
    protected $pivot = 'tbsupplier_supply';

    /**
     * Obtener todos los Proveedores con filtros opcionales
     */
    public function all(array $filters = [])
    {
        // Solo cargar conteo de Insumos, no todos los datos
        $query = Supplier::select('tbsuppliers.*')
            ->selectRaw('(select count(*) from `tbsupplies` inner join `tbsupplier_supply` on `tbsupplies`.`supply_id` = `tbsupplier_supply`.`supply_id` where `tbsuppliers`.`supplier_id` = `tbsupplier_supply`.`supplier_id`) as `supplies_count`');

        // Filtro de búsqueda por nombre
        if (!empty($filters['search'])) {
            $query->search($filters['search']);
        }

        // Filtro por estado
        if (!empty($filters['status'])) {
            $query->byStatus($filters['status']);
        }

        // Paginación de 6 por página y mantener query string para filtros
        return $query->orderBy('supplier_id', 'desc')->paginate(6)->withQueryString();
    }

    /**
     * Obtener solo Proveedores activos
     */
    public function allActive()
    {
        return Supplier::active()->with('supplies')->get();
    }

    /**
     * Obtener solo Proveedores activos con datos mínimos 
     */
    public function allActiveMinimal()
    {
        return Supplier::active()
            ->select('supplier_id', 'name')
            ->orderBy('name')
            ->get();
    }

    /**
     * Buscar Proveedor por ID
     */
    public function find($id)
    {
        return Supplier::with('supplies')->find($id);
    }

    /**
     * Buscar Proveedor por ID para modal de ver (detalles completos)
     */
    public function findForModal($id)
    {
        return Supplier::with(['supplies' => function($query) {
            $query->select('tbsupplies.supply_id', 'tbsupplies.name', 'tbsupplies.unit_of_measure', 
                          'tbsupplies.current_stock', 'tbsupplies.minimum_stock', 'tbsupplies.price', 'tbsupplies.status');
        }])->find($id);
    }

    /**
     * Buscar Proveedor por ID para modal de editar 
     */
    public function findForEdit($id)
    {
        return Supplier::with(['supplies:supply_id'])->find($id);
    }

    /**
     * Crear nuevo Proveedor
     */
    public function create(array $data, array $supplies = [])
    {
        $supplier = Supplier::create($data);
        
        // Asociar insumos si se proporcionaron
        if (count($supplies) > 0) {
            $supplier->supplies()->attach($supplies);
        }
        
        return $supplier->supplier_id;
    }

    /**
     * Actualizar Proveedor existente
     */
    public function update($id, array $data, array $supplies = null)
    {
        $supplier = Supplier::findOrFail($id);
        $supplier->update($data);
        
        // Sincronizar insumos si se proporcionaron
        if (is_array($supplies)) {
            $supplier->supplies()->sync($supplies);
        }
        
        return $supplier->fresh('supplies');
    }

    /**
     * Eliminar Proveedor
     */
    public function delete($id)
    {
        $supplier = Supplier::findOrFail($id);
        // Desvincular insumos para evitar restricciones de FK y luego eliminar definitivamente
        $supplier->supplies()->detach();
        return $supplier->delete();
    }

    /**
     * Crear un snapshot para poder restaurar tras eliminar.
     * Contiene los atributos del Proveedor y los IDs de sus insumos.
     */
    public function snapshotForRestore($id)
    {
        $supplier = Supplier::with(['supplies:supply_id'])->findOrFail($id);
        $supplierData = [
            'supplier_id' => $supplier->supplier_id,
            'name' => $supplier->name,
            'email' => $supplier->email ?? null,
            'phone' => $supplier->phone ?? null,
            'address' => $supplier->address ?? null,
            'status' => $supplier->status ?? null,
            'created_at' => $supplier->created_at,
            'updated_at' => $supplier->updated_at,
        ];

        return [
            'supplier' => $supplierData,
            'supply_ids' => $supplier->supplies->pluck('supply_id')->all(),
        ];
    }

    /**
     * Recrear un Proveedor a partir de un snapshot y re-asociar sus insumos.
     */
    public function recreateFromSnapshot(array $snapshot)
    {
        $data = $snapshot['supplier'] ?? [];
        $supplyIds = $snapshot['supply_ids'] ?? [];

        // Crear el registro con el mismo supplier_id si es posible
        $supplier = new Supplier();
        foreach ($data as $key => $value) {
            $supplier->setAttribute($key, $value);
        }
        $supplier->save();

        if (!empty($supplyIds)) {
            $supplier->supplies()->attach($supplyIds);
        }

        return $supplier->fresh('supplies');
    }

    /**
     * Contar totales para dashboard/filtros
     */
    public function countTotals()
    {
        $totals = [];
        
        // Total de Proveedores
        $totals['all'] = Supplier::count();
        
        // Por estado
        $totals['active'] = Supplier::where('status', 'Active')->count();
        $totals['inactive'] = Supplier::where('status', 'Inactive')->count();
        
        // Proveedores con insumos
        $totals['with_supplies'] = Supplier::has('supplies')->count();
        
        // Proveedores sin insumos
        $totals['without_supplies'] = Supplier::doesntHave('supplies')->count();

        return $totals;
    }

    /**
     * Obtener Proveedores sin insumos asociados
     */
    public function getWithoutSupplies()
    {
        return Supplier::doesntHave('supplies')->get();
    }

    /**
     * Obtener top Proveedores por total de compras
     */
    public function getTopByPurchases($limit = 10)
    {
        return Supplier::with('supplies')
            ->orderBy('total_purchases', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Incrementar total de compras de un Proveedor
     */
    public function incrementTotalPurchases($id, $amount)
    {
        $supplier = Supplier::findOrFail($id);
        $supplier->total_purchases += $amount;
        $supplier->save();
        
        return $supplier;
    }

    /**
     * Asociar insumo a Proveedor
     */
    public function attachSupply($supplierId, $supplyId)
    {
        $supplier = Supplier::findOrFail($supplierId);
        
        if (!$supplier->supplies()->where('supply_id', $supplyId)->exists()) {
            $supplier->supplies()->attach($supplyId);
            return true;
        }
        
        return false;
    }

    /**
     * Desasociar insumo de Proveedor
     */
    public function detachSupply($supplierId, $supplyId)
    {
        $supplier = Supplier::findOrFail($supplierId);
        $supplier->supplies()->detach($supplyId);
        
        return true;
    }

    /**
     * Obtener modelo de Proveedores para  personalizados
     */
    public function getModel()
    {
        return new Supplier();
    }
}
