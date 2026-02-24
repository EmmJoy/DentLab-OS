@extends('layouts.dashboard')

@section('title', 'Request Cart - SmileCare')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div class="page-title">
        <i class="fas fa-clipboard-list me-3"></i>
        <h1>My Requests</h1>
    </div>
    <a href="{{ route('lab.request-cart.create') }}" class="btn btn-success">
        <i class="fas fa-plus me-2"></i>New Request
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
        @if($myRequests->count() > 0)
            <div class="row">
                @foreach($myRequests as $req)
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card h-100 shadow-sm">
                        @if($req->image)
                            <img src="{{ asset('storage/' . $req->image) }}" class="card-img-top" alt="{{ $req->name }}" style="height: 200px; object-fit: cover;">
                        @else
                            <div class="card-img-top d-flex align-items-center justify-content-center bg-light" style="height: 200px;">
                                <i class="fas fa-image fa-3x text-muted"></i>
                            </div>
                        @endif
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title">{{ $req->name }}</h5>
                            <p class="text-success fw-bold mb-1">${{ number_format($req->price, 2) }}</p>
                            <span class="badge bg-secondary mb-2">Qty: {{ $req->quantity }}</span>
                            @if($req->description)
                                <p class="text-muted small">{{ Str::limit($req->description, 80) }}</p>
                            @endif
                            <div class="mt-auto">
                                <span class="badge bg-{{ $req->status === 'approved' ? 'success' : ($req->status === 'rejected' ? 'danger' : 'warning') }}">{{ ucfirst($req->status) }}</span>
                                @if($req->status === 'rejected' && $req->rejection_reason)
                                    <div class="small text-danger mt-1">Reason: {{ $req->rejection_reason }}</div>
                                @endif
                                <a href="{{ route('lab.request-cart.show', $req) }}" class="btn btn-outline-primary btn-sm w-100 mt-2">Details</a>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <div class="d-flex justify-content-center mt-4">
                {{ $myRequests->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-clipboard-list fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No requests yet</h5>
                <a href="{{ route('lab.request-cart.create') }}" class="btn btn-success mt-2">Create your first request</a>
            </div>
        @endif
    </div>
</div>
@endsection


