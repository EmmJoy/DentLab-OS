@extends('layouts.dashboard')

@section('title', 'Inventory Details - SmileCare')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div class="page-title">
        <i class="fas fa-box me-3"></i>
        <h1>Inventory Item Details</h1>
    </div>
    <a href="{{ route('lab.inventory.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-2"></i>Back to Inventory
    </a>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Item Information</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label text-muted">Item Name</label>
                            <div class="fw-bold">{{ $inventory->item_name }}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label text-muted">Category</label>
                            <div>
                                <span class="badge bg-primary">
                                    <i class="fas fa-folder me-1"></i>{{ $inventory->category }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label text-muted">Quantity</label>
                            <div class="fw-bold">
                                <span class="badge {{ $inventory->quantity < 5 ? 'bg-danger' : ($inventory->quantity < 10 ? 'bg-warning' : 'bg-success') }} fs-6">
                                    <i class="fas fa-sort-numeric-up me-1"></i>{{ $inventory->quantity }}
                                </span>
                                @if($inventory->quantity < 5)
                                    <small class="text-danger ms-2">Critical Stock</small>
                                @elseif($inventory->quantity < 10)
                                    <small class="text-warning ms-2">Low Stock</small>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label text-muted">Unit Price</label>
                            <div class="fw-bold text-success fs-5">${{ number_format($inventory->unit_price, 2) }}</div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label text-muted">Total Value</label>
                            <div class="fw-bold text-success fs-5">${{ number_format($inventory->quantity * $inventory->unit_price, 2) }}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label text-muted">Status</label>
                            <div>
                                <span class="badge {{ $inventory->status == 'available' ? 'bg-success' : ($inventory->status == 'out_of_stock' ? 'bg-danger' : 'bg-warning') }}">
                                    <i class="fas fa-{{ $inventory->status == 'available' ? 'check' : ($inventory->status == 'out_of_stock' ? 'times' : 'exclamation-triangle') }} me-1"></i>
                                    {{ ucfirst(str_replace('_', ' ', $inventory->status)) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                @if($inventory->supplier)
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label text-muted">Supplier</label>
                            <div><i class="fas fa-truck me-2"></i>{{ $inventory->supplier }}</div>
                        </div>
                    </div>
                    @if($inventory->expiry_date)
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label text-muted">Expiry Date</label>
                            <div>
                                <i class="fas fa-calendar-times me-2"></i>{{ \Carbon\Carbon::parse($inventory->expiry_date)->format('M d, Y') }}
                                @if(\Carbon\Carbon::parse($inventory->expiry_date)->isPast())
                                    <span class="badge bg-danger ms-2">Expired</span>
                                @elseif(\Carbon\Carbon::parse($inventory->expiry_date)->diffInDays() <= 30)
                                    <span class="badge bg-warning ms-2">Expires Soon</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
                @endif

                @if($inventory->description)
                <div class="mb-3">
                    <label class="form-label text-muted">Description</label>
                    <div class="border rounded p-3 bg-light">{{ $inventory->description }}</div>
                </div>
                @endif

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label text-muted">Managed By</label>
                            <div><i class="fas fa-user me-2"></i>{{ $inventory->manager->name }}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label text-muted">Added On</label>
                            <div><i class="fas fa-calendar me-2"></i>{{ $inventory->created_at->format('M d, Y g:i A') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-tools me-2"></i>Available Actions</h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('lab.inventory.edit', $inventory) }}" class="btn btn-warning">
                        <i class="fas fa-edit me-2"></i>Edit Item
                    </a>
                    <button class="btn btn-info" onclick="window.print()">
                        <i class="fas fa-print me-2"></i>Print Details
                    </button>
                    <a href="{{ route('lab.inventory.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-list me-2"></i>All Inventory
                    </a>
                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                        <i class="fas fa-trash me-2"></i>Delete Item
                    </button>
                </div>
            </div>
        </div>
        
        <div class="card mt-3">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-chart-line me-2"></i>Stock Analysis</h6>
            </div>
            <div class="card-body">
                @if($inventory->quantity >= 10)
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle me-2"></i>
                        <strong>Good Stock Level</strong><br>
                        <small>Current stock is adequate.</small>
                    </div>
                @elseif($inventory->quantity >= 5)
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Low Stock Alert</strong><br>
                        <small>Consider reordering soon.</small>
                    </div>
                @else
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        <strong>Critical Stock Level</strong><br>
                        <small>Immediate reordering required!</small>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete inventory item <strong>{{ $inventory->item_name }}</strong>?</p>
                <p class="text-danger"><small><i class="fas fa-exclamation-triangle me-1"></i>This action cannot be undone.</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form action="{{ route('lab.inventory.destroy', $inventory) }}" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete Item</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
