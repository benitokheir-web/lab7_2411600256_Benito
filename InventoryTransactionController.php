<?php
namespace App\Http\Controllers;
use App\Models\InventoryTransaction;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class InventoryTransactionController extends Controller
{
    public function index(Request $request) {
        $query = InventoryTransaction::with(['product','user'])->latest();
        if ($request->filled('product_id')) $query->where('product_id',$request->integer('product_id'));
        if ($request->filled('type')) $query->where('type',$request->input('type'));
        if ($request->filled('from')) $query->whereDate('created_at','>=',$request->input('from'));
        if ($request->filled('to')) $query->whereDate('created_at','<=',$request->input('to'));
        $transactions=$query->paginate(20)->withQueryString();
        $products=Product::orderBy('name')->get(['id','name']);
        return view('transactions.index',compact('transactions','products'));
    }
    public function create(string $type) {
        abort_unless(in_array($type,['in','out','adjustment'],true),404);
        $products=Product::orderBy('name')->get();
        return view('transactions.create',compact('type','products'));
    }
    public function store(Request $request,string $type) {
        abort_unless(in_array($type,['in','out','adjustment'],true),404);
        $data=$request->validate([
            'product_id'=>['required','integer','exists:products,id'],
            'quantity'=>['required','integer','min:'.($type==='adjustment'?'0':'1')],
            'reference_document'=>['nullable','string','max:255'],
            'customer_name'=>[$type==='out'?'required':'nullable','string','max:255'],
            'notes'=>['nullable','string','max:2000'],
        ]);
        DB::transaction(function() use ($data,$type) {
            $product=Product::query()->lockForUpdate()->findOrFail($data['product_id']);
            if($type==='out' && $data['quantity']>$product->quantity) {
                throw ValidationException::withMessages(['quantity'=>'Stock out cannot exceed available stock ('.$product->quantity.').']);
            }
            $quantity=(int)$data['quantity'];
            if($type==='adjustment') $data['notes']=trim(($data['notes']??'').' | Physical count adjustment from '.$product->quantity.' to '.$quantity);
            InventoryTransaction::create([
                'product_id'=>$product->id,'user_id'=>auth()->id(),'type'=>$type,'quantity'=>$quantity,
                'reference_document'=>$data['reference_document']??null,'customer_name'=>$data['customer_name']??null,
                'notes'=>$data['notes']??null,
            ]);
        });
        return redirect()->route('transactions.index')->with('success','Inventory transaction saved and stock updated.');
    }
    public function show(InventoryTransaction $transaction) {
        $transaction->load(['product','user']);
        return view('transactions.show',compact('transaction'));
    }
}
