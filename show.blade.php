@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div><h2 class="page-title">{{ $product->name }}</h2><p class="text-muted mb-0">{{ $product->sku }}</p></div>
    <div>
        <a href="{{ route('products.edit', $product) }}" class="btn btn-primary"><i class="bi bi-pencil"></i> Edit</a>
        <a href="{{ route('products.index') }}" class="btn btn-light">Back</a>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-5">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <h5 class="mb-3">Product Information</h5>
                <p><strong>Category:</strong> {{ $product->category }}</p>
                <p><strong>Supplier:</strong> {{ $product->supplier ?: 'N/A' }}</p>
                <p><strong>Quantity:</strong> {{ $product->quantity }}</p>
                <p><strong>Reorder Level:</strong> {{ $product->reorder_level }}</p>
                <p><strong>Unit Price:</strong> ₱{{ number_format($product->unit_price, 2) }}</p>
                <p><strong>Description:</strong><br>{{ $product->description ?: 'No description.' }}</p>
            </div>
        </div>
    </div>
    <div class="col-lg-7">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white"><strong>Transaction History</strong></div>
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead><tr><th>Date</th><th>Type</th><th>Qty</th><th>Customer</th><th>User</th><th>Reference</th><th>Notes</th></tr></thead>
                    <tbody>
                    @forelse($product->transactions as $t)
                        <tr>
                            <td>{{ $t->created_at->format('M d, Y h:i A') }}</td>
                            <td><span class="badge {{ $t->type === 'in' ? 'bg-success' : ($t->type === 'out' ? 'bg-danger' : 'bg-primary') }}">{{ strtoupper($t->type) }}</span></td>
                            <td>{{ $t->quantity }}</td>
                            <td>{{ $t->customer_name ?: '-' }}</td>
                            <td>{{ $t->user->name ?? 'System' }}</td>
                            <td>{{ $t->reference_document ?: '-' }}</td><td>{{ $t->notes ?: '-' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center py-4">No transactions yet.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
