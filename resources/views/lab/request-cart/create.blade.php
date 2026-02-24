@extends('layouts.dashboard')

@section('title', 'Request Product - SmileCare')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div class="page-title">
        <i class="fas fa-plus-square me-3"></i>
        <h1>Request New Product</h1>
    </div>
    <a href="{{ route('lab.request-cart.index') }}" class="btn btn-outline-primary">
        <i class="fas fa-arrow-left me-2"></i>My Requests
    </a>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-clipboard-list me-2"></i>Product Request Form</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('lab.request-cart.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Product Name *</label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Price *</label>
                            <input type="number" step="0.01" name="price" class="form-control @error('price') is-invalid @enderror" value="{{ old('price') }}" required>
                            @error('price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Quantity *</label>
                            <input type="number" name="quantity" min="1" class="form-control @error('quantity') is-invalid @enderror" value="{{ old('quantity') }}" required>
                            @error('quantity')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Description *</label>
                            <textarea name="description" rows="4" class="form-control @error('description') is-invalid @enderror" placeholder="Describe why this product is needed, its purpose, specifications, etc..." required>{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Product Image (Optional)</label>
                            <input type="file" name="image" class="form-control" accept="image/*">
                            <div class="form-text">Max file size: 5MB. Supported formats: JPG, PNG, GIF</div>
                        </div>
                    </div>
                    <div class="mt-4">
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-paper-plane me-2"></i>Submit Request
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Request Guidelines</h5>
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0">
                    <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Provide clear product name</li>
                    <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Set realistic price estimate</li>
                    <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Explain why it's needed</li>
                    <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Add product image if available</li>
                    <li><i class="fas fa-clock text-warning me-2"></i>Admin will review within 24-48 hours</li>
                </ul>
            </div>
        </div>
        
        <div class="card mt-3">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-history me-2"></i>Recent Requests</h5>
            </div>
            <div class="card-body">
                <p class="text-muted small">Your recent product requests will appear here once submitted.</p>
                <a href="{{ route('lab.request-cart.index') }}" class="btn btn-outline-primary btn-sm">
                    <i class="fas fa-list me-1"></i>View All Requests
                </a>
            </div>
        </div>
    </div>
</div>
@endsection


