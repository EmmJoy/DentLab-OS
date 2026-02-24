@extends('layouts.dashboard')

@section('title', 'Lab Technician Dashboard - SmileCare')

@section('content')
<div class="page-title">
    <i class="fas fa-tachometer-alt me-3"></i>
    <h1>Lab Technician Dashboard</h1>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-3 mb-3">
        <div class="stats-card">
            <div class="d-flex align-items-center">
                <div class="stats-icon" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <i class="fas fa-users"></i>
                </div>
                <div class="ms-3">
                    <h3 class="mb-0">{{ $stats['total_patients'] }}</h3>
                    <p class="text-muted mb-0">Total Patients</p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="stats-card">
            <div class="d-flex align-items-center">
                <div class="stats-icon" style="background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div class="ms-3">
                    <h3 class="mb-0">{{ $stats['low_stock_items'] }}</h3>
                    <p class="text-muted mb-0">Low Stock Items</p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="stats-card">
            <div class="d-flex align-items-center">
                <div class="stats-icon" style="background: linear-gradient(135deg, #17a2b8 0%, #6f42c1 100%);">
                    <i class="fas fa-dollar-sign"></i>
                </div>
                <div class="ms-3">
                    <h3 class="mb-0">${{ number_format($stats['today_payments'], 2) }}</h3>
                    <p class="text-muted mb-0">Today's Payments</p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="stats-card">
            <div class="d-flex align-items-center">
                <div class="stats-icon" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%);">
                    <i class="fas fa-clipboard-check"></i>
                </div>
                <div class="ms-3">
                    <h3 class="mb-0">{{ $stats['recent_patients']->count() }}</h3>
                    <p class="text-muted mb-0">Recent Patients</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Access -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-bolt me-2"></i>Quick Access</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <a href="{{ route('lab.patients.index') }}" class="btn btn-primary w-100">
                            <i class="fas fa-users d-block mb-2"></i>
                            View Patients
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="{{ route('lab.inventory.index') }}" class="btn btn-success w-100">
                            <i class="fas fa-boxes d-block mb-2"></i>
                            Check Inventory
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <!-- Data Entry shortcut removed -->
                    </div>
                    <div class="col-md-3 mb-3">
                        <button class="btn btn-outline-secondary w-100" onclick="window.print()">
                            <i class="fas fa-print d-block mb-2"></i>
                            Print Report
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Patients -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-clock me-2"></i>Recent Patients</h5>
            </div>
            <div class="card-body">
                @if($stats['recent_patients']->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Phone</th>
                                    <th>Gender</th>
                                    <th>Age</th>
                                    <th>Status</th>
                                    <th>Added On</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($stats['recent_patients'] as $patient)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-circle me-2" style="width: 40px; height: 40px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;">
                                                {{ strtoupper(substr($patient->FullName, 0, 1)) }}
                                            </div>
                                            <div>
                                                <div class="fw-bold">{{ $patient->FullName }}</div>
                                                <small class="text-muted">{{ $patient->DoctorName }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $patient->PhoneNumber }}</td>
                                    <td>
                                        <span class="badge bg-light text-dark">
                                            {{ ucfirst($patient->Gender) }}
                                        </span>
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($patient->DateOfBirth)->age }} years</td>
                                    <td>
                                        <span class="badge {{ $patient->status == 'active' ? 'bg-success' : 'bg-secondary' }}">
                                            {{ ucfirst($patient->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $patient->created_at->format('M d, Y') }}</td>
                                    <td>
                                        <a href="{{ route('lab.patients.show', $patient) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-users fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No patients in system</h5>
                        <p class="text-muted">No patients have been added to the system yet.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
