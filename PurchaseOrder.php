<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
class PurchaseOrder extends Model {
 use SoftDeletes;
 protected $fillable=['reference_number','supplier_id','status','order_date','expected_delivery_date','subtotal','tax','total','notes','created_by','approved_by','approved_at','received_at','rejection_reason'];
 protected $casts=['order_date'=>'date','expected_delivery_date'=>'date','approved_at'=>'datetime','received_at'=>'datetime','subtotal'=>'decimal:2','tax'=>'decimal:2','total'=>'decimal:2'];
 public function supplier(){return $this->belongsTo(Supplier::class);}
 public function items(){return $this->hasMany(PurchaseOrderItem::class);}
 public function createdBy(){return $this->belongsTo(User::class,'created_by');}
 public function approvedBy(){return $this->belongsTo(User::class,'approved_by');}
 public function scopePending($q){return $q->where('status','pending');}
 public function scopeActive($q){return $q->whereNotIn('status',['received','cancelled','rejected']);}
 public function scopeByStatus($q,$status){return $q->when($status,fn($x)=>$x->where('status',$status));}
 public static function generateReferenceNumber(): string{return 'PO-'.now()->format('Ymd').'-'.Str::upper(Str::random(5));}
 public function calculateTotal(): void {$this->subtotal=$this->items()->get()->sum(fn($i)=>$i->quantity*$i->unit_price);$this->total=$this->subtotal+($this->tax??0);$this->save();}
 public function canBeApproved(): bool{return $this->status==='pending';}
 public function canBeReceived(): bool{return in_array($this->status,['approved','sent'],true);}
}
