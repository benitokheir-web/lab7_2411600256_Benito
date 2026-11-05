<?php
namespace App\Services;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\PurchaseOrder;
use Illuminate\Support\Facades\DB;
class ReorderService {
 public function calculateReorderPoint(Product $product): int {
  $lead=(int)($product->supplierRelation?->lead_time_days ?? 0);
  return (int)ceil(((float)$product->average_daily_usage*$lead)+(int)$product->safety_stock);
 }
 public function calculateOptimalOrderQuantity(Product $product): int {
  $needed=max((int)$product->minimum_order_quantity,$this->calculateReorderPoint($product)-(int)$product->quantity);
  $pack=max(1,(int)$product->package_size);
  return (int)(ceil($needed/$pack)*$pack);
 }
 public function getProductsNeedingReorder() {
  return Product::with('supplierRelation')->get()->filter(fn($p)=>$p->supplier_id && $p->quantity <= $this->calculateReorderPoint($p));
 }
 public function groupProductsBySupplier($products){return $products->groupBy('supplier_id');}
 public function generatePurchaseOrders(): int {
  $created=0;
  foreach($this->groupProductsBySupplier($this->getProductsNeedingReorder()) as $supplierId=>$products){
   $supplier=Supplier::active()->find($supplierId); if(!$supplier)continue;
   $alreadyOpen=PurchaseOrder::where('supplier_id',$supplierId)->whereIn('status',['draft','pending','approved','sent'])->exists();
   if($alreadyOpen)continue;
   DB::transaction(function()use($supplier,$products,&$created){
    $po=PurchaseOrder::create(['reference_number'=>PurchaseOrder::generateReferenceNumber(),'supplier_id'=>$supplier->id,'status'=>'draft','order_date'=>today(),'expected_delivery_date'=>today()->addDays($supplier->lead_time_days),'subtotal'=>0,'tax'=>0,'total'=>0,'created_by'=>null]);
    foreach($products as $product)$po->items()->create(['product_id'=>$product->id,'product_name'=>$product->name,'quantity'=>$this->calculateOptimalOrderQuantity($product),'unit_price'=>$product->unit_price,'received_quantity'=>0]);
    $po->calculateTotal();$created++;
   });
  }
  return $created;
 }
}
