@extends('layouts.dashboard')

@section('title', 'Payment Management - SmileCare')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div class="page-title">
        <i class="fas fa-credit-card me-3"></i>
        <h1>Payment Management</h1>
    </div>
    <a href="{{ route('lab.payments.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i>Record Payment
    </a>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-list me-2"></i>All Payments</h5>
    </div>
    <div class="card-body">
        @if($payments->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Patient</th>
                            <th>Amount</th>
                            <th>Payment Method</th>
                            <th>Payment Date</th>
                            <th>Description</th>
                            <th>Processed By</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($payments as $payment)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle me-3" style="width: 40px; height: 40px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;">
                                        {{ strtoupper(substr($payment->patient->FullName, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="fw-bold">{{ $payment->patient->FullName }}</div>
                                        <small class="text-muted">{{ $payment->patient->PhoneNumber }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <strong class="text-success fs-5">${{ number_format($payment->amount, 2) }}</strong>
                            </td>
                            <td>
                                @php
                                    $methodIcons = [
                                        'cash' => 'money-bill-wave',
                                        'card' => 'credit-card',
                                        'bank_transfer' => 'university',
                                        'check' => 'money-check'
                                    ];
                                    $methodColors = [
                                        'cash' => 'success',
                                        'card' => 'primary',
                                        'bank_transfer' => 'info',
                                        'check' => 'warning'
                                    ];
                                @endphp
                                <span class="badge bg-{{ $methodColors[$payment->payment_method] ?? 'secondary' }}">
                                    <i class="fas fa-{{ $methodIcons[$payment->payment_method] ?? 'question' }} me-1"></i>
                                    {{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}
                                </span>
                            </td>
                            <td>{{ $payment->payment_date->format('M d, Y') }}</td>
                            <td>{{ Str::limit($payment->description ?: 'No description', 30) }}</td>
                            <td>
                                <small class="text-muted">{{ $payment->processor->name }}</small>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('lab.payments.show', $payment) }}" class="btn btn-sm btn-outline-primary" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('lab.payments.edit', $payment) }}" class="btn btn-sm btn-outline-warning" title="Edit Payment">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-outline-danger" title="Delete Payment" 
                                            data-bs-toggle="modal" data-bs-target="#deleteModal{{ $payment->id }}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Summary Stats -->
            <div class="row mt-4">
                <div class="col-md-3">
                    <div class="alert alert-success">
                        <strong>Total Payments: ${{ number_format($payments->sum('amount'), 2) }}</strong>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="alert alert-info">
                        <strong>Total Count: {{ $payments->total() }}</strong>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="alert alert-warning">
                        <strong>This Month: ${{ number_format($payments->where('payment_date', '>=', now()->startOfMonth())->sum('amount'), 2) }}</strong>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="alert alert-primary">
                        <strong>Today: ${{ number_format($payments->where('payment_date', today())->sum('amount'), 2) }}</strong>
                    </div>
                </div>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-4">
                {{ $payments->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-credit-card fa-4x text-muted mb-4"></i>
                <h4 class="text-muted">No payments recorded</h4>
                <p class="text-muted mb-4">Start tracking payments by recording the first transaction.</p>
                <a href="{{ route('lab.payments.create') }}" class="btn btn-primary btn-lg">
                    <i class="fas fa-plus me-2"></i>Record First Payment
                </a>
            </div>
        @endif
    </div>
</div>

<!-- Delete Confirmation Modals -->
@if(count($payments) > 0)
    @foreach($payments as $payment)
        <div class="modal fade" id="deleteModal{{ $payment->id }}" tabindex="-1" aria-labelledby="deleteModalLabel{{ $payment->id }}" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteModalLabel{{ $payment->id }}">Confirm Delete</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to delete this payment record?</p>
                        <div class="alert alert-warning">
                            <strong>Payment Details:</strong><br>
                            Patient: {{ $payment->patient->FullName }}<br>
                            Amount: ${{ number_format($payment->amount, 2) }}<br>
                            Date: {{ $payment->payment_date->format('M d, Y') }}
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
    @endforeach
@endif
@endsection
