<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Supplier extends Model {
 use SoftDeletes;
 protected $fillable=['company_name','contact_person','email','phone','address','payment_terms','lead_time_days','minimum_order_amount','is_active'];
 protected $casts=['is_active'=>'boolean','minimum_order_amount'=>'decimal:2'];
 public function products(){return $this->hasMany(Product::class);}
 public function purchaseOrders(){return $this->hasMany(PurchaseOrder::class);}
 public function scopeActive($q){return $q->where('is_active',true);}
 public function scopeWithLeadTime($q,$days){return $q->where('lead_time_days','<=',$days);}
 public function getFullContactAttribute(): string{return trim($this->contact_person.' | '.$this->email.' | '.$this->phone,' |');}
 public function getPerformanceRatingAttribute(): float {
  $received=$this->purchaseOrders()->where('status','received')->count();
  if(!$received)return 0;
  $ontime=$this->purchaseOrders()->where('status','received')->whereColumn('received_at','<=','expected_delivery_date')->count();
  return round($ontime/$received*100,1);
 }
}
