@extends('layouts.dashboard')

@section('title', 'Patient Details - SmileCare')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div class="page-title">
        <i class="fas fa-user me-3"></i>
        <h1>Patient Details</h1>
        <small class="text-muted ms-3">ID: {{ $patient->Predict3DId }}</small>
        <small class="badge bg-info text-dark ms-2">Upper: {{ $patient->UpperCases ?? 0 }} | Lower: {{ $patient->LowerCases ?? 0 }}</small>
    </div>
    <a href="{{ route('lab.patients.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-2"></i>Back to Patients
    </a>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-user me-2"></i>Patient Information</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-2 text-center mb-3">
                        <div class="avatar-circle mx-auto" style="width: 80px; height: 80px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 32px;">
                            {{ strtoupper(substr($patient->Predict3DId, 0, 1)) }}
                        </div>
                    </div>
                    <div class="col-md-10">
                        <div class="row">
                            <div class="col-md-6">
                                <h4 class="mb-1">3D Predict ID: {{ $patient->Predict3DId }}</h4>
                                <p class="text-muted mb-3">
                                    <span class="badge {{ $patient->status == 'active' ? 'bg-success' : 'bg-secondary' }}">
                                        {{ ucfirst($patient->status) }}
                                    </span>
                                </p>
                                <div class="mb-2">
                                    <strong><i class="fas fa-arrow-up me-2"></i>Upper Cases:</strong> {{ $patient->UpperCases ?? 0 }}
                                </div>
                                <div class="mb-2">
                                    <strong><i class="fas fa-arrow-down me-2"></i>Lower Cases:</strong> {{ $patient->LowerCases ?? 0 }}
                                </div>
                                <div class="mb-2">
                                    <strong><i class="fas fa-phone me-2"></i>Phone:</strong> {{ $patient->PhoneNumber }}
                                </div>
                                <div class="mb-2">
                                    <strong><i class="fas fa-exclamation-triangle me-2"></i>Emergency:</strong> {{ $patient->EmergencyContact }}
                                </div>
                                <div class="mb-2">
                                    <strong><i class="fas fa-user-md me-2"></i>Doctor:</strong> {{ $patient->DoctorName }}
                                </div>
                                <div class="mb-2">
                                    <strong><i class="fas fa-clinic-medical me-2"></i>Chamber:</strong> {{ $patient->ChamberName }}
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-2">
                                    <strong><i class="fas fa-calendar me-2"></i>Age:</strong> {{ \Carbon\Carbon::parse($patient->DateOfBirth)->age }} years old
                                </div>
                                <div class="mb-2">
                                    <strong><i class="fas fa-birthday-cake me-2"></i>DOB:</strong> {{ \Carbon\Carbon::parse($patient->DateOfBirth)->format('M d, Y') }}
                                </div>
                                <div class="mb-2">
                                    <strong><i class="fas fa-venus-mars me-2"></i>Gender:</strong> {{ ucfirst($patient->Gender) }}
                                </div>
                                <div class="mb-2">
                                    <strong><i class="fas fa-map-marked-alt me-2"></i>Territory:</strong> {{ $patient->TerritoryName }}
                                </div>
                                <div class="mb-2">
                                    <strong><i class="fas fa-globe me-2"></i>Regional:</strong> {{ $patient->RegionalName }}
                                </div>
                                <div class="mb-2">
                                    <strong><i class="fas fa-user-plus me-2"></i>Added by:</strong> {{ $patient->creator->name }}
                                </div>
                                <div class="mb-2">
                                    <strong><i class="fas fa-clock me-2"></i>Date Added:</strong> {{ $patient->created_at->format('M d, Y') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <hr>
                
                <div class="row">
                    <div class="col-12">
                        <h6><i class="fas fa-map-marker-alt me-2"></i>Address</h6>
                        <p class="text-muted">{{ $patient->Address }}</p>
                    </div>
                </div>
                
                <hr>
                <div class="row">
                    <div class="col-12">
                        <h6><i class="fas fa-tooth me-2"></i>Scanning Information</h6>
                        <div class="alert alert-info">
                            <strong>Scanning For:</strong> {{ $patient->ScanningFor }}
                            @if($patient->ScanningForOthers)
                                <br><strong>Details:</strong> {{ $patient->ScanningForOthers }}
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment History (Read-only) -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-credit-card me-2"></i>Payment History (Read-only)</h5>
            </div>
            <div class="card-body">
                @if($patient->payments->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Amount</th>
                                    <th>Method</th>
                                    <th>Description</th>
                                    <th>Status</th>
                                    <th>Processed By</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($patient->payments as $payment)
                                <tr>
                                    <td>{{ $payment->payment_date->format('M d, Y') }}</td>
                                    <td>
                                        <strong class="text-success">${{ number_format($payment->amount, 2) }}</strong>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark">
                                            <i class="fas fa-{{ $payment->payment_method == 'cash' ? 'money-bill' : ($payment->payment_method == 'card' ? 'credit-card' : 'university') }} me-1"></i>
                                            {{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}
                                        </span>
                                    </td>
                                    <td>{{ $payment->description ?: 'No description' }}</td>
                                    <td>
                                        <span class="badge bg-{{ $payment->status == 'completed' ? 'success' : ($payment->status == 'pending' ? 'warning' : 'danger') }}">
                                            {{ ucfirst($payment->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $payment->processor->name }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-3">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="alert alert-success">
                                    <strong>Total Payments: ${{ number_format($patient->payments->sum('amount'), 2) }}</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-credit-card fa-3x text-muted mb-3"></i>
                        <h6 class="text-muted">No payments recorded</h6>
                        <p class="text-muted">No payment history found for this patient.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-chart-pie me-2"></i>Quick Stats</h6>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span>Total Payments:</span>
                    <strong class="text-success">${{ number_format($patient->payments->sum('amount'), 2) }}</strong>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span>Payment Count:</span>
                    <strong>{{ $patient->payments->count() }}</strong>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span>Last Payment:</span>
                    <strong>
                        @if($patient->payments->count() > 0)
                            {{ $patient->payments->sortByDesc('payment_date')->first()->payment_date->format('M d, Y') }}
                        @else
                            Never
                        @endif
                    </strong>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <span>Patient Since:</span>
                    <strong>{{ $patient->created_at->format('M d, Y') }}</strong>
                </div>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-tools me-2"></i>Available Actions</h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('lab.patients.edit', $patient) }}" class="btn btn-warning">
                        <i class="fas fa-edit me-2"></i>Edit Patient
                    </a>
                    <button class="btn btn-info" onclick="window.print()">
                        <i class="fas fa-print me-2"></i>Print Details
                    </button>
                    <a href="{{ route('lab.patients.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-list me-2"></i>All Patients
                    </a>
                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                        <i class="fas fa-trash me-2"></i>Delete Patient
                    </button>
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
                <p>Are you sure you want to delete patient <strong>{{ $patient->Predict3DId }}</strong>?</p>
                <p class="text-danger"><small><i class="fas fa-exclamation-triangle me-1"></i>This action cannot be undone.</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form action="{{ route('lab.patients.destroy', $patient) }}" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete Patient</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
