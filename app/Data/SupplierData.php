<?php

namespace App\Data;

use App\Models\Supplier;

class SupplierData
{
   
    protected $table = 'tbsupplier';

    /**
     * Obtener todos los Proveedores con filtros opcionales
     */
    public function all(array $filters = [])
    {
        $query = Supplier::select('tbsupplier.*')
        ->selectRaw('(select count(*) from `tbsupplier_gallery` where `tbsupplier`.`supplier_id` = `tbsupplier_gallery`.`supplier_id`) as `gallery_count`');
        // Filtro de búsqueda
        if (!empty($filters['search'])) {
            $query->search($filters['search']);
        }

        // Filtro por local (para gerentes)
        if (!empty($filters['local_id'])) {
            $query->byLocal($filters['local_id']);
        }

        // Ordenamiento
        $sortBy = $filters['sort_by'] ?? 'recent';
        switch ($sortBy) {
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('name', 'desc');
                break;
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'recent':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        // Paginación de 10 por página
        return $query->paginate(10)->withQueryString();
    }

    /**
     * Obtener todos los Proveedores sin paginación
     */
    public function getAllActive()
    {
        return Supplier::with('gallery')
            ->orderBy('name')
            ->get();
    }

    /**
     * Obtener todos los Proveedores con datos mínimos
     */
    public function getAllActiveMinimal()
    {
        return Supplier::select('supplier_id', 'name', 'phone', 'email')
            ->orderBy('name')
            ->get();
    }

    /**
     * Buscar un Proveedor por ID
     */
    public function find($id)
    {
        return Supplier::with('gallery')->find($id);
    }

    /**
     * Obtener proveedores de un local específico
     */
    public function getByLocal($localId)
    {
        return Supplier::byLocal($localId)
            ->with('gallery')
            ->orderBy('name')
            ->get();
    }

    /**
     * Crear un nuevo Proveedor
     */
    public function create(array $data)
    {
        return Supplier::create($data);
    }

    /**
     * Actualizar un Proveedor
     */
    public function update($id, array $data)
    {
        $supplier = Supplier::find($id);
        if ($supplier) {
            $supplier->update($data);
            return $supplier;
        }
        return null;
    }

    /**
     * Eliminar un Proveedor
     */
    public function delete($id)
    {
        $supplier = Supplier::find($id);
        if ($supplier) {
            $supplier->delete();
            return true;
        }
        return false;
    }

    /**
     * Obtener total de proveedores
     */
    public function countTotals()
    {
        return [
            'total' => Supplier::count(),
        ];
    }

    /**
     * Obtener total de proveedores por local
     */
    public function countTotalsByLocal($localId)
    {
        return [
            'total' => Supplier::byLocal($localId)->count(),
        ];
    }

    /**
     * Asignar proveedor a un local
     */
    public function assignToLocal($supplierId, $localId)
    {
        $supplier = Supplier::find($supplierId);
        if ($supplier) {
            // Sincronizar (agregar si no existe)
            $supplier->locals()->syncWithoutDetaching([$localId]);
            return true;
        }
        return false;
    }

    /**
     * Desasignar proveedor de un local
     */
    public function removeFromLocal($supplierId, $localId)
    {
        $supplier = Supplier::find($supplierId);
        if ($supplier) {
            $supplier->locals()->detach($localId);
            return true;
        }
        return false;
    }

    /**
     * Obtener proveedores del local actual del usuario
     */
    public function getByLocalUser($user)
    {
        if ($user->isAdminLocal()) {
            $local = $user->locals()->first();
            if ($local) {
                return $this->getByLocal($local->local_id);
            }
        }
        return [];
    }
}
