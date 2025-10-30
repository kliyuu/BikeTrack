<?php

namespace App\Console\Commands;

use App\Models\Order;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class AutoCompleteDeliveredOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:auto-complete';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically mark delivered orders as completed after 24 hours';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Checking for delivered orders to auto-complete...');

        // Find orders that are delivered and have been delivered for more than 24 hours
        $orders = Order::where('status', 'delivered')
            ->whereNotNull('delivered_at')
            ->where('delivered_at', '<=', now()->subHours(24))
            ->get();

        if ($orders->isEmpty()) {
            $this->info('No orders found to auto-complete.');

            return self::SUCCESS;
        }

        $completedCount = 0;

        foreach ($orders as $order) {
            try {
                $order->status = 'completed';
                $order->save();

                $completedCount++;

                $this->info("Order #{$order->order_number} marked as completed.");

                // Log the auto-completion
                Log::info("Order #{$order->order_number} automatically marked as completed after 24 hours.", [
                    'order_id' => $order->id,
                    'delivered_at' => $order->delivered_at,
                    'completed_at' => now(),
                ]);
            } catch (\Exception $e) {
                $this->error("Failed to complete order #{$order->order_number}: {$e->getMessage()}");
                Log::error("Failed to auto-complete order #{$order->order_number}", [
                    'order_id' => $order->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $this->info("Successfully auto-completed {$completedCount} order(s).");

        return self::SUCCESS;
    }
}
