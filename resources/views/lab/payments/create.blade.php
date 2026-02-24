@extends('layouts.dashboard')

@section('title', 'Record Payment - SmileCare')

@section('content')
<div class="page-title">
    <i class="fas fa-credit-card me-3"></i>
    <h1>Record New Payment</h1>
</div>

@push('scripts')
<script>
(function(){
  const routeFindLab = "{{ route('lab.production.find-patient', ['predict3dId' => 'PREDICT_ID']) }}";
  const $lookupId = document.getElementById('labPredictId');
  const $btnLookup = document.getElementById('btnLabFetch');
  const $infoWrap = document.getElementById('labPatientInfo');
  const $infoSection = document.getElementById('labPatientInfoSection');

  function computeAge(iso){
    if(!iso) return '';
    const d = new Date(iso); if(Number.isNaN(d.getTime())) return '';
    const years = Math.floor((Date.now()-d.getTime())/(365.25*24*60*60*1000));
    return years>0 ? years+" years" : '';
  }

  $btnLookup?.addEventListener('click', async ()=>{
    const id = ($lookupId.value||'').trim();
    if(!id){ alert('Enter Predict3DId'); return; }
    $infoWrap.innerHTML='';
    $infoSection.classList.add('d-none');
    try{
      const url = routeFindLab.replace('PREDICT_ID', encodeURIComponent(id));
      const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
      if(!res.ok) throw new Error('Patient not found');
      const p = await res.json();
      const age = computeAge(p.DateOfBirth);
      const scanningFor = (p.ScanningFor === 'Others' && p.ScanningForOthers) ? `${p.ScanningFor} (${p.ScanningForOthers})` : (p.ScanningFor||'');
      $infoWrap.innerHTML = `
        <div class="row g-3">
          <div class="col-md-6">
            <div class="d-flex justify-content-between"><span class="fw-semibold">Patient Name:</span><span>${p.FullName ?? ''}</span></div>
            <div class="d-flex justify-content-between"><span class="fw-semibold">Doctor Name:</span><span>${p.DoctorName ?? ''}</span></div>
            <div class="d-flex justify-content-between"><span class="fw-semibold">Scanning For:</span><span>${scanningFor}</span></div>
          </div>
          <div class="col-md-6">
            <div class="d-flex justify-content-between"><span class="fw-semibold">Gender:</span><span>${p.Gender ?? ''}</span></div>
            <div class="d-flex justify-content-between"><span class="fw-semibold">Age:</span><span>${age}</span></div>
            <div class="d-flex justify-content-between"><span class="fw-semibold">Phone Number:</span><span>${p.PhoneNumber ?? ''}</span></div>
          </div>
        </div>
        <hr class="my-3"/>
        <div class="small text-muted">3D Predict ID: <span class="fw-semibold">${p.Predict3DId}</span></div>
      `;
      $infoSection.classList.remove('d-none');
    }catch(e){
      $infoWrap.innerHTML = `<div class='alert alert-danger mb-0'>${e.message||'Failed to fetch patient'}</div>`;
      $infoSection.classList.remove('d-none');
    }
  });
})();
</script>
@endpush

<!-- Patient Lookup -->
<div class="card mb-3">
  <div class="card-body">
    <h6 class="mb-3">Patient Lookup</h6>
    <div class="row g-3 align-items-end">
      <div class="col-md-4">
        <label class="form-label">3D Predict ID</label>
        <input id="labPredictId" type="text" class="form-control" placeholder="Enter Predict3DId">
      </div>
      <div class="col-md-2">
        <button id="btnLabFetch" type="button" class="btn w-100 text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
          <i class="fas fa-search me-2"></i>Fetch
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Patient Information (hidden until fetched) -->
<div id="labPatientInfoSection" class="mb-4 d-none">
  <div class="rounded-top px-3 py-2 text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
    <strong>Patient Information</strong>
  </div>
  <div class="border rounded-bottom p-3 bg-white">
    <div id="labPatientInfo"></div>
  </div>
  
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-money-bill me-2"></i>Payment Information</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('lab.payments.store') }}">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="patient_id" class="form-label">
                                <i class="fas fa-user me-1"></i>Patient *
                            </label>
                            <select class="form-select @error('patient_id') is-invalid @enderror" id="patient_id" name="patient_id" required>
                                <option value="">Select Patient</option>
                                @foreach($patients as $patient)
                                    <option value="{{ $patient->Predict3DId }}" {{ (old('patient_id') == $patient->Predict3DId || request('patient_id') == $patient->Predict3DId) ? 'selected' : '' }}>
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
                                   id="amount" name="amount" value="{{ old('amount') }}" min="0" required>
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
                                <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>Cash</option>
                                <option value="card" {{ old('payment_method') == 'card' ? 'selected' : '' }}>Credit/Debit Card</option>
                                <option value="bank_transfer" {{ old('payment_method') == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                <option value="check" {{ old('payment_method') == 'check' ? 'selected' : '' }}>Check</option>
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
                                   id="payment_date" name="payment_date" value="{{ old('payment_date', date('Y-m-d')) }}" required>
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
                                  placeholder="Enter payment description, treatment details, or notes">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Record Payment
                        </button>
                        <a href="{{ route('lab.payments.index') }}" class="btn btn-secondary">
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
                <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Payment Information</h6>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <i class="fas fa-lightbulb me-2"></i>
                    <strong>Tips:</strong>
                    <ul class="mb-0 mt-2">
                        <li>All fields marked with * are required</li>
                        <li>Select the correct patient before entering amount</li>
                        <li>Choose appropriate payment method</li>
                        <li>Add description for better record keeping</li>
                    </ul>
                </div>
                
                <div class="alert alert-success">
                    <i class="fas fa-shield-alt me-2"></i>
                    <strong>Security:</strong>
                    <p class="mb-0 mt-2">All payment information is securely stored and encrypted.</p>
                </div>
            </div>
        </div>
        
        <div class="card mt-3">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-chart-line me-2"></i>Quick Stats</h6>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span>Today's Total:</span>
                    <strong class="text-success">$0.00</strong>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span>This Month:</span>
                    <strong class="text-info">$0.00</strong>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <span>Total Payments:</span>
                    <strong class="text-primary">0</strong>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
