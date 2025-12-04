<?php

namespace App\Data;

use App\Models\Supply;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SupplyData
{
    protected $table = 'tbsupplies';
    protected $pivot = 'tbsupplier_supply';

    /**
     * Obtener todos los Insumos con filtros opcionales
     */
    public function all(array $filters = [])
    {
        // Solo cargar conteo de Proveedores, no todos los datos
        $query = Supply::select('tbsupplies.*')
            ->selectRaw('(select count(*) from `tbsuppliers` inner join `tbsupplier_supply` on `tbsuppliers`.`supplier_id` = `tbsupplier_supply`.`supplier_id` where `tbsupplies`.`supply_id` = `tbsupplier_supply`.`supply_id`) as `suppliers_count`');

        // Filtro de búsqueda por nombre
        if (!empty($filters['search'])) {
            $query->search($filters['search']);
        }

        // Filtro por estado
        if (!empty($filters['status'])) {
            $query->byStatus($filters['status']);
        }

        // Filtro por stock
        if (!empty($filters['stock'])) {
            if ($filters['stock'] === 'low') {
                $query->lowStock();
            }
        }

        // Filtro por vencimiento
        if (!empty($filters['expiration'])) {
            switch ($filters['expiration']) {
                case 'expiring_soon':
                    $query->expiringSoon();
                    break;
                case 'expired':
                    $query->expired();
                    break;
                case 'good':
                    $query->goodCondition();
                    break;
            }
        }

        // Paginación y mantener query string para conservar filtros
        return $query->orderBy('supply_id', 'desc')
            ->paginate(6)
            ->withQueryString();
    }

    /**
     * Obtener todos los insumos 
     */
    public function allMinimal()
    {
        // Incluir precio porque algunas vistas de proveedores muestran nombre y precio
        return Supply::select('supply_id', 'name', 'price')
            ->orderBy('name')
            ->get();
    }

    /**
     * Buscar insumo por ID
     */
    public function find($id)
    {
        return Supply::with('suppliers')->find($id);
    }

    /**
     * Buscar insumo por ID para modal de ver (detalles completos)
     */
    public function findForModal($id)
    {
        return Supply::with(['suppliers' => function($query) {
            $query->select('tbsuppliers.supplier_id', 'tbsuppliers.name', 'tbsuppliers.phone', 'tbsuppliers.email');
        }])->find($id);
    }

    /**
     * Buscar insumo por ID para modal de editar 
     */
    public function findForEdit($id)
    {
        return Supply::with(['suppliers:supplier_id'])->find($id);
    }

    /**
     * Crear nuevo Insumo
     */
    public function create(array $data, array $suppliers = [])
    {
        $supply = Supply::create($data);
        
        if (count($suppliers) > 0) {
            $supply->suppliers()->attach($suppliers);
        }
        
        return $supply->supply_id;
    }

    /**
     * Actualizar Insumo existente
     */
    public function update($id, array $data, array $suppliers = null)
    {
        $supply = Supply::findOrFail($id);
        $supply->update($data);

        // Si se proporciona array de proveedores, sincronizar
        if (is_array($suppliers)) {
            $supply->suppliers()->sync($suppliers);
        }

        return $supply->fresh('suppliers');
    }

    /**
     * Eliminar Insumo
     */
    public function delete($id)
    {
        $supply = Supply::findOrFail($id);
        $supply->suppliers()->detach();
        return $supply->delete();
    }

    /**
     * Contar totales para dashboard/filtros
     */
    public function countTotals()
    {
        $totals = [];
        
        // Total de Insumos
        $totals['all'] = Supply::count();
        
        // Por estado
        $totals['available'] = Supply::where('status', 'Available')->count();
        $totals['out_of_stock'] = Supply::where('status', 'Out of Stock')->count();
        $totals['expired'] = Supply::where('status', 'Expired')->count();
        
        // Stock bajo
        $totals['low_stock'] = Supply::lowStock()->count();
        
        // Por vencer (próximos 30 días)
        $totals['expiring_soon'] = Supply::expiringSoon()->count();
        
        // Buenos (sin fecha de vencimiento o más de 30 días)
        $totals['good'] = Supply::goodCondition()->count();

        return $totals;
    }

    /**
     * Obtener Insumos con stock bajo
     */
    public function getLowStock()
    {
        return Supply::with('suppliers')
            ->lowStock()
            ->get();
    }

    /**
     * Obtener Insumos por vencer
     */
    public function getExpiringSoon()
    {
        return Supply::with('suppliers')
            ->expiringSoon()
            ->get();
    }

    /**
     * Obtener Insumos vencidos
     */
    public function getExpired()
    {
        return Supply::with('suppliers')
            ->expired()
            ->get();
    }

    /**
     * Actualizar stock de un Insumos
     */
    public function updateStock($id, $newStock)
    {
        $supply = Supply::findOrFail($id);
        
        $supply->current_stock = $newStock;
        
        // Actualizar estado automáticamente
        if ($newStock <= 0) {
            $supply->status = 'Out of Stock';
        } else if ($supply->status === 'Out of Stock') {
            $supply->status = 'Available';
        }
        
        $supply->save();
        
        return $supply;
    }

    /**
     * Verificar y actualizar estados de vencimiento
     */
    public function updateExpirationStatuses()
    {
        $now = Carbon::now();
        
        // Marcar como vencidos
        Supply::where('expiration_date', '<', $now)
            ->where('status', '!=', 'Expired')
            ->update(['status' => 'Expired']);
        
        return Supply::where('status', 'Expired')->count();
    }
}
