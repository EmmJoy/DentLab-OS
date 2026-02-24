@extends('layouts.dashboard')

@section('title', 'Add Lab Technician - SmileCare')

@section('content')
<div class="page-title">
    <i class="fas fa-user-plus me-3"></i>
    <h1>Add New Lab Technician</h1>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-user-md me-2"></i>Technician Information</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.lab-technicians.store') }}">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label">
                                <i class="fas fa-user me-1"></i>Full Name *
                            </label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="username" class="form-label">
                                <i class="fas fa-at me-1"></i>Username *
                            </label>
                            <input type="text" class="form-control @error('username') is-invalid @enderror" 
                                   id="username" name="username" value="{{ old('username') }}" required>
                            @error('username')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">
                                <i class="fas fa-envelope me-1"></i>Email Address
                            </label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                   id="email" name="email" value="{{ old('email') }}">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="password" class="form-label">
                                <i class="fas fa-lock me-1"></i>Password *
                            </label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                   id="password" name="password" required>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Create Technician
                        </button>
                        <a href="{{ route('admin.lab-technicians.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times me-2"></i>Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Information</h6>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <i class="fas fa-lightbulb me-2"></i>
                    <strong>Lab Technician Access:</strong>
                    <ul class="mb-0 mt-2">
                        <li>View patients (read-only)</li>
                        <li>Check inventory (read-only)</li>
                        <li>Data entry functions</li>
                        <li>Cannot create users</li>
                        <li>Cannot modify payments</li>
                    </ul>
                </div>
                
                <div class="alert alert-warning">
                    <i class="fas fa-shield-alt me-2"></i>
                    <strong>Security:</strong>
                    <p class="mb-0 mt-2">Username must be unique. Password should be at least 6 characters long.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
