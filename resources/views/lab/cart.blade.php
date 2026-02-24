@extends('layouts.dashboard')

@section('title', 'My Cart - Lab')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div class="page-title">
        <i class="fas fa-shopping-cart me-3"></i>
        <h1>My Cart</h1>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('lab.products.index') }}" class="btn btn-outline-primary">
            <i class="fas fa-boxes me-2"></i>Browse Products
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
        @php $total = 0; @endphp
        @if($cart->items->count())
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th class="text-end" style="width: 120px;">Unit Price</th>
                            <th class="text-center" style="width: 150px;">Quantity</th>
                            <th class="text-end" style="width: 120px;">Subtotal</th>
                            <th style="width: 80px;"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($cart->items as $item)
                            @php $subtotal = $item->unit_price * $item->quantity; $total += $subtotal; @endphp
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        @if($item->product && $item->product->image)
                                            <img src="{{ asset('storage/' . $item->product->image) }}" alt="{{ $item->product->name }}" class="me-3 rounded" style="width: 48px; height: 48px; object-fit: cover;">
                                        @else
                                            <div class="me-3 bg-light d-flex align-items-center justify-content-center" style="width:48px;height:48px;border-radius:6px;"><i class="fas fa-image text-muted"></i></div>
                                        @endif
                                        <div>
                                            <div class="fw-semibold">{{ $item->product->name ?? 'Deleted product' }}</div>
                                            <small class="text-muted">Stock left: {{ $item->product->quantity ?? 0 }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-end">${{ number_format($item->unit_price, 2) }}</td>
                                <td class="text-center">
                                    <form action="{{ route('lab.cart.update', $item) }}" method="POST" class="d-inline-flex align-items-center gap-2">
                                        @csrf
                                        <input type="number" name="quantity" class="form-control form-control-sm" value="{{ $item->quantity }}" min="1" style="width:90px;">
                                        <button class="btn btn-sm btn-outline-primary"><i class="fas fa-sync"></i></button>
                                    </form>
                                </td>
                                <td class="text-end">${{ number_format($subtotal, 2) }}</td>
                                <td>
                                    <form action="{{ route('lab.cart.delete', $item) }}" method="POST" onsubmit="return confirm('Remove this item?')">
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
                            <th class="text-end">${{ number_format($total, 2) }}</th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <div class="d-flex justify-content-end">
                <form action="{{ route('lab.cart.confirm') }}" method="POST">
                    @csrf
                    <button class="btn btn-success"><i class="fas fa-check me-2"></i>Confirm</button>
                </form>
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">Your cart is empty</h5>
                <a href="{{ route('lab.products.index') }}" class="btn btn-primary mt-2"><i class="fas fa-boxes me-2"></i>Browse Products</a>
            </div>
        @endif
    </div>
</div>
@endsection
