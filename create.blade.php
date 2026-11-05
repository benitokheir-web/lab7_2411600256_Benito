@extends('layouts.app')

@section('content')
<div class="mb-4">
    <h2 class="page-title">Add Product</h2>
    <p class="text-muted">Create a new inventory item.</p>
</div>
<div class="card shadow-sm border-0">
    <div class="card-body p-4">
        <form method="POST" action="{{ route('products.store') }}">
            @include('products.form')
            <div class="mt-4">
                <button class="btn btn-primary"><i class="bi bi-check-lg"></i> Save Product</button>
                <a href="{{ route('products.index') }}" class="btn btn-light">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
