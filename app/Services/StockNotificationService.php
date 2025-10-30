<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Notification;
use App\Models\ProductVariant;
use Illuminate\Support\Collection;

class StockNotificationService
{
    /**
     * Check for out of stock products and create notifications
     */
    public function checkOutOfStockProducts(): void
    {
        $outOfStockVariants = $this->getOutOfStockVariants();

        foreach ($outOfStockVariants as $variant) {
            $this->createOutOfStockNotification($variant);
        }
    }

    /**
     * Check for low stock products and create notifications
     */
    public function checkLowStockProducts(): void
    {
        $lowStockVariants = $this->getLowStockVariants();

        foreach ($lowStockVariants as $variant) {
            $this->createLowStockNotification($variant);
        }
    }

    /**
     * Get out of stock product variants
     */
    private function getOutOfStockVariants(): Collection
    {
        return ProductVariant::with(['product.category', 'product.brand'])
            ->whereHas('product', function ($query) {
                $query->where('is_active', true);
            })
            ->where('is_active', true)
            ->where('cached_stock', '=', 0)
            ->get();
    }

    /**
     * Get low stock product variants
     */
    private function getLowStockVariants(): Collection
    {
        return ProductVariant::with(['product.category', 'product.brand'])
            ->whereHas('product', function ($query) {
                $query->where('is_active', true);
            })
            ->where('is_active', true)
            ->whereColumn('cached_stock', '>', '0')
            ->whereColumn('cached_stock', '<=', 'low_stock_threshold')
            ->get();
    }

    /**
     * Create out of stock notification if it doesn't exist yet
     */
    private function createOutOfStockNotification(ProductVariant $variant): void
    {
        $title = 'Product Out of Stock';
        $message = "Product '{$variant->product->name}' ({$variant->getDisplayName()}) is out of stock.";

        // Check if notification already exists for this variant today
        $existingNotification = Notification::where('client_id', null)
            ->where('title', $title)
            ->where('message', $message)
            ->whereDate('created_at', today())
            ->first();

        if (! $existingNotification) {
            // Get the first admin user for system notifications
            $adminUser = \App\Models\User::whereHas('role', function ($query) {
                $query->where('name', 'admin');
            })->first() ?? \App\Models\User::first();

            Notification::create([
                'user_id' => $adminUser?->id ?? 1,
                'client_id' => null,
                'title' => $title,
                'message' => $message,
                'url' => route('admin.products.variants', ['productId' => $variant->product_id]),
                'is_read' => false,
            ]);
        }
    }

    /**
     * Create low stock notification if it doesn't exist yet
     */
    private function createLowStockNotification(ProductVariant $variant): void
    {
        $title = 'Low Stock Alert';
        $message = "Product '{$variant->product->name}' ({$variant->getDisplayName()}) is running low on stock. Current: {$variant->cached_stock}, Threshold: {$variant->low_stock_threshold}";

        // Check if notification already exists for this variant today
        $existingNotification = Notification::where('client_id', null)
            ->where('title', $title)
            ->where('message', $message)
            ->whereDate('created_at', today())
            ->first();

        if (! $existingNotification) {
            // Get the first admin user for system notifications
            $adminUser = \App\Models\User::whereHas('role', function ($query) {
                $query->where('name', 'admin');
            })->first() ?? \App\Models\User::first();

            Notification::create([
                'user_id' => $adminUser?->id ?? 1,
                'client_id' => null,
                'title' => $title,
                'message' => $message,
                'url' => route('admin.products.variants', ['productId' => $variant->product_id]),
                'is_read' => false,
            ]);
        }
    }

    /**
     * Get summary counts for dashboard
     */
    public function getStockSummary(): array
    {
        return [
            'out_of_stock_count' => $this->getOutOfStockVariants()->count(),
            'low_stock_count' => $this->getLowStockVariants()->count(),
        ];
    }
}
