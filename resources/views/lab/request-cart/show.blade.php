@extends('layouts.dashboard')

@section('title', 'Request Details - SmileCare')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div class="page-title">
        <i class="fas fa-file-alt me-3"></i>
        <h1>Request Details</h1>
    </div>
    <a href="{{ route('lab.request-cart.index') }}" class="btn btn-outline-primary">
        <i class="fas fa-arrow-left me-2"></i>Back
    </a>
</div>

<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-md-5">
                <div class="border rounded overflow-hidden" style="height: 280px;">
                    @if($productRequest->image)
                        <img src="{{ asset('storage/' . $productRequest->image) }}" style="width:100%; height:100%; object-fit: cover;">
                    @else
                        <div class="bg-light w-100 h-100 d-flex align-items-center justify-content-center">
                            <i class="fas fa-image fa-3x text-muted"></i>
                        </div>
                    @endif
                </div>
            </div>
            <div class="col-md-7">
                <h4>{{ $productRequest->name }}</h4>
                <p class="text-success h5">${{ number_format($productRequest->price, 2) }}</p>
                <p class="mb-1"><strong>Quantity:</strong> {{ $productRequest->quantity }}</p>
                <p class="mb-1"><strong>Status:</strong> <span class="badge bg-{{ $productRequest->status === 'approved' ? 'success' : ($productRequest->status === 'rejected' ? 'danger' : 'warning') }}">{{ ucfirst($productRequest->status) }}</span></p>
                @if($productRequest->status !== 'pending')
                    <p class="mb-1"><strong>Reviewed at:</strong> {{ optional($productRequest->reviewed_at)->format('Y-m-d H:i') }}</p>
                @endif
                @if($productRequest->rejection_reason)
                    <p class="text-danger"><strong>Rejection reason:</strong> {{ $productRequest->rejection_reason }}</p>
                @endif

                @if($productRequest->description)
                    <div class="mt-3">
                        <h6>Description</h6>
                        <p class="text-muted">{{ $productRequest->description }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection


