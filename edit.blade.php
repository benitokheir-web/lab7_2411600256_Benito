@extends('layouts.app')

@section('content')
<div class="mb-4">
    <h2 class="page-title">Edit Product</h2>
    <p class="text-muted">Update product information.</p>
</div>
<div class="card shadow-sm border-0">
    <div class="card-body p-4">
        <form method="POST" action="{{ route('products.update', $product) }}">
            @method('PUT')
            @include('products.form')
            <div class="mt-4">
                <button class="btn btn-primary"><i class="bi bi-save"></i> Update Product</button>
                <a href="{{ route('products.index') }}" class="btn btn-light">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
