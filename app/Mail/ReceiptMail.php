<?php

namespace App\Mail;

use App\Models\Order;
use App\Models\Receipt;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Queue\SerializesModels;

class ReceiptMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        private Order $order,
        private Receipt $receipt,
        private string $pdfPath
    ) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Comprobante de Orden #' . $this->order->order_number . ' - La Comarca Gastropark',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.receipt-email',
            with: [
                'order' => $this->order,
                'receipt' => $this->receipt,
                'customerName' => $this->order->user?->first()?->full_name ?? 'Cliente',
            ],
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        if (!file_exists($this->pdfPath)) {
            return [];
        }

        return [
            Attachment::fromPath($this->pdfPath)
                ->as('Comprobante_' . $this->order->order_number . '.pdf')
                ->withMime('application/pdf'),
        ];
    }
}
