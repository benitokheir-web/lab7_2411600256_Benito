<?php
namespace App\Http\Controllers;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\Product;
use App\Models\InventoryTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class PurchaseOrderController extends Controller {
 public function index(Request $r){
  $orders=PurchaseOrder::with(['supplier','createdBy'])->when($r->status,fn($q,$s)=>$q->where('status',$s))->when($r->supplier_id,fn($q,$s)=>$q->where('supplier_id',$s))->latest()->paginate(12)->withQueryString();
  return view('purchase-orders.index',compact('orders'));
 }
 public function create(){ $suppliers=Supplier::active()->orderBy('company_name')->get();$products=Product::orderBy('name')->get();return view('purchase-orders.create',compact('suppliers','products')); }
 public function store(Request $r){
  $d=$r->validate(['supplier_id'=>'required|exists:suppliers,id','expected_delivery_date'=>'required|date|after_or_equal:today','notes'=>'nullable|string|max:2000','items'=>'required|array|min:1','items.*.product_id'=>'required|exists:products,id','items.*.quantity'=>'required|integer|min:1']);
  $supplier=Supplier::active()->findOrFail($d['supplier_id']);
  DB::transaction(function()use($d,$supplier){
   $po=PurchaseOrder::create(['reference_number'=>PurchaseOrder::generateReferenceNumber(),'supplier_id'=>$supplier->id,'status'=>'pending','order_date'=>today(),'expected_delivery_date'=>$d['expected_delivery_date'],'notes'=>$d['notes']??null,'created_by'=>auth()->id(),'subtotal'=>0,'tax'=>0,'total'=>0]);
   foreach($d['items'] as $row){$product=Product::findOrFail($row['product_id']);$po->items()->create(['product_id'=>$product->id,'product_name'=>$product->name,'quantity'=>$row['quantity'],'unit_price'=>$product->unit_price,'received_quantity'=>0]);}
   $po->calculateTotal();
  });
  return redirect()->route('purchase-orders.index')->with('success','Purchase order created and submitted for approval.');
 }
 public function show(PurchaseOrder $purchaseOrder){$purchaseOrder->load(['supplier','items.product','createdBy','approvedBy']);return view('purchase-orders.show',compact('purchaseOrder'));}
 public function approve(PurchaseOrder $purchaseOrder){abort_unless(auth()->user()->role==='admin'||auth()->user()->role==='manager',403);abort_unless($purchaseOrder->canBeApproved(),403);$purchaseOrder->update(['status'=>'approved','approved_by'=>auth()->id(),'approved_at'=>now()]);return back()->with('success','Purchase order approved.');}
 public function reject(Request $r,PurchaseOrder $purchaseOrder){abort_unless(auth()->user()->role==='admin'||auth()->user()->role==='manager',403);$d=$r->validate(['rejection_reason'=>'required|string|max:1000']);abort_unless($purchaseOrder->status==='pending',403);$purchaseOrder->update(['status'=>'rejected','rejection_reason'=>$d['rejection_reason']]);return back()->with('success','Purchase order rejected.');}
 public function send(PurchaseOrder $purchaseOrder){abort_unless(auth()->user()->role==='admin'||auth()->user()->role==='manager',403);abort_unless($purchaseOrder->status==='approved',403);$purchaseOrder->update(['status'=>'sent']);return back()->with('success','Order marked as sent to supplier.');}
 public function receive(PurchaseOrder $purchaseOrder){
  abort_unless(in_array(auth()->user()->role,['admin','warehouse'],true),403);abort_unless($purchaseOrder->canBeReceived(),403);
  DB::transaction(function()use($purchaseOrder){$purchaseOrder->load('items');foreach($purchaseOrder->items as $item){$remaining=$item->quantity-$item->received_quantity;if($remaining>0){InventoryTransaction::create(['product_id'=>$item->product_id,'user_id'=>auth()->id(),'type'=>'in','quantity'=>$remaining,'reference_document'=>$purchaseOrder->reference_number,'notes'=>'Received against purchase order '.$purchaseOrder->reference_number]);$item->update(['received_quantity'=>$item->quantity]);}}$purchaseOrder->update(['status'=>'received','received_at'=>now()]);});
  return back()->with('success','Items received and posted through the Lab 6 InventoryTransactionObserver.');
 }
}
