<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * DEPRECATED: Los datos de comprobante se guardan en tbreceipt
 * Modelo para representar un comprobante de orden. 
 * Aunque inicialmente se consideró guardar los datos del comprobante directamente en la tabla de órdenes (tborder), 
 * se decidió crear una tabla separada (tbreceipt) para almacenar esta información de manera más estructurada y escalable. 
 * Este modelo se mantiene por compatibilidad, pero los datos reales del comprobante ahora se gestionan a través del modelo Receipt y la tabla tbreceipt.
 */
class Receipt extends Model
{
    use HasFactory;

    protected $table = 'tbreceipt';
    protected $primaryKey = 'receipt_id';

    protected $fillable = [
        'order_id',
        'receipt_number',
        'payment_method',
        'receipt_reference',
        'pdf_path',
        'sent_to_email',
        'sent_at',
    ];

    /**
     * Relación: Orden asociada al comprobante
     */
    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'order_id');
    }
}
