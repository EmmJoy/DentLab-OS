@extends('layouts.dashboard')

@section('title', 'Confirmed Carts - Lab')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div class="page-title">
        <i class="fas fa-list me-3"></i>
        <h1>Confirmed Carts</h1>
    </div>
    <a href="{{ route('lab.cart.index') }}" class="btn btn-outline-primary">
        <i class="fas fa-shopping-cart me-2"></i>My Cart
    </a>
</div>

<div class="card">
    <div class="card-body">
        @if($carts->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Items</th>
                            <th class="text-end">Total</th>
                            <th>Confirmed At</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($carts as $cart)
                        @php $total = $cart->items->sum(fn($i) => $i->unit_price * $i->quantity); @endphp
                        <tr>
                            <td>
                                <div class="fw-semibold">{{ $cart->user->name ?? 'Unknown' }}</div>
                                <small class="text-muted">ID: {{ $cart->user_id }}</small>
                            </td>
                            <td>
                                <ul class="mb-0">
                                    @foreach($cart->items as $item)
                                        <li>{{ $item->product->name ?? 'Deleted product' }} × {{ $item->quantity }} @ ${{ number_format($item->unit_price, 2) }}</li>
                                    @endforeach
                                </ul>
                            </td>
                            <td class="text-end">${{ number_format($total, 2) }}</td>
                            <td>{{ optional($cart->confirmed_at)->format('M d, Y H:i') }}</td>
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
                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No confirmed carts yet</h5>
            </div>
        @endif
    </div>
</div>
@endsection
