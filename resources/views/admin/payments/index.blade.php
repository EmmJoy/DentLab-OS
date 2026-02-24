@extends('layouts.dashboard')

@section('title', 'Payment Management - SmileCare')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div class="page-title">
        <i class="fas fa-credit-card me-3"></i>
        <h1>Payment Management</h1>
    </div>

<!-- Export Payments by Date Range Modal -->
<div class="modal fade" id="exportPaymentsRangeModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fas fa-calendar me-2"></i>Export Payments by Date Range</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form method="GET" action="{{ route('admin.payments.export.range') }}" class="needs-validation" novalidate>
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">From <span class="text-danger">*</span></label>
              <input type="date" name="from" class="form-control" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">To <span class="text-danger">*</span></label>
              <input type="date" name="to" class="form-control" required>
            </div>
          </div>
          <div class="mt-2 small text-muted">The exported file will be an Excel (.xlsx) file.</div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-success"><i class="fas fa-file-export me-2"></i>Export</button>
        </div>
      </form>
    </div>
  </div>
</div>
    <div class="d-flex gap-2">
        <div class="btn-group">
            <button type="button" class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fas fa-filter me-2"></i>Shortlist
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                <li>
                    <a class="dropdown-item" href="{{ route('admin.payments.shortlist.last15') }}">
                        <i class="fas fa-calendar-day me-2"></i>Last 15 days payments
                    </a>
                </li>
                <li>
                    <button type="button" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#exportPaymentsRangeModal">
                        <i class="fas fa-calendar me-2"></i>Download payments by date range
                    </button>
                </li>
                <li>
                    <a class="dropdown-item" href="{{ route('admin.payments.export.all') }}">
                        <i class="fas fa-file-excel me-2"></i>Download all payments (Excel)
                    </a>
                </li>
                <li>
                    <a class="dropdown-item" href="{{ route('admin.payments.installments.pending') }}">
                        <i class="fas fa-list me-2"></i>Installments (unpaid)
                    </a>
                </li>
            </ul>
        </div>
        <a href="{{ route('admin.payments.plan.index') }}" class="btn btn-primary">
            <i class="fas fa-file-invoice-dollar me-2"></i>Record Payment by 3D Predict ID
        </a>
    </div>
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
                            <th>Total Amount</th>
                            <th>Payment Method</th>
                            <th>Payment Type</th>
                            <th>Payment Date</th>
                            <th>Remaining Balance</th>
                            <th>Total Paid Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($payments as $payment)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle me-3" style="width: 40px; height: 40px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;">
                                        {{ strtoupper(substr($payment->patient_full_name ?? 'P', 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="fw-bold">
                                            {{ $payment->patient_full_name ?? 'Unknown' }}
                                            @php $st = strtolower($payment->patient_status ?? ''); @endphp
                                            <span class="badge ms-2 {{ $st === 'active' ? 'bg-success' : 'bg-secondary' }}">{{ ucfirst($payment->patient_status ?? 'unknown') }}</span>
                                        </div>
                                        <small class="text-muted">ID: {{ $payment->predict3d_id }} {{ $payment->patient_phone ? '· '.$payment->patient_phone : '' }}</small>
                                    </div>
                                </div>
                            </td>
                            <td><strong class="text-success fs-6">${{ number_format($payment->total_amount ?? 0, 2) }}</strong></td>
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
                            <td>
                                @if($payment->is_installment)
                                    <span class="badge bg-info">Installment</span>
                                    @if($payment->next_payment_date)
                                        <div class="small text-muted">Next: {{ \Carbon\Carbon::parse($payment->next_payment_date)->format('M d, Y') }}</div>
                                    @endif
                                @else
                                    <span class="badge bg-success">Full</span>
                                @endif
                            </td>
                            <td>{{ \Carbon\Carbon::parse($payment->payment_date)->format('M d, Y') }}</td>
                            <td><strong>${{ number_format($payment->remaining_amount ?? 0, 2) }}</strong></td>
                            <td><strong class="text-primary">${{ number_format($payment->total_paid ?? 0, 2) }}</strong></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Summary Stats -->
            <div class="row mt-4">
                <div class="col-md-4">
                    <div class="alert alert-success">
                        <strong>Grand Total: ${{ number_format($grandTotal ?? 0, 2) }}</strong>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="alert alert-warning">
                        <strong>This Month: ${{ number_format($totalThisMonth ?? 0, 2) }}</strong>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="alert alert-primary">
                        <strong>Today: ${{ number_format($totalToday ?? 0, 2) }}</strong>
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
                <a href="{{ route('admin.payments.plan.index') }}" class="btn btn-primary btn-lg">
                    <i class="fas fa-plus me-2"></i>Record First Payment
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
