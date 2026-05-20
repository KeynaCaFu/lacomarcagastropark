@extends('layouts.app')

@section('title', 'Historial de Órdenes')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/reports.css') }}">
<style>
    .oh-status-badge {
        display: inline-block;
        padding: 3px 9px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 600;
    }
    .oh-status--pending    { background:#fef3c7; color:#92400e; }
    .oh-status--preparing  { background:#ffedd5; color:#9a3412; }
    .oh-status--ready      { background:#d1fae5; color:#065f46; }
    .oh-status--delivered  { background:#dcfce7; color:#14532d; }
    .oh-status--cancelled  { background:#fee2e2; color:#991b1b; }

    .oh-table {
    width: 96%;
     border-collapse: collapse;
     font-size: 13px;
     margin-left: 20px; }

    .oh-table thead th {
       padding: 10px 16px;
        text-align: center;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .5px;
        color: #666;
        border-bottom: 2px solid #e5e7eb;
        background: #a3aa996b;
    }
    .oh-table tbody tr { border-bottom: 1px solid #f0f0f0; transition: background .15s; }
    .oh-table tbody tr:hover { background: #fafafa; }
    .oh-table tbody td { padding: 11px 14px; vertical-align: middle; }
    .oh-table tbody tr:last-child { border-bottom: none; }

    .oh-action-btn {
        padding: 5px 9px;
        border: 2px solid;
        background: transparent;
        border-radius: 5px;
        cursor: pointer;
        font-size: 11px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        transition: all .2s;
        text-decoration: none;
        white-space: nowrap;
    }
    .oh-btn--download { border-color:#3b82f6; color:#3b82f6; }
    .oh-btn--download:hover { background:#3b82f6; color:white; }
    .oh-btn--regen    { border-color:#f59e0b; color:#f59e0b; }
    .oh-btn--regen:hover { background:#f59e0b; color:white; }
    .oh-btn--resend   { border-color:#10b981; color:#10b981; }
    .oh-btn--resend:hover { background:#10b981; color:white; }

    .oh-empty { text-align:center; padding:60px 20px; color:#999; }
    .oh-empty i { font-size:40px; opacity:.35; margin-bottom:12px; display:block; }

    .oh-pdf-badge { display:inline-block; padding:2px 7px; border-radius:10px; font-size:10px; font-weight:600; }
    .oh-pdf--yes { background:#d1fae5; color:#065f46; }
    .oh-pdf--no  { background:#fee2e2; color:#991b1b; }
</style>
@endpush

@section('content')
<div class="rp-container">

    {{-- Header --}}
    <div class="rp-header" style="margin-bottom:1.75rem;">
        <h1>Historial de Órdenes</h1>
        <p>Todas las órdenes del local — {{ $local->name }}</p>
    </div>

    {{-- Navegación --}}
    <div class="rp-view-toggle">
        <a href="{{ route('reports.orders') }}?local_id={{ $local->local_id }}" class="rp-view-btn">
            <i class="fas fa-store"></i> Reportes del Local
        </a>
        <a href="{{ route('reports.products') }}?local_id={{ $local->local_id }}" class="rp-view-btn">
            <i class="fas fa-box"></i> Reportes por Producto
        </a>
        <span class="rp-view-btn active" style="cursor:default; pointer-events:none;">
            <i class="fas fa-history"></i> Historial de Órdenes
        </span>
    </div>

    {{-- Filtros --}}
    <div class="rp-card" style="display:inline-block; min-width: 388px; width:auto;">
        <form id="filterForm" method="GET" action="{{ route('reports.order-history') }}">
            <div class="rp-filters">
                @if($userLocals->count() > 1)
                <div>
                    <label for="local_id">Local</label>
                    <select name="local_id" id="local_id" onchange="document.getElementById('filterForm').submit()">
                        @foreach($userLocals as $l)
                        <option value="{{ $l->local_id }}" {{ $local->local_id == $l->local_id ? 'selected' : '' }}>
                            {{ $l->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                @endif
                <div>
                    <label for="period">Período</label>
                    <select name="period" id="period" onchange="handlePeriodChange(this.value)">
                        <option value="today"  {{ $period==='today'  ? 'selected' : '' }}>Hoy</option>
                        <option value="week"   {{ $period==='week'   ? 'selected' : '' }}>Esta Semana</option>
                        <option value="month"  {{ $period==='month'  ? 'selected' : '' }}>Este Mes</option>
                        <option value="year"   {{ $period==='year'   ? 'selected' : '' }}>Este Año</option>
                        <option value="custom" {{ $period==='custom' ? 'selected' : '' }}>Personalizado</option>
                    </select>
                </div>
                <div id="startDateDiv" style="display:{{ $period==='custom' ? 'block' : 'none' }};">
                    <label for="start_date">Desde</label>
                    <input type="date" name="start_date" id="start_date" value="{{ $startDate }}">
                </div>
                <div id="endDateDiv" style="display:{{ $period==='custom' ? 'block' : 'none' }};">
                    <label for="end_date">Hasta</label>
                    <input type="date" name="end_date" id="end_date" value="{{ $endDate }}">
                </div>
                <div id="filterBtnDiv" style="display:{{ $period==='custom' ? 'block' : 'none' }};">
                    <label style="visibility:hidden;">–</label>
                    <button type="submit" class="rp-btn rp-btn--green rp-btn--full">Filtrar</button>
                </div>
            </div>
        </form>
    </div>

    {{-- Tabla de órdenes --}}
    <div class="rp-card" style="padding:0; overflow:hidden;">
        <div style="padding:16px 20px; border-bottom:1px solid #e5e7eb; display:flex; justify-content:space-between; align-items:center;">
            <h2 style="margin:0; font-size:15px;">
                Órdenes
                <span id="oh-count-badge" style="background:#e18018; color:white; padding:2px 9px; border-radius:12px; font-size:12px; margin-left:6px;">
                    {{ $orders->total() }}
                </span>
            </h2>
            <input type="text" id="ohSearch" placeholder="Buscar orden o cliente..." style="padding:7px 12px; border:1px solid #e5e7eb; border-radius:6px; font-size:13px; width:220px;">
        </div>

        <div id="oh-table-wrapper">
        @if($orders->isEmpty())
            <div class="oh-empty">
                <i class="fas fa-inbox"></i>
                <p>No hay órdenes en este período</p>
            </div>
        @else
        <div style="overflow-x:auto;">
            <table class="oh-table">
                <thead>
                    <tr>
                        <th>Orden</th>
                        <th>Cliente</th>
                        <th>Estado</th>
                        <th>Fecha</th>
                        <th>Hora</th>
                        <th>Total</th>
                        <th>PDF</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="ohTableBody">
                @foreach($orders as $order)
                    @php
                        $customer  = $order->user->first();
                        $statusMap = [
                            'Pending'   => ['label' => 'Pendiente',       'class' => 'oh-status--pending'],
                            'Preparing' => ['label' => 'En Preparación',  'class' => 'oh-status--preparing'],
                            'Ready'     => ['label' => 'Listo',           'class' => 'oh-status--ready'],
                            'Delivered' => ['label' => 'Entregado',       'class' => 'oh-status--delivered'],
                            'Cancelled' => ['label' => 'Cancelado',       'class' => 'oh-status--cancelled'],
                        ];
                        $statusInfo = $statusMap[$order->status] ?? ['label' => $order->status, 'class' => ''];
                        $hasPdf = $order->receipts && $order->receipts->contains(fn($r) => $r->pdf_path !== null);
                        $isDelivered = $order->status === 'Delivered';
                    @endphp
                    <tr data-search="{{ strtolower($order->order_number . ' ' . ($customer?->full_name ?? '')) }}">
                        <td style="font-weight:700; color:#333;">{{ $order->order_number }}</td>
                        <td>
                            <div style="font-weight:600; color:#111;">{{ $customer?->full_name ?? 'Sin cliente' }}</div>
                            @if($customer?->email)
                                <div style="font-size:11px; color:#999;">{{ $customer->email }}</div>
                            @endif
                        </td>
                        <td>
                            <span class="oh-status-badge {{ $statusInfo['class'] }}">
                                {{ $statusInfo['label'] }}
                            </span>
                        </td>
                        <td style="color:#666;">{{ $order->date }}</td>
                        <td style="color:#666;">{{ \Carbon\Carbon::parse($order->time)->format('H:i') }}</td>
                        <td style="">₡{{ number_format($order->total_amount, 2) }}</td>
                        <td>
                            <span id="oh-pdf-{{ $order->order_id }}" class="oh-pdf-badge {{ $hasPdf ? 'oh-pdf--yes' : 'oh-pdf--no' }}">
                                <i class="fas {{ $hasPdf ? 'fa-file-pdf' : 'fa-exclamation-circle' }}"></i>
                                {{ $hasPdf ? 'PDF' : 'Sin PDF' }}
                            </span>
                        </td>
                        <td>
                            @if($isDelivered)
                            <div style="display:flex; gap:5px; flex-wrap:wrap;">
                                <button class="oh-action-btn oh-btn--download" onclick="ohDownload({{ $order->order_id }})" title="Descargar comprobante">
                                    <i class="fas fa-download"></i>
                                </button>
                                <button class="oh-action-btn oh-btn--regen" onclick="ohRegenerate({{ $order->order_id }})" title="Regenerar comprobante">
                                    <i class="fas fa-redo"></i>
                                </button>
                                <button class="oh-action-btn oh-btn--resend" onclick="ohResend({{ $order->order_id }})" title="Reenviar por correo">
                                    <i class="fas fa-envelope"></i>
                                </button>
                            </div>
                            @else
                            <span style="color:#ccc; font-size:12px;">—</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        @if($orders->hasPages())
            <div class="pagination-wrapper" style="margin-top:16px; display:flex; justify-content:center;">
                <div class="pagination-container">
                    {{ $orders->onEachSide(3)->links('pagination::bootstrap-4') }}
                </div>
            </div>
        @endif
        <div style="text-align:center; color:#6b7280; font-size:13px; margin: 8px 0 16px;">
            Mostrando <strong>{{ $orders->firstItem() }}</strong> a <strong>{{ $orders->lastItem() }}</strong> de <strong>{{ $orders->total() }}</strong> órdenes
        </div>
        @endif
        </div>{{-- #oh-table-wrapper --}}
    </div>

</div>

{{-- Modal datos cliente --}}
<div id="ohClientModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:3000; align-items:center; justify-content:center;">
    <div style="background:white; border-radius:12px; padding:30px; max-width:430px; width:90%; box-shadow:0 10px 40px rgba(0,0,0,0.2);">
        <h3 style="margin:0 0 10px; color:#111; font-size:17px;">
            <i class="fas fa-user-plus" style="margin-right:8px; color:#e18018;"></i> Datos del Cliente
        </h3>
        <p style="margin:0 0 20px; color:#666; font-size:13px; line-height:1.6;">
            Esta orden no tiene cliente registrado. Ingresa los datos para gestionar el comprobante.
        </p>
        <form id="ohClientForm" style="display:flex; flex-direction:column; gap:14px;">
            <div>
                <label style="display:block; margin-bottom:5px; font-weight:600; color:#333; font-size:13px;">
                    Nombre <span style="color:#ef4444;">*</span>
                </label>
                <input type="text" id="ohModalName" placeholder="Ej: Juan Pérez" required
                    style="width:100%; padding:10px 12px; border:1px solid #e5e7eb; border-radius:8px; font-size:14px; box-sizing:border-box;">
            </div>
            <div>
                <label style="display:block; margin-bottom:5px; font-weight:600; color:#333; font-size:13px;">
                    Correo <span style="color:#ef4444;">*</span>
                </label>
                <input type="email" id="ohModalEmail" placeholder="cliente@email.com" required
                    style="width:100%; padding:10px 12px; border:1px solid #e5e7eb; border-radius:8px; font-size:14px; box-sizing:border-box;">
            </div>
            <div style="display:flex; gap:12px; margin-top:6px;">
                <button type="button" id="ohCancelModal"
                    style="flex:1; padding:10px; border:2px solid #e5e7eb; background:white; color:#666; border-radius:8px; font-weight:600; cursor:pointer;">
                    Cancelar
                </button>
                <button type="submit"
                    style="flex:1; padding:10px; background:linear-gradient(135deg,#e18018,#c9690f); color:white; border:none; border-radius:8px; font-weight:600; cursor:pointer;">
                    Continuar
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
let ohPending = { action: null, orderId: null };

// Búsqueda en tabla (página actual)
document.getElementById('ohSearch').addEventListener('input', performOhSearch);

// Integración con top-search-bar
const topSearchInput = document.getElementById('topSearchInput');
if (topSearchInput) {
    topSearchInput.addEventListener('input', function() {
        document.getElementById('ohSearch').value = this.value;
        performOhSearch();
    });
}

function performOhSearch() {
    const term = document.getElementById('ohSearch').value.toLowerCase().trim();
    document.querySelectorAll('#ohTableBody tr').forEach(row => {
        row.style.display = (!term || row.dataset.search.includes(term)) ? '' : 'none';
    });
}

// Paginación AJAX — reemplaza solo #oh-table-wrapper
document.addEventListener('click', function(e) {
    const link = e.target.closest('#oh-table-wrapper .pagination a');
    if (!link) return;
    e.preventDefault();

    const wrapper = document.getElementById('oh-table-wrapper');
    wrapper.style.opacity = '0.5';
    wrapper.style.pointerEvents = 'none';

    fetch(link.href, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(r => r.text())
        .then(html => {
            const doc = new DOMParser().parseFromString(html, 'text/html');

            const newWrapper = doc.getElementById('oh-table-wrapper');
            if (newWrapper) wrapper.innerHTML = newWrapper.innerHTML;

            const newBadge = doc.getElementById('oh-count-badge');
            const badge = document.getElementById('oh-count-badge');
            if (newBadge && badge) badge.textContent = newBadge.textContent;

            history.pushState(null, '', link.href);
        })
        .catch(() => { window.location.href = link.href; })
        .finally(() => {
            wrapper.style.opacity = '';
            wrapper.style.pointerEvents = '';
        });
});

// Modal cliente
document.getElementById('ohCancelModal').addEventListener('click', () => {
    document.getElementById('ohClientModal').style.display = 'none';
    ohPending = { action: null, orderId: null };
});

document.getElementById('ohClientForm').addEventListener('submit', e => {
    e.preventDefault();
    const name  = document.getElementById('ohModalName').value.trim();
    const email = document.getElementById('ohModalEmail').value.trim();
    if (!name || !email) return;

    document.getElementById('ohClientModal').style.display = 'none';

    if (ohPending.action === 'download')    proceedDownload(ohPending.orderId);
    if (ohPending.action === 'regenerate')  proceedRegenerate(ohPending.orderId, email, name);
    if (ohPending.action === 'resend')      proceedResend(ohPending.orderId, email, name);

    document.getElementById('ohClientForm').reset();
    ohPending = { action: null, orderId: null };
});

function checkClientThen(orderId, action) {
    fetch(`{{ url('ordenes') }}/${orderId}/comprobante/validar-cliente`, {
        method: 'POST',
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json'
        }
    })
    .then(r => r.json().then(d => ({ ok: r.ok, data: d })))
    .then(({ ok, data }) => {
        if (ok && data.hasValidClient) {
            if (action === 'download')   proceedDownload(orderId);
            if (action === 'regenerate') proceedRegenerate(orderId);
            if (action === 'resend')     proceedResendDirect(orderId);
        } else {
            ohPending = { action, orderId };
            document.getElementById('ohClientModal').style.display = 'flex';
            document.getElementById('ohModalName').focus();
        }
    })
    .catch(() => Swal.fire('Error', 'No se pudo validar el cliente', 'error'));
}

function ohDownload(orderId)    { checkClientThen(orderId, 'download'); }
function ohRegenerate(orderId) {
    Swal.fire({
        icon: 'question', title: '¿Regenerar Comprobante?',
        text: 'Se generará un nuevo comprobante para esta orden.',
        showCancelButton: true,
        confirmButtonText: 'Sí, regenerar', cancelButtonText: 'Cancelar',
        confirmButtonColor: '#f59e0b', cancelButtonColor: '#6b7280',
    }).then(r => { if (r.isConfirmed) checkClientThen(orderId, 'regenerate'); });
}
function ohResend(orderId) {
    Swal.fire({
        icon: 'question', title: '¿Reenviar Comprobante?',
        text: 'Se reenviará el comprobante al correo del cliente.',
        showCancelButton: true,
        confirmButtonText: 'Sí, reenviar', cancelButtonText: 'Cancelar',
        confirmButtonColor: '#10b981', cancelButtonColor: '#6b7280',
    }).then(r => { if (r.isConfirmed) checkClientThen(orderId, 'resend'); });
}

function proceedDownload(orderId) {
    fetch(`{{ url('ordenes') }}/${orderId}/comprobante/descargar`, {
        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => { if (!r.ok) throw new Error(); return r.blob(); })
    .then(blob => {
        const a = document.createElement('a');
        a.href = window.URL.createObjectURL(blob);
        a.download = `comprobante_${orderId}.pdf`;
        document.body.appendChild(a); a.click();
        window.URL.revokeObjectURL(a.href); document.body.removeChild(a);
    })
    .catch(() => Swal.fire('Error', 'No se pudo descargar. Intenta regenerarlo primero.', 'error'));
}

function proceedRegenerate(orderId, email = null, name = null) {
    Swal.fire({ title: 'Procesando...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });

    fetch(`{{ url('ordenes') }}/${orderId}/comprobante/regenerar`, {
        method: 'POST',
        headers: {
            'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ customer_email: email, customer_name: name })
    })
    .then(r => r.json().then(d => ({ ok: r.ok, data: d })))
    .then(({ ok, data }) => {
        if (ok && data.success) {
            swToast && swToast.fire({ icon: 'success', title: data.message });
            const badge = document.getElementById(`oh-pdf-${orderId}`);
            if (badge) badge.outerHTML = `<span id="oh-pdf-${orderId}" class="oh-pdf-badge oh-pdf--yes"><i class="fas fa-file-pdf"></i> PDF</span>`;
        } else {
            Swal.fire('Error', data.message || 'Error al regenerar', 'error');
        }
    })
    .catch(() => Swal.fire('Error', 'No se pudo regenerar el comprobante', 'error'));
}

function proceedResendDirect(orderId) {
    Swal.fire({ title: 'Procesando...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });

    fetch(`{{ url('ordenes') }}/${orderId}/comprobante/reenviar`, {
        method: 'POST',
        headers: {
            'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(r => r.json().then(d => ({ ok: r.ok, data: d })))
    .then(({ ok, data }) => {
        if (ok && data.success) swToast && swToast.fire({ icon: 'success', title: data.message });
        else Swal.fire('Error', data.message || 'Error al reenviar', 'error');
    })
    .catch(() => Swal.fire('Error', 'No se pudo reenviar el comprobante', 'error'));
}

function proceedResend(orderId, email, name) {
    Swal.fire({ title: 'Procesando...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });

    fetch(`{{ url('ordenes') }}/${orderId}/comprobante/reenviar`, {
        method: 'POST',
        headers: {
            'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ customer_email: email, customer_name: name })
    })
    .then(r => r.json().then(d => ({ ok: r.ok, data: d })))
    .then(({ ok, data }) => {
        if (ok && data.success) swToast && swToast.fire({ icon: 'success', title: data.message });
        else Swal.fire('Error', data.message || 'Error al reenviar', 'error');
    })
    .catch(() => Swal.fire('Error', 'No se pudo reenviar el comprobante', 'error'));
}

function handlePeriodChange(v, submit = true) {
    const isCustom = v === 'custom';
    document.getElementById('startDateDiv').style.display  = isCustom ? 'block' : 'none';
    document.getElementById('endDateDiv').style.display    = isCustom ? 'block' : 'none';
    document.getElementById('filterBtnDiv').style.display  = isCustom ? 'block' : 'none';
    if (!isCustom && submit) document.getElementById('filterForm').submit();
}
handlePeriodChange(document.getElementById('period').value, false);
</script>
@endpush

@endsection
