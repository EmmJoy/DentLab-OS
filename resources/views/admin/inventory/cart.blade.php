@extends('layouts.dashboard')

@section('title', 'Cart - SmileCare')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div class="page-title">
        <i class="fas fa-shopping-cart me-3"></i>
        <h1>Your Cart</h1>
    </div>
    <a href="{{ route('admin.inventory.index') }}" class="btn btn-outline-primary">
        <i class="fas fa-arrow-left me-2"></i>Back to Inventory
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="card">
    <div class="card-body">
        @if($cart->items->count() > 0)
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div></div>
                <form action="{{ route('admin.cart.confirm') }}" method="POST">
                    @csrf
                    <button class="btn btn-success"><i class="fas fa-check me-2"></i>Confirm</button>
                </form>
            </div>
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th class="text-end">Unit Price</th>
                            <th class="text-center" style="width: 150px;">Quantity</th>
                            <th class="text-end">Subtotal</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($cart->items as $item)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="me-3" style="width: 64px; height: 64px; overflow: hidden; border-radius: 8px;">
                                        @if($item->product->image)
                                            <img src="{{ asset('storage/' . $item->product->image) }}" style="width:100%; height:100%; object-fit: cover;">
                                        @else
                                            <div class="bg-light w-100 h-100 d-flex align-items-center justify-content-center">
                                                <i class="fas fa-image text-muted"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div>
                                        <div class="fw-semibold">{{ $item->product->name }}</div>
                                        @if($item->product->requester)
                                            <div class="small text-muted">Requested by: {{ $item->product->requester->name }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="text-end">${{ number_format($item->unit_price, 2) }}</td>
                            <td class="text-center">
                                <form action="{{ route('admin.cart.update', $item) }}" method="POST" class="d-flex justify-content-center gap-2">
                                    @csrf
                                    <input type="number" name="quantity" class="form-control form-control-sm" value="{{ $item->quantity }}" min="1" style="width: 80px;">
                                    <button class="btn btn-sm btn-outline-primary">Update</button>
                                </form>
                            </td>
                            <td class="text-end">${{ number_format($item->unit_price * $item->quantity, 2) }}</td>
                            <td class="text-end">
                                <form action="{{ route('admin.cart.delete', $item) }}" method="POST" onsubmit="return confirm('Remove this item?')">
                                    @csrf
                                    <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="3" class="text-end">Total</th>
                            <th class="text-end">${{ number_format($cart->totalAmount(), 2) }}</th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">Your cart is empty</h5>
                <a href="{{ route('admin.inventory.index') }}" class="btn btn-primary mt-2">Browse Products</a>
            </div>
        @endif
    </div>
</div>
<div class="mt-3">
    <a href="{{ route('admin.cart.confirmed') }}" class="btn btn-outline-secondary">
        <i class="fas fa-list me-2"></i>List Confirmed Cart
    </a>
</div>
@endsection


