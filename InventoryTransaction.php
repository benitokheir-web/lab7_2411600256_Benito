<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryTransaction extends Model
{
    protected $fillable = [
        'product_id', 'user_id', 'type', 'quantity',
        'reference_document', 'customer_name', 'notes',
    ];

    protected function casts(): array
    {
        return ['quantity' => 'integer'];
    }

    public function product(): BelongsTo { return $this->belongsTo(Product::class); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }

    public function scopeStockIn($query) { return $query->where('type', 'in'); }
    public function scopeStockOut($query) { return $query->where('type', 'out'); }
    public function scopeRecent($query) { return $query->latest(); }
}
