<?php
namespace App\Console\Commands;
use Illuminate\Console\Command;
use App\Services\ReorderService;
class CheckReorderPoints extends Command {
 protected $signature='inventory:check-reorder-points';
 protected $description='Detect low stock and generate draft purchase orders grouped by supplier.';
 public function handle(ReorderService $service): int {
  try {$count=$service->generatePurchaseOrders();$this->info("Reorder check completed. Draft purchase orders created: {$count}");return self::SUCCESS;}
  catch(\Throwable $e){report($e);$this->error('Reorder check failed: '.$e->getMessage());return self::FAILURE;}
 }
}
