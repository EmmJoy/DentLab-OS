@extends('layouts.dashboard')

@section('title', 'Product Management - SmileCare')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div class="page-title">
        <i class="fas fa-boxes me-3"></i>
        <h1>Smile Care Inventory</h1>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.products.create') }}" class="btn btn-success">
            <i class="fas fa-plus me-2"></i>Add Product
        </a>
        <a href="{{ route('admin.cart.index') }}" class="btn btn-primary">
            <i class="fas fa-shopping-cart me-2"></i>View Product
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<!-- Pending Requests Section -->
@if($pendingRequests->count() > 0)
<div class="card mb-4 border-warning">
    <div class="card-header bg-warning text-dark">
        <h5 class="mb-0">
            <i class="fas fa-clock me-2"></i>
            Pending Product Requests from Lab Technicians ({{ $pendingRequests->count() }})
        </h5>
    </div>
    <div class="card-body">
        <div class="row">
            @foreach($pendingRequests as $request)
            <div class="col-md-6 col-lg-4 mb-3">
                <div class="card border-warning h-100">
                    <div class="card-header bg-light">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="badge bg-warning">Pending Review</span>
                            <small class="text-muted">{{ $request->created_at->diffForHumans() }}</small>
                        </div>
                    </div>
                    <div class="card-body d-flex flex-column">
                        @if($request->image)
                            <img src="{{ asset('storage/' . $request->image) }}" class="card-img-top mb-3" alt="{{ $request->name }}" style="height: 150px; object-fit: cover;">
                        @else
                            <div class="card-img-top d-flex align-items-center justify-content-center bg-light mb-3" style="height: 150px;">
                                <i class="fas fa-image fa-2x text-muted"></i>
                            </div>
                        @endif
                        
                        <h6 class="card-title">{{ $request->name }}</h6>
                        <p class="text-success fw-bold mb-1">${{ number_format($request->price, 2) }}</p>
                        <p class="text-muted small mb-2">Stock Requested: {{ $request->quantity }}</p>
                        <p class="text-info small mb-2">
                            <i class="fas fa-user me-1"></i>
                            <strong>Requested by: {{ $request->requester->name }}</strong>
                        </p>
                        @if($request->description)
                            <p class="small text-muted mb-3">{{ Str::limit($request->description, 80) }}</p>
                        @endif
                        
                        <div class="mt-auto">
                            <div class="d-grid gap-2">
                                <button class="btn btn-success btn-sm" onclick="approveRequest({{ $request->id }})">
                                    <i class="fas fa-check me-1"></i>Approve & Add to Products
                                </button>
                                <button class="btn btn-danger btn-sm" onclick="rejectRequest({{ $request->id }})">
                                    <i class="fas fa-times me-1"></i>Reject Request
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endif

<!-- Products Grid -->
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
                                    <span class="badge bg-{{ $product->quantity > 10 ? 'success' : ($product->quantity > 0 ? 'warning' : 'danger') }}">
                                        Stock: {{ $product->quantity }}
                                    </span>
                                </div>
                                
                                @if($product->requested_by)
                                    <p class="small text-info mb-2">
                                        <i class="fas fa-user me-1"></i>Requested by: {{ $product->requester->name }}
                                    </p>
                                @endif
                                
                                <div class="d-flex gap-2 mb-3">
                                    <form action="{{ route('admin.cart.add', $product) }}" method="POST" class="d-flex w-100 gap-2">
                                        @csrf
                                        <input type="number" name="quantity" class="form-control form-control-sm" value="1" min="1" max="{{ $product->quantity }}" style="width: 80px;">
                                        <button class="btn btn-primary btn-sm flex-fill" {{ $product->quantity == 0 ? 'disabled' : '' }}>
                                            <i class="fas fa-cart-plus me-1"></i>Take Away
                                        </button>
                                    </form>
                                </div>
                                
                                <div class="btn-group w-100" role="group">
                                    <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-outline-warning btn-sm">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" class="btn btn-outline-danger btn-sm" onclick="deleteProduct({{ $product->id }})">
                                        <i class="fas fa-trash"></i>
                                    </button>
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
                <i class="fas fa-shopping-bag fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No products found</h5>
                <p class="text-muted">Start by adding your first product to the inventory.</p>
                <a href="{{ route('admin.products.create') }}" class="btn btn-success">
                    <i class="fas fa-plus me-2"></i>Add First Product
                </a>
            </div>
        @endif
    </div>
</div>

<!-- Modals -->
<!-- Approve Request Modal -->
<div class="modal fade" id="approveModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Approve Product Request</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to approve this product request?</p>
                <p class="text-success"><small>This will add the product to your inventory.</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="approveForm" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn btn-success">Approve Request</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Reject Request Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reject Product Request</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Please provide a reason for rejecting this request:</p>
                <form id="rejectForm" method="POST">
                    @csrf
                    <div class="mb-3">
                        <textarea name="rejection_reason" class="form-control" rows="3" placeholder="Enter rejection reason..." required></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" form="rejectForm" class="btn btn-danger">Reject Request</button>
            </div>
        </div>
    </div>
</div>

<style>
.product-card {
    transition: transform 0.2s;
}
.product-card:hover {
    transform: translateY(-5px);
}
</style>

<script>
function approveRequest(requestId) {
    if (confirm('Are you sure you want to approve this product request? It will be added to the main inventory.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/product-requests/${requestId}/approve`;
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        
        form.appendChild(csrfToken);
        document.body.appendChild(form);
        form.submit();
    }
}

function rejectRequest(requestId) {
    if (confirm('Are you sure you want to reject this product request? This action cannot be undone.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/product-requests/${requestId}/reject`;
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        
        form.appendChild(csrfToken);
        document.body.appendChild(form);
        form.submit();
    }
}

function deleteProduct(productId) {
    if (confirm('Are you sure you want to delete this product? This action cannot be undone.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/products/${productId}`;
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        
        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'DELETE';
        
        form.appendChild(csrfToken);
        form.appendChild(methodField);
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endsection
