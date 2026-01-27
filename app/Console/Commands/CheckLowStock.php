<?php

namespace App\Console\Commands;

use App\Models\Inventario;
use Illuminate\Console\Command;
use App\Notifications\LowStockNotification;
use Illuminate\Support\Facades\Notification;

class CheckLowStock extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'inventory:check-low-stock {--threshold=3 : The stock level threshold for notifications}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for products with low stock and send notifications';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $threshold = (int) $this->option('threshold');

        $lowStockItems = Inventario::with(['producto', 'almacene'])
            ->where('cantidad', '<=', $threshold)
            ->get();

        if ($lowStockItems->isEmpty()) {
            $this->info("No products found with stock at or below {$threshold}.");
            return 0;
        }

        $this->info("Found {$lowStockItems->count()} products with low stock.");

        $count = 0;

        foreach ($lowStockItems as $item) {
            $users = \App\Models\User::role('admin')->get();

            foreach ($users as $user) {
                $user->notify(new LowStockNotification(
                    $item->producto,
                    $item->cantidad,
                    $item->almacene ? $item->almacene->nombre : null,
                    $item->id_inventario
                ));
                $count++;
            }

            $this->line("Notified about: {$item->producto->item} (Stock: {$item->cantidad})");
        }

        $this->info("Sent {$count} low stock notifications.");

        return 0;
    }
}
