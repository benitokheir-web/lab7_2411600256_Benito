<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class PurchaseOrderItem extends Model {
 protected $fillable=['purchase_order_id','product_id','product_name','quantity','unit_price','received_quantity'];
 protected $casts=['unit_price'=>'decimal:2'];
 public function purchaseOrder(){return $this->belongsTo(PurchaseOrder::class);}
 public function product(){return $this->belongsTo(Product::class);}
 public function getLineTotalAttribute(): float{return $this->quantity*$this->unit_price;}
 public function getOutstandingQuantityAttribute(): int{return max(0,$this->quantity-$this->received_quantity);}
}
