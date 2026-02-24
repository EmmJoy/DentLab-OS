@extends('layouts.dashboard')

@section('title', 'Products - Lab')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div class="page-title">
        <i class="fas fa-shopping-bag me-3"></i>
        <h1>Available Products</h1>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('lab.cart.index') }}" class="btn btn-primary">
            <i class="fas fa-shopping-cart me-2"></i>View Cart
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="card">
    <div class="card-body">
        @if($products->count() > 0)
            <div class="row">
                @foreach($products as $product)
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card h-100 shadow-sm product-card">
                        @if($product->image)
                            <img src="{{ asset('storage/' . $product->image) }}" class="card-img-top" alt="{{ $product->name }}" style="height: 200px; object-fit: cover;">
                        @else
                            <div class="card-img-top d-flex align-items-center justify-content-center bg-light" style="height: 200px;">
                                <i class="fas fa-image fa-3x text-muted"></i>
                            </div>
                        @endif
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title">{{ $product->name }}</h5>
                            @if($product->description)
                                <p class="card-text text-muted small">{{ Str::limit($product->description, 80) }}</p>
                            @endif
                            <div class="mt-auto">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <span class="h5 text-success mb-0">${{ number_format($product->price, 2) }}</span>
                                    <span class="badge bg-{{ $product->quantity > 10 ? 'success' : ($product->quantity > 0 ? 'warning' : 'danger') }}">Stock: {{ $product->quantity }}</span>
                                </div>
                                <div class="d-flex gap-2 mb-2">
                                    <form action="{{ route('lab.cart.add', $product) }}" method="POST" class="d-flex w-100 gap-2">
                                        @csrf
                                        <input type="number" name="quantity" class="form-control form-control-sm" value="1" min="1" max="{{ $product->quantity }}" style="width: 80px;">
                                        <button class="btn btn-primary btn-sm flex-fill" {{ $product->quantity == 0 ? 'disabled' : '' }}>
                                            <i class="fas fa-cart-plus me-1"></i>Add to Cart
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            <div class="d-flex justify-content-center mt-4">
                {{ $products->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No products available</h5>
            </div>
        @endif
    </div>
</div>
@endsection
