@extends('layouts.dashboard')

@section('title', 'Payment Details - SmileCare')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div class="page-title">
        <i class="fas fa-credit-card me-3"></i>
        <h1>Payment Details</h1>
    </div>
    <a href="{{ route('lab.payments.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-2"></i>Back to Payments
    </a>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Payment Information</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label text-muted">Patient</label>
                            <div class="fw-bold">
                                <i class="fas fa-user me-2"></i>{{ $payment->patient->FullName }}
                            </div>
                            <small class="text-muted">{{ $payment->patient->PhoneNumber }}</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label text-muted">Amount</label>
                            <div class="fw-bold text-success fs-4">${{ number_format($payment->amount, 2) }}</div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label text-muted">Payment Method</label>
                            <div>
                                <span class="badge bg-primary">
                                    <i class="fas fa-{{ $payment->payment_method == 'cash' ? 'money-bill' : ($payment->payment_method == 'card' ? 'credit-card' : ($payment->payment_method == 'bank_transfer' ? 'university' : 'money-check')) }} me-1"></i>
                                    {{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label text-muted">Payment Date</label>
                            <div>
                                <i class="fas fa-calendar me-2"></i>{{ \Carbon\Carbon::parse($payment->payment_date)->format('M d, Y') }}
                            </div>
                        </div>
                    </div>
                </div>

                @if($payment->description)
                <div class="mb-3">
                    <label class="form-label text-muted">Description/Notes</label>
                    <div class="border rounded p-3 bg-light">{{ $payment->description }}</div>
                </div>
                @endif

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label text-muted">Processed By</label>
                            <div><i class="fas fa-user-tie me-2"></i>{{ $payment->processor->name }}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label text-muted">Recorded On</label>
                            <div><i class="fas fa-clock me-2"></i>{{ $payment->created_at->format('M d, Y g:i A') }}</div>
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
                    <a href="{{ route('lab.payments.edit', $payment) }}" class="btn btn-warning">
                        <i class="fas fa-edit me-2"></i>Edit Payment
                    </a>
                    <button class="btn btn-info" onclick="window.print()">
                        <i class="fas fa-print me-2"></i>Print Receipt
                    </button>
                    <a href="{{ route('lab.patients.show', $payment->patient) }}" class="btn btn-outline-primary">
                        <i class="fas fa-user me-2"></i>View Patient
                    </a>
                    <a href="{{ route('lab.payments.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-list me-2"></i>All Payments
                    </a>
                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                        <i class="fas fa-trash me-2"></i>Delete Payment
                    </button>
                </div>
            </div>
        </div>
        
        <div class="card mt-3">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Patient Payment History</h6>
            </div>
            <div class="card-body">
                @php
                    $patientPayments = $payment->patient->payments;
                    $totalPaid = $patientPayments->sum('amount');
                    $paymentCount = $patientPayments->count();
                @endphp
                
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span>Total Payments:</span>
                    <strong class="text-primary">{{ $paymentCount }}</strong>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span>Total Amount:</span>
                    <strong class="text-success">${{ number_format($totalPaid, 2) }}</strong>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <span>Average Payment:</span>
                    <strong class="text-info">${{ $paymentCount > 0 ? number_format($totalPaid / $paymentCount, 2) : '0.00' }}</strong>
                </div>
            </div>
        </div>
        
        <div class="card mt-3">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Payment Status</h6>
            </div>
            <div class="card-body">
                <div class="alert alert-success">
                    <i class="fas fa-check-circle me-2"></i>
                    <strong>Payment Recorded</strong><br>
                    <small>This payment has been successfully recorded in the system.</small>
                </div>
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
                <p>Are you sure you want to delete this payment record?</p>
                <div class="alert alert-warning">
                    <strong>Payment Details:</strong><br>
                    Patient: {{ $payment->patient->FullName }}<br>
                    Amount: ${{ number_format($payment->amount, 2) }}<br>
                    Date: {{ \Carbon\Carbon::parse($payment->payment_date)->format('M d, Y') }}
                </div>
                <p class="text-danger"><small><i class="fas fa-exclamation-triangle me-1"></i>This action cannot be undone.</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form action="{{ route('lab.payments.destroy', $payment) }}" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete Payment</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
