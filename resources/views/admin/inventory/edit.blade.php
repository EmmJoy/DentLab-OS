@extends('layouts.dashboard')

@section('title', 'Edit Product - SmileCare')

@section('content')
<div class="page-title">
    <i class="fas fa-edit me-3"></i>
    <h1>Edit Product</h1>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-box me-2"></i>Product Information</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.products.update', $product) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label">
                                <i class="fas fa-tag me-1"></i>Product Name *
                            </label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name', $product->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="price" class="form-label"><i class="fas fa-dollar-sign me-1"></i>Price *</label>
                            <input type="number" step="0.01" class="form-control @error('price') is-invalid @enderror" id="price" name="price" value="{{ old('price', $product->price) }}" min="0" required>
                            @error('price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="quantity" class="form-label">Stock Quantity <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('quantity') is-invalid @enderror" id="quantity" name="quantity" value="{{ old('quantity', $product->quantity) }}" min="0" required>
                            @error('quantity')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="image" class="form-label">Product Image</label>
                            <input type="file" class="form-control" id="image" name="image" accept="image/*">
                            <div class="form-text">Max file size: 5MB. Supported formats: JPG, PNG, GIF</div>
                            @if($product->image)
                                <div class="mt-2">
                                    <small class="text-muted">Current image:</small>
                                    <img src="{{ asset('storage/' . $product->image) }}" alt="Current image" class="d-block mt-1" style="max-width: 100px; max-height: 100px; object-fit: cover;">
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Product Description</label>
                        <textarea class="form-control" id="description" name="description" rows="4" placeholder="Enter product description...">{{ old('description', $product->description) }}</textarea>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.inventory.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i>Back to Products
                        </a>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save me-1"></i>Update Product
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-eye me-2"></i>Preview</h5>
            </div>
            <div class="card-body">
                <div class="card product-preview">
                    <div id="image-preview" class="card-img-top d-flex align-items-center justify-content-center bg-light" style="height: 150px;">
                        @if($product->image)
                            <img src="{{ asset('storage/' . $product->image) }}" style="width: 100%; height: 150px; object-fit: cover;">
                        @else
                            <i class="fas fa-image fa-2x text-muted"></i>
                        @endif
                    </div>
                    <div class="card-body">
                        <h6 class="card-title" id="preview-name">{{ $product->name }}</h6>
                        <p class="card-text text-muted small" id="preview-description">{{ $product->description ?: 'Product description will appear here...' }}</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="h6 text-success mb-0" id="preview-price">${{ number_format($product->price, 2) }}</span>
                            <span class="badge bg-secondary" id="preview-stock">Stock: {{ $product->quantity }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Live preview functionality
function updatePreview() {
    const name = document.getElementById('name').value || 'Product Name';
    const price = document.getElementById('price').value || '0.00';
    const quantity = document.getElementById('quantity').value || '0';
    const description = document.getElementById('description').value || 'Product description will appear here...';
    
    document.getElementById('preview-name').textContent = name;
    document.getElementById('preview-price').textContent = '$' + parseFloat(price).toFixed(2);
    document.getElementById('preview-stock').textContent = 'Stock: ' + quantity;
    document.getElementById('preview-description').textContent = description.length > 60 ? description.substring(0, 60) + '...' : description;
}

// Image preview
function previewImage(event) {
    const file = event.target.files[0];
    const preview = document.getElementById('image-preview');
    
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.innerHTML = `<img src="${e.target.result}" style="width: 100%; height: 150px; object-fit: cover;">`;
        };
        reader.readAsDataURL(file);
    } else {
        // Show current image if exists, otherwise show placeholder
        const currentImage = '{{ $product->image ? asset("storage/" . $product->image) : "" }}';
        if (currentImage) {
            preview.innerHTML = `<img src="${currentImage}" style="width: 100%; height: 150px; object-fit: cover;">`;
        } else {
            preview.innerHTML = '<i class="fas fa-image fa-2x text-muted"></i>';
        }
    }
}

// Event listeners
document.getElementById('name').addEventListener('input', updatePreview);
document.getElementById('price').addEventListener('input', updatePreview);
document.getElementById('quantity').addEventListener('input', updatePreview);
document.getElementById('description').addEventListener('input', updatePreview);
document.getElementById('image').addEventListener('change', previewImage);

// Initialize preview
updatePreview();
</script>
@endsection
