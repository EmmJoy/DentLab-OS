@extends('layouts.dashboard')

@section('title', 'Product Details - SmileCare')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div class="page-title">
        <i class="fas fa-eye me-3"></i>
        <h1>Product Details</h1>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-warning">
            <i class="fas fa-edit me-2"></i>Edit Product
        </a>
        <a href="{{ route('admin.inventory.index') }}" class="btn btn-outline-primary">
            <i class="fas fa-arrow-left me-2"></i>Back to Products
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-5">
        <div class="card">
            <div class="card-body">
                <div class="border rounded overflow-hidden" style="height: 300px;">
                    @if($product->image)
                        <img src="{{ asset('storage/' . $product->image) }}" style="width:100%; height:100%; object-fit: cover;">
                    @else
                        <div class="bg-light w-100 h-100 d-flex align-items-center justify-content-center">
                            <i class="fas fa-image fa-3x text-muted"></i>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-7">
        <div class="card">
            <div class="card-body">
                <h3 class="card-title mb-3">{{ $product->name }}</h3>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="d-flex align-items-center mb-2">
                            <i class="fas fa-dollar-sign text-success me-2"></i>
                            <span class="h4 text-success mb-0">${{ number_format($product->price, 2) }}</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex align-items-center mb-2">
                            <i class="fas fa-boxes text-info me-2"></i>
                            <span class="h5 mb-0">Stock: {{ $product->quantity }}</span>
                        </div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <span class="badge bg-{{ $product->status === 'active' ? 'success' : 'secondary' }} fs-6">
                        {{ ucfirst($product->status) }}
                    </span>
                </div>
                
                @if($product->description)
                    <div class="mb-3">
                        <h6>Description</h6>
                        <p class="text-muted">{{ $product->description }}</p>
                    </div>
                @endif
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <small class="text-muted">Created by:</small>
                        <div>{{ $product->creator->name ?? 'Unknown' }}</div>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted">Created on:</small>
                        <div>{{ $product->created_at->format('M d, Y H:i') }}</div>
                    </div>
                </div>
                
                @if($product->requested_by)
                    <div class="mb-3">
                        <small class="text-muted">Requested by:</small>
                        <div class="text-info">{{ $product->requester->name ?? 'Unknown' }}</div>
                    </div>
                @endif
                
                <div class="mb-3">
                    <small class="text-muted">Last updated:</small>
                    <div>{{ $product->updated_at->format('M d, Y H:i') }}</div>
                </div>
            </div>
        </div>
        
        <!-- Quick Actions -->
        <div class="card mt-3">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-bolt me-2"></i>Quick Actions</h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-warning">
                        <i class="fas fa-edit me-2"></i>Edit Product
                    </a>
                    <form action="{{ route('admin.products.destroy', $product) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this product?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger w-100">
                            <i class="fas fa-trash me-2"></i>Delete Product
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
