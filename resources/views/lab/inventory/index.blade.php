@extends('layouts.dashboard')

@section('title', 'Inventory - SmileCare')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div class="page-title">
        <i class="fas fa-boxes me-3"></i>
        <h1>Inventory Items</h1>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('lab.request-cart.create') }}" class="btn btn-success">
            <i class="fas fa-plus me-2"></i>Request to Add Product
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
        @if($inventories->count() > 0)
            <div class="row">
                @foreach($inventories as $inventory)
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title">{{ $inventory->item_name }}</h5>
                            <p class="text-success fw-bold mb-1">${{ number_format($inventory->unit_price, 2) }}</p>
                            <span class="badge bg-{{ $inventory->quantity > 10 ? 'success' : ($inventory->quantity > 0 ? 'warning' : 'danger') }} mb-2">
                                Stock: {{ $inventory->quantity }}
                            </span>
                            <span class="badge bg-{{ $inventory->status === 'available' ? 'success' : ($inventory->status === 'out_of_stock' ? 'danger' : 'warning') }} mb-2">
                                {{ ucfirst(str_replace('_', ' ', $inventory->status)) }}
                            </span>
                            
                            @if($inventory->description)
                                <p class="text-muted small">{{ Str::limit($inventory->description, 80) }}</p>
                            @endif
                            
                            <div class="mt-auto">
                                <div class="d-flex gap-2">
                                    <a href="{{ route('lab.inventory.show', $inventory) }}" class="btn btn-outline-primary btn-sm flex-fill">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                    <a href="{{ route('lab.inventory.edit', $inventory) }}" class="btn btn-outline-warning btn-sm flex-fill">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <div class="d-flex justify-content-center mt-4">
                {{ $inventories->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-boxes fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No inventory items found</h5>
                <p class="text-muted">Request products to be added to the inventory.</p>
                <a href="{{ route('lab.request-cart.create') }}" class="btn btn-success">
                    <i class="fas fa-plus me-2"></i>Request First Product
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
