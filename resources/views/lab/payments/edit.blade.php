@extends('layouts.dashboard')

@section('title', 'Edit Payment - SmileCare')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div class="page-title">
        <i class="fas fa-edit me-3"></i>
        <h1>Edit Payment</h1>
    </div>
    <a href="{{ route('lab.payments.show', $payment) }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-2"></i>Back to Payment Details
    </a>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-money-bill me-2"></i>Update Payment Information</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('lab.payments.update', $payment) }}">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="patient_id" class="form-label">
                                <i class="fas fa-user me-1"></i>Patient *
                            </label>
                            <select class="form-select @error('patient_id') is-invalid @enderror" id="patient_id" name="patient_id" required>
                                <option value="">Select Patient</option>
                                @foreach($patients as $patient)
                                    <option value="{{ $patient->id }}" {{ (old('patient_id', $payment->patient_id) == $patient->id) ? 'selected' : '' }}>
                                        {{ $patient->FullName }} - {{ $patient->PhoneNumber }}
                                    </option>
                                @endforeach
                            </select>
                            @error('patient_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="amount" class="form-label">
                                <i class="fas fa-dollar-sign me-1"></i>Amount *
                            </label>
                            <input type="number" step="0.01" class="form-control @error('amount') is-invalid @enderror" 
                                   id="amount" name="amount" value="{{ old('amount', $payment->amount) }}" min="0" required>
                            @error('amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="payment_method" class="form-label">
                                <i class="fas fa-credit-card me-1"></i>Payment Method *
                            </label>
                            <select class="form-select @error('payment_method') is-invalid @enderror" id="payment_method" name="payment_method" required>
                                <option value="">Select Payment Method</option>
                                <option value="cash" {{ old('payment_method', $payment->payment_method) == 'cash' ? 'selected' : '' }}>Cash</option>
                                <option value="card" {{ old('payment_method', $payment->payment_method) == 'card' ? 'selected' : '' }}>Credit/Debit Card</option>
                                <option value="bank_transfer" {{ old('payment_method', $payment->payment_method) == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                <option value="check" {{ old('payment_method', $payment->payment_method) == 'check' ? 'selected' : '' }}>Check</option>
                            </select>
                            @error('payment_method')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="payment_date" class="form-label">
                                <i class="fas fa-calendar me-1"></i>Payment Date *
                            </label>
                            <input type="date" class="form-control @error('payment_date') is-invalid @enderror" 
                                   id="payment_date" name="payment_date" value="{{ old('payment_date', $payment->payment_date ? $payment->payment_date->format('Y-m-d') : '') }}" required>
                            @error('payment_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="description" class="form-label">
                            <i class="fas fa-align-left me-1"></i>Description/Notes
                        </label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" name="description" rows="3" 
                                  placeholder="Enter payment description, treatment details, or notes">{{ old('description', $payment->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Update Payment
                        </button>
                        <a href="{{ route('lab.payments.show', $payment) }}" class="btn btn-secondary">
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
                <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Current Information</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label text-muted">Current Patient</label>
                    <div class="fw-bold">{{ $payment->patient->FullName }}</div>
                    <small class="text-muted">{{ $payment->patient->PhoneNumber }}</small>
                </div>
                
                <div class="mb-3">
                    <label class="form-label text-muted">Current Amount</label>
                    <div class="fw-bold text-success">${{ number_format($payment->amount, 2) }}</div>
                </div>
                
                <div class="mb-3">
                    <label class="form-label text-muted">Current Method</label>
                    <div>
                        <span class="badge bg-primary">
                            {{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}
                        </span>
                    </div>
                </div>
                
                <div class="alert alert-info">
                    <i class="fas fa-lightbulb me-2"></i>
                    <strong>Tips:</strong>
                    <ul class="mb-0 mt-2">
                        <li>Double-check patient selection</li>
                        <li>Verify payment amount</li>
                        <li>Update payment method if needed</li>
                        <li>Add notes for clarity</li>
                    </ul>
                </div>
            </div>
        </div>
        
        <div class="card mt-3">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-history me-2"></i>Payment History</h6>
            </div>
            <div class="card-body">
                <div class="mb-2">
                    <label class="form-label text-muted">Recorded By:</label>
                    <div>{{ $payment->processor->name }}</div>
                </div>
                <div class="mb-2">
                    <label class="form-label text-muted">Recorded On:</label>
                    <div>{{ $payment->created_at->format('M d, Y g:i A') }}</div>
                </div>
                @if($payment->updated_at != $payment->created_at)
                <div class="mb-2">
                    <label class="form-label text-muted">Last Updated:</label>
                    <div>{{ $payment->updated_at->format('M d, Y g:i A') }}</div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
