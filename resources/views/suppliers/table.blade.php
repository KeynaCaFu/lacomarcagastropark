@if($suppliers && count($suppliers) > 0)
    <table>
        <thead>
            <tr>
                <th style="border-bottom: none !important;">Nombre</th>
                <th style="border-bottom: none !important;">Teléfono</th>
                <th style="border-bottom: none !important;">Email</th>
                <th style="border-bottom: none !important;">Registrado</th>
                <th style="border-bottom: none !important;">Acciones</th>
            </tr>
        </thead>

        <tbody>
            @foreach($suppliers as $supplier)
            <tr>
                <td>
                    <strong>{{ $supplier->name }}</strong>
                </td>
                <td>{{ $supplier->phone }}</td>
                <td>{{ $supplier->email }}</td>
                <td>
                    {{ $supplier->created_at ? $supplier->created_at->format('d/m/Y') : 'N/A' }}
                </td>
                <td style="text-align:center;">
                    <div class="actions" style="justify-content: center;">
                        <a href="{{ route('suppliers.show', $supplier->supplier_id) }}" class="btn-action btn-view">
                            <i class="fas fa-eye"></i>
                        </a>

                        <a href="{{ route('suppliers.edit', $supplier->supplier_id) }}" class="btn-action btn-edit">
                            <i class="fas fa-edit"></i>
                        </a>

                        <form action="{{ route('suppliers.destroy', $supplier->supplier_id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-action btn-delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
@else
    <div class="empty-state">
        <i class="fas fa-truck" style="font-size: 40px; margin-bottom: 10px;"></i>
        <p>No hay proveedores registrados.</p>
    </div>
@endif