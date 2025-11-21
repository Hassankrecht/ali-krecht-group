<?php

namespace App\Mail;

use App\Models\Checkout;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Barryvdh\DomPDF\Facade\Pdf;

class OrderPlacedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $order;
    public $isAdmin;

    public function __construct(Checkout $order, $isAdmin = false)
    {
        $this->order = $order;
        $this->isAdmin = $isAdmin;
    }

    public function build()
    {
        $subject = $this->isAdmin
            ? "📦 New Order #{$this->order->id} Received"
            : "✅ Your Order #{$this->order->id} Confirmation";

        $pdf = Pdf::loadView('pdf.invoice', ['order' => $this->order])->setPaper('a4');

        return $this->subject($subject)
            ->markdown('emails.orders.placed')
            ->attachData($pdf->output(), "invoice_{$this->order->id}.pdf", [
                'mime' => 'application/pdf',
            ]);
    }
}
