@extends('layouts.dashboard')

@section('title', 'Data Entry Portal - SmileCare')

@section('content')
<div class="page-title">
    <i class="fas fa-keyboard me-3"></i>
    <h1>Data Entry Portal</h1>
</div>

<div class="row">
    <div class="col-md-4 mb-4">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-user-plus me-2"></i>Patient Entry</h5>
            </div>
            <div class="card-body">
                <p class="text-muted">Quickly add new patients to the system with all required information.</p>
                <a href="{{ route('admin.patients.create') }}" class="btn btn-primary w-100">
                    <i class="fas fa-plus me-2"></i>Add Patient
                </a>
            </div>
        </div>
    </div>
    
    <div class="col-md-4 mb-4">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-boxes me-2"></i>Inventory Entry</h5>
            </div>
            <div class="card-body">
                <p class="text-muted">Add new inventory items and manage stock levels efficiently.</p>
                <a href="{{ route('admin.products.create') }}" class="btn btn-success w-100">
                    <i class="fas fa-plus me-2"></i>Add Inventory
                </a>
            </div>
        </div>
    </div>
    
    <div class="col-md-4 mb-4">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-credit-card me-2"></i>Payment Entry</h5>
            </div>
            <div class="card-body">
                <p class="text-muted">Record patient payments and maintain financial records.</p>
                <a href="{{ route('admin.payments.create') }}" class="btn btn-info w-100">
                    <i class="fas fa-plus me-2"></i>Record Payment
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Quick Statistics</h5>
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
                            <i class="fas fa-credit-card fa-2x text-info mb-2"></i>
                            <h4>{{ \App\Models\Payment::count() }}</h4>
                            <p class="text-muted">Total Payments</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stats-card">
                            <i class="fas fa-user-md fa-2x text-warning mb-2"></i>
                            <h4>{{ \App\Models\User::where('role', 'lab_technician')->count() }}</h4>
                            <p class="text-muted">Lab Technicians</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
