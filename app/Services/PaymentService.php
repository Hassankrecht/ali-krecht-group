<?php

namespace App\Services;

use App\Models\Payment;
use Illuminate\Support\Facades\Log;

class PaymentService
{
    /**
     * Process a payment
     */
    public function process(array $data): Payment
    {
        // Validate payment data
        $this->validatePaymentData($data);

        // Create payment record
        $payment = Payment::create([
            'user_id' => $data['user_id'],
            'checkout_id' => $data['checkout_id'] ?? null,
            'amount' => $data['amount'],
            'currency' => $data['currency'] ?? 'USD',
            'method' => $data['method'],
            'status' => 'pending',
            'reference' => $this->generateReference(),
            'metadata' => $data['metadata'] ?? [],
        ]);

        // Process based on payment method
        match ($data['method']) {
            'card' => $this->processCard($payment, $data),
            'bank_transfer' => $this->processBankTransfer($payment, $data),
            'paypal' => $this->processPaypal($payment, $data),
            'crypto' => $this->processCrypto($payment, $data),
            default => throw new \InvalidArgumentException('Invalid payment method'),
        };

        return $payment;
    }

    /**
     * Process card payment
     */
    private function processCard(Payment $payment, array $data): void
    {
        // Integrate with payment gateway (Stripe, etc)
        try {
            $result = $this->stripeChargeCard(
                $payment->amount,
                $data['token'],
                $payment->reference
            );

            if ($result['success']) {
                $payment->update([
                    'status' => 'completed',
                    'transaction_id' => $result['transaction_id'],
                    'processed_at' => now(),
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Card payment failed: ' . $e->getMessage());
            $payment->update(['status' => 'failed']);
            throw $e;
        }
    }

    /**
     * Process bank transfer
     */
    private function processBankTransfer(Payment $payment, array $data): void
    {
        // Generate bank transfer details
        $bankDetails = [
            'account_number' => config('payments.bank.account'),
            'routing_number' => config('payments.bank.routing'),
            'bank_name' => config('payments.bank.name'),
            'reference' => $payment->reference,
        ];

        $payment->update([
            'status' => 'pending_confirmation',
            'metadata' => array_merge($payment->metadata ?? [], $bankDetails),
        ]);
    }

    /**
     * Process PayPal payment
     */
    private function processPaypal(Payment $payment, array $data): void
    {
        // Integrate with PayPal API
        // This would typically redirect user to PayPal
        $payment->update(['status' => 'pending']);
    }

    /**
     * Process crypto payment
     */
    private function processCrypto(Payment $payment, array $data): void
    {
        // Generate crypto payment address
        $address = $this->generateCryptoAddress($data['currency']);

        $payment->update([
            'status' => 'pending',
            'metadata' => array_merge($payment->metadata ?? [], [
                'crypto_address' => $address,
                'currency' => $data['currency'],
            ]),
        ]);
    }

    /**
     * Refund a payment
     */
    public function refund(Payment $payment, ?float $amount = null): Payment
    {
        $refundAmount = $amount ?? $payment->amount;

        if ($refundAmount > $payment->amount) {
            throw new \InvalidArgumentException('Refund amount exceeds payment amount');
        }

        try {
            if ($payment->method === 'card' && $payment->transaction_id) {
                $this->stripeRefund($payment->transaction_id, $refundAmount);
            }

            $payment->update([
                'status' => 'refunded',
                'refunded_amount' => $refundAmount,
                'refunded_at' => now(),
            ]);

            return $payment;
        } catch (\Exception $e) {
            Log::error('Refund failed: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Verify payment status
     */
    public function verify(Payment $payment): bool
    {
        return match ($payment->method) {
            'card' => $this->verifyCard($payment),
            'bank_transfer' => $this->verifyBankTransfer($payment),
            'paypal' => $this->verifyPaypal($payment),
            'crypto' => $this->verifyCrypto($payment),
            default => false,
        };
    }

    /**
     * Get payment history for user
     */
    public function userPayments($userId, int $limit = 50)
    {
        return Payment::where('user_id', $userId)
            ->orderByDesc('created_at')
            ->paginate($limit);
    }

    /**
     * Get transaction stats
     */
    public function stats(?string $method = null): array
    {
        $query = Payment::where('status', 'completed');

        if ($method) {
            $query->where('method', $method);
        }

        return [
            'total_transactions' => $query->count(),
            'total_amount' => $query->sum('amount'),
            'average_amount' => $query->avg('amount'),
        ];
    }

    /**
     * Validate payment data
     */
    private function validatePaymentData(array $data): void
    {
        $required = ['user_id', 'amount', 'method'];

        foreach ($required as $field) {
            if (!isset($data[$field])) {
                throw new \InvalidArgumentException("Missing required field: {$field}");
            }
        }

        if ($data['amount'] <= 0) {
            throw new \InvalidArgumentException('Amount must be greater than 0');
        }
    }

    /**
     * Generate unique payment reference
     */
    private function generateReference(): string
    {
        return 'PAY-' . strtoupper(uniqid()) . '-' . now()->timestamp;
    }

    /**
     * Generate crypto payment address
     */
    private function generateCryptoAddress(string $type): string
    {
        // This would integrate with crypto payment service
        return '0x' . bin2hex(random_bytes(20));
    }

    /**
     * Stripe charge card
     */
    private function stripeChargeCard(float $amount, string $token, string $reference): array
    {
        // Stub implementation - would use actual Stripe SDK
        return [
            'success' => true,
            'transaction_id' => 'ch_' . uniqid(),
        ];
    }

    /**
     * Stripe refund
     */
    private function stripeRefund(string $transactionId, float $amount): void
    {
        // Stub implementation - would use actual Stripe SDK
    }

    /**
     * Verify card payment
     */
    private function verifyCard(Payment $payment): bool
    {
        return $payment->status === 'completed' && $payment->transaction_id;
    }

    /**
     * Verify bank transfer
     */
    private function verifyBankTransfer(Payment $payment): bool
    {
        // Check bank confirmation
        return $payment->status === 'completed';
    }

    /**
     * Verify PayPal payment
     */
    private function verifyPaypal(Payment $payment): bool
    {
        // Verify with PayPal API
        return $payment->status === 'completed';
    }

    /**
     * Verify crypto payment
     */
    private function verifyCrypto(Payment $payment): bool
    {
        // Check blockchain confirmation
        return $payment->status === 'completed';
    }
}
