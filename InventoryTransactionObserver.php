<?php

namespace App\Observers;

use App\Models\InventoryTransaction;
use App\Models\Product;
use App\Models\User;
use App\Notifications\LowStockNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\ValidationException;

class InventoryTransactionObserver
{
    public function created(InventoryTransaction $transaction): void
    {
        $product = Product::query()->lockForUpdate()->findOrFail($transaction->product_id);

        $wasLowStock = $product->isLowStock();

        $newQuantity = match ($transaction->type) {
            'in' => $product->quantity + $transaction->quantity,
            'out' => $product->quantity - $transaction->quantity,
            'adjustment' => $transaction->quantity,
            default => throw ValidationException::withMessages(['type' => 'Invalid transaction type.']),
        };

        if ($newQuantity < 0) {
            throw ValidationException::withMessages([
                'quantity' => 'Insufficient stock. Stock cannot become negative.',
            ]);
        }

        $product->update(['quantity' => $newQuantity]);

        if (!$wasLowStock && $product->isLowStock()) {
            $recipients = User::query()->get();
            Notification::send($recipients, new LowStockNotification($product));
        }
    }
}
