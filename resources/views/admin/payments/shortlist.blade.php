@extends('layouts.dashboard')

@section('title', 'Payments - Last 15 Days')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div class="page-title">
        <i class="fas fa-calendar-day me-3"></i>
        <h1>Last 15 Days Payments</h1>
        <small class="text-muted ms-2">Since {{ optional($since)->format('M d, Y') }}</small>
    </div>
    <a href="{{ route('admin.payments.index') }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-2"></i>Back to All Payments
    </a>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-list me-2"></i>Payments</h5>
    </div>
    <div class="card-body">
        @if(($payments ?? collect())->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Patient</th>
                            <th>Predict3DId</th>
                            <th>Amount</th>
                            <th>Method</th>
                            <th>Payment Date</th>
                            <th>Processed By</th>
                            <th>Plan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($payments as $r)
                            <tr>
                                <td>{{ $r->patient_full_name ?? 'Unknown' }}</td>
                                <td><code>{{ $r->predict3d_id }}</code></td>
                                <td><strong class="text-success">${{ number_format($r->amount, 2) }}</strong></td>
                                <td>{{ ucfirst(str_replace('_',' ',$r->payment_method)) }}</td>
                                <td>{{ \Carbon\Carbon::parse($r->payment_date)->format('Y-m-d') }}</td>
                                <td><small class="text-muted">{{ $r->processor_name ?? '—' }}</small></td>
                                <td><span class="badge bg-light text-dark">#{{ $r->plan_id }}</span></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-credit-card fa-3x text-muted mb-3"></i>
                <h6 class="text-muted">No payments recorded in the last 15 days.</h6>
            </div>
        @endif
    </div>
</div>
@endsection
