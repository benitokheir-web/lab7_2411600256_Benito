<?php
namespace App\Providers;
use App\Models\InventoryTransaction;
use App\Observers\InventoryTransactionObserver;
use Illuminate\Support\ServiceProvider;
class AppServiceProvider extends ServiceProvider {
    public function register(): void {}
    public function boot(): void { InventoryTransaction::observe(InventoryTransactionObserver::class); }
}
