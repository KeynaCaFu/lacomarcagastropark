<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $table = 'tborder';
    protected $primaryKey = 'order_id';
    public $timestamps = true;

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $fillable = [
        'order_number',
        'quantity',
        'additional_notes',
        'preparation_time',
        'date',
        'time',
        'total_amount',
        'voucher_path',
        'cancellation_reason',
        'status',
        'origin',
        'local_id',
        'payment_method',
        'receipt_reference',
        'receipt_number',
        'receipt_sent_to_email',
        'receipt_sent_at',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'date' => 'date',
    ];

    // Estados disponibles
    const STATUS_PENDING = 'Pending';
    const STATUS_PREPARATION = 'Preparing';
    const STATUS_READY = 'Ready';
    const STATUS_DELIVERED = 'Delivered';
    const STATUS_CANCELLED = 'Cancelled';

    // Orígenes
    const ORIGIN_WEB = 'web';
    const ORIGIN_PRESENCIAL = 'presencial';

    /**
     * Relación: Ítems de la orden
     */
    public function items()
    {
        return $this->hasMany(OrderItem::class, 'order_id', 'order_id');
    }

    /**
     * Relación: Local donde se hizo la orden
     */
    public function local()
    {
        return $this->belongsTo(Local::class, 'local_id', 'local_id');
    }

    /**
     * Relación: Usuarios que hicieron la orden (cliente o gerente)
     */
    public function user()
    {
        return $this->belongsToMany(User::class, 'tbuser_order', 'order_id', 'user_id');
    }

    /**
     * Relación: Comprobantes/Recibos de la orden
     */
    public function receipts()
    {
        return $this->hasMany(Receipt::class, 'order_id', 'order_id');
    }

    /**
     * Relación: Locales asociados a la orden
     */
    public function locals()
    {
        return $this->belongsToMany(Local::class, 'tblocal_order', 'order_id', 'local_id');
    }

    /**
     * Obtener estados disponibles
     */
    public static function getStatuses()
    {
        return [
            self::STATUS_PENDING => 'Pendiente',
            self::STATUS_PREPARATION => 'En Preparación',
            self::STATUS_READY => 'Listo',
            self::STATUS_DELIVERED => 'Entregado',
            self::STATUS_CANCELLED => 'Cancelado',
        ];
    }

    /**
     * Obtener orígenes disponibles
     */
    public static function getOrigins()
    {
        return [
            self::ORIGIN_WEB => 'Web',
            self::ORIGIN_PRESENCIAL => 'Presencial',
        ];
    }

    /**
     * Obtener color de estado para UI
     */
    public function getStatusColorClass()
    {
        return match($this->status) {
            self::STATUS_PENDING => 'status-pending',
            self::STATUS_PREPARATION => 'status-preparation',
            self::STATUS_READY => 'status-ready',
            self::STATUS_DELIVERED => 'status-delivered',
            self::STATUS_CANCELLED => 'status-cancelled',
            default => 'status-pending'
        };
    }

    /**
     * Obtener icono de estado
     */
    public function getStatusIcon()
    {
        return match($this->status) {
            self::STATUS_PENDING => 'fas fa-hourglass-start',
            self::STATUS_PREPARATION => 'fas fa-fire',
            self::STATUS_READY => 'fas fa-check-circle',
            self::STATUS_DELIVERED => 'fas fa-truck',
            self::STATUS_CANCELLED => 'fas fa-times-circle',
            default => 'fas fa-info-circle'
        };
    }

    /**
     * Buscar por orden
     */
    public function scopeSearch($query, $search)
    {
        return $query->where('order_number', 'like', "%{$search}%")
                    ->orWhere('additional_notes', 'like', "%{$search}%");
    }

    /**
     * Filtrar por estado
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Filtrar por local
     */
    public function scopeByLocal($query, $localId)
    {
        return $query->where('local_id', $localId);
    }

    /**
     * Calcular total de la orden a partir de los items
     */
    public function calculateTotal()
    {
        $total = 0;
        foreach ($this->items as $item) {
            $localProduct = $item->product->locals->where('local_id', $this->local_id)->first();
            $price = $localProduct ? $localProduct->pivot->price : 0;
            $total += $price * $item->quantity;
        }
        return $total;
    }

    /**
     * Obtener total: verifica si total_amount es correcto, si no lo calcula
     */
    public function getTotalAmount()
    {
        $calculatedTotal = $this->calculateTotal();
        // Si hay discrepancia mayor a 0.01, retorna el calculado
        if (abs($this->total_amount - $calculatedTotal) > 0.01) {
            return $calculatedTotal;
        }
        return $this->total_amount;
    }

    /**
     * Filtrar por fecha
     */
    public function scopeByDate($query, $date)
    {
        return $query->whereDate('date', $date);
    }

    /**
     * Filtrar órdenes recientes
     */
    public function scopeRecent($query, $days = 30)
    {
        return $query->whereDate('created_at', '>=', now()->subDays($days));
    }
}
