@extends('layouts.dashboard')

@section('title', 'Data Entry Portal - SmileCare')

@section('content')
<div class="page-title">
    <i class="fas fa-keyboard me-3"></i>
    <h1>Data Entry Portal</h1>
    <small class="text-muted ms-3">(Lab Technician Access)</small>
</div>

<div class="row">
    <div class="col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-users me-2"></i>Patient Information</h5>
            </div>
            <div class="card-body">
                <p class="text-muted">View and access patient information in the system.</p>
                <a href="{{ route('lab.patients.index') }}" class="btn btn-primary w-100">
                    <i class="fas fa-eye me-2"></i>View Patients
                </a>
            </div>
        </div>
    </div>
    
    <div class="col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-boxes me-2"></i>Inventory Check</h5>
            </div>
            <div class="card-body">
                <p class="text-muted">Check current inventory levels and item availability.</p>
                <a href="{{ route('lab.inventory.index') }}" class="btn btn-success w-100">
                    <i class="fas fa-search me-2"></i>Check Inventory
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-chart-bar me-2"></i>System Overview</h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-3">
                        <div class="stats-card">
                            <i class="fas fa-users fa-2x text-primary mb-2"></i>
                            <h4>{{ \App\Models\Patient::count() }}</h4>
                            <p class="text-muted">Total Patients</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stats-card">
                            <i class="fas fa-boxes fa-2x text-success mb-2"></i>
                            <h4>{{ \App\Models\Inventory::count() }}</h4>
                            <p class="text-muted">Inventory Items</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stats-card">
                            <i class="fas fa-exclamation-triangle fa-2x text-warning mb-2"></i>
                            <h4>{{ \App\Models\Inventory::where('quantity', '<', 10)->count() }}</h4>
                            <p class="text-muted">Low Stock Items</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stats-card">
                            <i class="fas fa-calendar fa-2x text-info mb-2"></i>
                            <h4>{{ \Carbon\Carbon::now()->format('M d') }}</h4>
                            <p class="text-muted">Today's Date</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-12">
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>
            <strong>Lab Technician Permissions:</strong>
            <ul class="mb-0 mt-2">
                <li>View patient information (read-only)</li>
                <li>Check inventory levels (read-only)</li>
                <li>Access data entry functions</li>
                <li>Generate reports and print information</li>
            </ul>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="alert alert-warning">
            <i class="fas fa-shield-alt me-2"></i>
            <strong>Access Restrictions:</strong>
            <p class="mb-0 mt-2">Lab technicians cannot create new users, modify payments, or make changes to patient/inventory records. Contact an administrator for these functions.</p>
        </div>
    </div>
</div>
@endsection
