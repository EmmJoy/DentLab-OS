@extends('layouts.dashboard')

@section('title', 'Edit Inventory Item - SmileCare')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div class="page-title">
        <i class="fas fa-edit me-3"></i>
        <h1>Edit Inventory Item</h1>
    </div>
    <a href="{{ route('lab.inventory.show', $inventory) }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-2"></i>Back to Item Details
    </a>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-box me-2"></i>Update Item Information</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('lab.inventory.update', $inventory) }}">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="item_name" class="form-label">
                                <i class="fas fa-tag me-1"></i>Item Name *
                            </label>
                            <input type="text" class="form-control @error('item_name') is-invalid @enderror" 
                                   id="item_name" name="item_name" value="{{ old('item_name', $inventory->item_name) }}" required>
                            @error('item_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="category" class="form-label">
                                <i class="fas fa-folder me-1"></i>Category *
                            </label>
                            <select class="form-select @error('category') is-invalid @enderror" id="category" name="category" required>
                                <option value="">Select Category</option>
                                <option value="Dental Tools" {{ old('category', $inventory->category) == 'Dental Tools' ? 'selected' : '' }}>Dental Tools</option>
                                <option value="Medications" {{ old('category', $inventory->category) == 'Medications' ? 'selected' : '' }}>Medications</option>
                                <option value="Consumables" {{ old('category', $inventory->category) == 'Consumables' ? 'selected' : '' }}>Consumables</option>
                                <option value="Equipment" {{ old('category', $inventory->category) == 'Equipment' ? 'selected' : '' }}>Equipment</option>
                                <option value="Office Supplies" {{ old('category', $inventory->category) == 'Office Supplies' ? 'selected' : '' }}>Office Supplies</option>
                                <option value="Cleaning Supplies" {{ old('category', $inventory->category) == 'Cleaning Supplies' ? 'selected' : '' }}>Cleaning Supplies</option>
                                <option value="Other" {{ old('category', $inventory->category) == 'Other' ? 'selected' : '' }}>Other</option>
                            </select>
                            @error('category')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="quantity" class="form-label">
                                <i class="fas fa-sort-numeric-up me-1"></i>Quantity *
                            </label>
                            <input type="number" class="form-control @error('quantity') is-invalid @enderror" 
                                   id="quantity" name="quantity" value="{{ old('quantity', $inventory->quantity) }}" min="0" required>
                            @error('quantity')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="unit_price" class="form-label">
                                <i class="fas fa-dollar-sign me-1"></i>Unit Price *
                            </label>
                            <input type="number" step="0.01" class="form-control @error('unit_price') is-invalid @enderror" 
                                   id="unit_price" name="unit_price" value="{{ old('unit_price', $inventory->unit_price) }}" min="0" required>
                            @error('unit_price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="supplier" class="form-label">
                                <i class="fas fa-truck me-1"></i>Supplier
                            </label>
                            <input type="text" class="form-control @error('supplier') is-invalid @enderror" 
                                   id="supplier" name="supplier" value="{{ old('supplier', $inventory->supplier) }}">
                            @error('supplier')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="expiry_date" class="form-label">
                                <i class="fas fa-calendar-times me-1"></i>Expiry Date
                            </label>
                            <input type="date" class="form-control @error('expiry_date') is-invalid @enderror" 
                                   id="expiry_date" name="expiry_date" value="{{ old('expiry_date', $inventory->expiry_date ? $inventory->expiry_date->format('Y-m-d') : '') }}">
                            @error('expiry_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="description" class="form-label">
                            <i class="fas fa-align-left me-1"></i>Description
                        </label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" name="description" rows="3" 
                                  placeholder="Enter item description, specifications, or notes">{{ old('description', $inventory->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Update Item
                        </button>
                        <a href="{{ route('lab.inventory.show', $inventory) }}" class="btn btn-secondary">
                            <i class="fas fa-times me-2"></i>Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Current Information</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label text-muted">Current Status</label>
                    <div>
                        <span class="badge {{ $inventory->status == 'available' ? 'bg-success' : ($inventory->status == 'out_of_stock' ? 'bg-danger' : 'bg-warning') }}">
                            {{ ucfirst(str_replace('_', ' ', $inventory->status)) }}
                        </span>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label class="form-label text-muted">Current Total Value</label>
                    <div class="fw-bold text-success">${{ number_format($inventory->quantity * $inventory->unit_price, 2) }}</div>
                </div>
                
                <div class="alert alert-info">
                    <i class="fas fa-lightbulb me-2"></i>
                    <strong>Tips:</strong>
                    <ul class="mb-0 mt-2">
                        <li>Status updates automatically based on quantity</li>
                        <li>Check expiry dates for medications</li>
                        <li>Update supplier info for easy reordering</li>
                    </ul>
                </div>
            </div>
        </div>
        
        <div class="card mt-3">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-calculator me-2"></i>New Total Calculator</h6>
            </div>
            <div class="card-body">
                <div class="mb-2">
                    <label class="form-label">New Total Value:</label>
                    <div id="total-value" class="fw-bold text-success fs-5">${{ number_format($inventory->quantity * $inventory->unit_price, 2) }}</div>
                </div>
                <small class="text-muted">Updates automatically based on quantity × unit price</small>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const quantityInput = document.getElementById('quantity');
    const priceInput = document.getElementById('unit_price');
    const totalValue = document.getElementById('total-value');
    
    function updateTotal() {
        const quantity = parseFloat(quantityInput.value) || 0;
        const price = parseFloat(priceInput.value) || 0;
        const total = quantity * price;
        totalValue.textContent = '$' + total.toFixed(2);
    }
    
    quantityInput.addEventListener('input', updateTotal);
    priceInput.addEventListener('input', updateTotal);
});
</script>
@endsection
