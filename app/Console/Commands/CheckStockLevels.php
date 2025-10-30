<?php

namespace App\Console\Commands;

use App\Services\StockNotificationService;
use Illuminate\Console\Command;

class CheckStockLevels extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stock:check {--type=all : Check type: all, out-of-stock, low-stock}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check stock levels and create notifications for out of stock and low stock products';

    /**
     * Execute the console command.
     */
    public function handle(StockNotificationService $stockService): int
    {
        $type = $this->option('type');

        $this->info('Checking stock levels...');

        switch ($type) {
            case 'out-of-stock':
                $stockService->checkOutOfStockProducts();
                $this->info('✓ Out of stock products checked');
                break;
            case 'low-stock':
                $stockService->checkLowStockProducts();
                $this->info('✓ Low stock products checked');
                break;
            case 'all':
            default:
                $stockService->checkOutOfStockProducts();
                $stockService->checkLowStockProducts();
                $this->info('✓ Out of stock products checked');
                $this->info('✓ Low stock products checked');
                break;
        }

        // Show summary
        $summary = $stockService->getStockSummary();
        $this->info('Summary:');
        $this->info("- Out of stock products: {$summary['out_of_stock_count']}");
        $this->info("- Low stock products: {$summary['low_stock_count']}");

        return Command::SUCCESS;
    }
}
