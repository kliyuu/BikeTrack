<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;

class PaymentService
{
    public function createPayment(Order $order, float $amount, string $method, $proof = null): Payment
    {
        // Implementation for creating a payment
        $data = [
            'order_id' => $order->id,
            'amount' => $amount,
            'method' => $method,
            'status' => 'pending',
        ];

        if ($method === 'gcash' && $proof instanceof UploadedFile) {
            $data['proof_path'] = $proof->store('payments', 'public');
        }

        return Payment::create($data);
    }

    public function markAsPaid(Payment $payment)
    {
        $payment->update([
            'status' => 'paid',
            'paid_at' => Carbon::now(),
        ]);

        $payment->order->update(['status' => 'paid']);
    }

    public function markAsFailed(Payment $payment)
    {
        $payment->update(['status' => 'failed']);
        $payment->order->update(['status' => 'payment_failed']);
    }
}
