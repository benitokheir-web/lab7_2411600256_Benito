<div class="row g-3">
@foreach(['company_name'=>'Company name','contact_person'=>'Contact person','email'=>'Email','phone'=>'Phone','address'=>'Address','payment_terms'=>'Payment terms','lead_time_days'=>'Lead time (days)','minimum_order_amount'=>'Minimum order amount'] as $field=>$label)
<div class="col-md-6"><label class="form-label">{{ $label }}</label><input class="form-control" name="{{ $field }}" value="{{ old($field,$supplier->$field) }}" @if($field==='email') type="email" @elseif(in_array($field,['lead_time_days','minimum_order_amount'])) type="number" min="0" step="0.01" @endif required></div>
@endforeach
<div class="col-12 form-check ms-2"><input class="form-check-input" type="checkbox" name="is_active" value="1" {{ old('is_active',$supplier->is_active ?? true)?'checked':'' }}><label class="form-check-label">Active supplier</label></div></div>
