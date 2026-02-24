@extends('layouts.dashboard')

@section('title', 'Confirmed Carts - SmileCare')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div class="page-title">
        <i class="fas fa-clipboard-check me-3"></i>
        <h1>Confirmed Carts</h1>
    </div>
    <a href="{{ route('admin.cart.index') }}" class="btn btn-outline-primary">
        <i class="fas fa-shopping-cart me-2"></i>Back to Cart
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
        @if($carts->count() > 0)
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Confirmed At</th>
                            <th>User</th>
                            <th>Items</th>
                            <th class="text-end">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($carts as $cart)
                        <tr>
                            <td>{{ $cart->confirmed_at->format('Y-m-d H:i') }}</td>
                            <td>{{ $cart->user->name }} ({{ $cart->user->role }})</td>
                            <td>
                                <ul class="mb-0 ps-3">
                                    @foreach($cart->items as $it)
                                        <li>{{ $it->product->name }} — Qty: {{ $it->quantity }} @ ${{ number_format($it->unit_price, 2) }}</li>
                                    @endforeach
                                </ul>
                            </td>
                            <td class="text-end">${{ number_format($cart->totalAmount(), 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-center mt-3">
                {{ $carts->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-clipboard-check fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No confirmed carts yet</h5>
            </div>
        @endif
    </div>
</div>
@endsection




