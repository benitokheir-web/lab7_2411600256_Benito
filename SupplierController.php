<?php
namespace App\Http\Controllers;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
class SupplierController extends Controller {
 public function index(Request $r){
  $suppliers=Supplier::query()->when($r->search,fn($q,$s)=>$q->where(fn($x)=>$x->where('company_name','like',"%$s%")->orWhere('contact_person','like',"%$s%")->orWhere('email','like',"%$s%")))
   ->when($r->status!==null&&$r->status!=='',fn($q)=>$q->where('is_active',$r->status))->orderBy('company_name')->paginate(10)->withQueryString();
  return view('suppliers.index',compact('suppliers'));
 }
 public function create(){return view('suppliers.create',['supplier'=>new Supplier]);}
 public function store(Request $r){
  $d=$r->validate(['company_name'=>'required|string|max:180','contact_person'=>'required|string|max:150','email'=>'required|email|unique:suppliers,email','phone'=>'required|string|max:30','address'=>'required|string|max:500','payment_terms'=>'required|string|max:80','lead_time_days'=>'required|integer|min:0','minimum_order_amount'=>'required|numeric|min:0']);
  $d['is_active']=$r->boolean('is_active');Supplier::create($d);return redirect()->route('suppliers.index')->with('success','Supplier added.');
 }
 public function show(Supplier $supplier){$supplier->load(['purchaseOrders'=>fn($q)=>$q->latest()->limit(10),'products']);return view('suppliers.show',compact('supplier'));}
 public function edit(Supplier $supplier){return view('suppliers.edit',compact('supplier'));}
 public function update(Request $r,Supplier $supplier){
  $d=$r->validate(['company_name'=>'required|string|max:180','contact_person'=>'required|string|max:150','email'=>['required','email',Rule::unique('suppliers','email')->ignore($supplier->id)],'phone'=>'required|string|max:30','address'=>'required|string|max:500','payment_terms'=>'required|string|max:80','lead_time_days'=>'required|integer|min:0','minimum_order_amount'=>'required|numeric|min:0']);
  $d['is_active']=$r->boolean('is_active');$supplier->update($d);return redirect()->route('suppliers.index')->with('success','Supplier updated.');
 }
 public function destroy(Supplier $supplier){
  if($supplier->purchaseOrders()->whereIn('status',['pending','approved','sent'])->exists())return back()->with('error','Supplier has open purchase orders and cannot be archived.');
  $supplier->delete();return back()->with('success','Supplier archived using soft delete.');
 }
}
