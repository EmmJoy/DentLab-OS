@extends('layouts.dashboard')

@section('title', 'Pending Installments - SmileCare')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div class="page-title">
        <i class="fas fa-tasks me-3"></i>
        <h1>Pending Installments</h1>
        <small class="text-muted ms-2">Showing installment plans with remaining balance</small>
    </div>
    <a href="{{ route('admin.payments.index') }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-2"></i>Back to All Payments
    </a>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-list me-2"></i>Installments</h5>
    </div>
    <div class="card-body">
        @if(($plans ?? collect())->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Patient</th>
                            <th>Predict3DId</th>
                            <th>Total Amount</th>
                            <th>Total Paid</th>
                            <th>Remaining</th>
                            <th>Payment Method</th>
                            <th>Next Payment Date</th>
                            <th>Plan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($plans as $p)
                            <tr>
                                <td>{{ $p->patient_full_name ?? 'Unknown' }}</td>
                                <td><code>{{ $p->predict3d_id }}</code></td>
                                <td><strong class="text-success">${{ number_format($p->total_amount ?? 0, 2) }}</strong></td>
                                <td><strong class="text-primary">${{ number_format($p->total_paid ?? 0, 2) }}</strong></td>
                                <td><strong>${{ number_format($p->remaining_amount ?? 0, 2) }}</strong></td>
                                <td>
                                    <span class="badge bg-light text-dark">{{ ucfirst(str_replace('_',' ',$p->payment_method)) }}</span>
                                </td>
                                <td>
                                    @if($p->next_payment_date)
                                        {{ \Carbon\Carbon::parse($p->next_payment_date)->format('M d, Y') }}
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td><span class="badge bg-light text-dark">#{{ $p->plan_id }}</span></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-list fa-3x text-muted mb-3"></i>
                <h6 class="text-muted">No pending installments found.</h6>
            </div>
        @endif
    </div>
</div>
@endsection
