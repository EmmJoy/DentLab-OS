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
  // Reuse routes from admin payments plan endpoints
  const routeFind = "{{ route('admin.payments.plan.find-patient', ['predict3dId' => 'PREDICT_ID']) }}";
  const routeGet = "{{ route('admin.payments.plan.get', ['predict3dId' => 'PREDICT_ID']) }}";
  const routeSave = "{{ route('admin.payments.plan.save', ['predict3dId' => 'PREDICT_ID']) }}";
  const routeSaveInstallment = "{{ route('admin.payments.plan.add-installment', ['predict3dId' => 'PREDICT_ID']) }}";
  const routeUpdateTotal = "{{ route('admin.payments.plan.update-total', ['predict3dId' => 'PREDICT_ID']) }}";

  const $predict = document.getElementById('predictId');
  const $btnFetch = document.getElementById('btnFetch');
  const $summary = document.getElementById('patientSummary');
  const $infoSection = document.getElementById('patientInfoSection');
  const $infoBody = document.getElementById('patientInfoBody');
  const $planBlock = document.getElementById('planBlock');
  const $install = document.getElementById('installmentSection');
  const $full = document.getElementById('fullSection');
  const $remaining = document.getElementById('remaining');
  const $history = document.querySelector('#historyTable tbody');
  const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

  const $pmRadios = document.querySelectorAll('input[name="paymentMethod"]');
  const $ptFull = document.getElementById('ptFull');
  const $ptInstall = document.getElementById('ptInstallment');
  const $totalAmount = document.getElementById('totalAmount');
  const $btnEditTotal = document.getElementById('btnEditTotal');
  const $btnSaveTotal = document.getElementById('btnSaveTotal');

  const $currentAmount = document.getElementById('currentAmount');
  const $currentDate = document.getElementById('currentDate');
  const $nextDate = document.getElementById('nextDate');
  const $btnSavePlan = document.getElementById('btnSavePlan');
  const $fullDate = document.getElementById('fullDate');
  const $fullAmount = document.getElementById('fullAmount');
  const $btnSaveFull = document.getElementById('btnSaveFull');

  // Summary badges
  const $sumTotal = document.getElementById('sumTotal');
  const $sumPaid = document.getElementById('sumPaid');
  const $sumRemaining = document.getElementById('sumRemaining');

  let currentPredict = '';

  function selectedMethod(){
    for(const r of $pmRadios){ if(r.checked) return r.value; }
    return 'cash';
  }
  function togglePlanType(){
    if($ptInstall && $ptInstall.checked){ $install.classList.remove('d-none'); $full?.classList.add('d-none'); }
    else { $install.classList.add('d-none'); $full?.classList.remove('d-none'); }
  }
  document.querySelectorAll('input[name="planType"]').forEach(r=> r.addEventListener('change', togglePlanType));

  function computeAge(isoDate){
    if(!isoDate) return '';
    const dob = new Date(isoDate);
    if(Number.isNaN(dob.getTime())) return '';
    const diff = Date.now() - dob.getTime();
    const years = Math.floor(diff / (365.25 * 24 * 60 * 60 * 1000));
    return years > 0 ? `${years} years` : '';
  }

  $btnFetch?.addEventListener('click', async (evt) => {
    evt.preventDefault?.();
    evt.stopPropagation?.();
    const id = ($predict.value||'').trim();
    if(!id){ alert('Enter Predict3DId'); return; }
    $summary?.classList.add('d-none');
    $history.innerHTML='';
    try{
      const urlP = routeFind.replace('PREDICT_ID', encodeURIComponent(id));
      const resP = await fetch(urlP, { headers: { 'Accept': 'application/json' } });
      if(!resP.ok) throw new Error('Patient not found');
      const p = await resP.json();
      const age = computeAge(p.DateOfBirth);
      const scanningFor = (p.ScanningFor === 'Others' && p.ScanningForOthers) ? `${p.ScanningFor} (${p.ScanningForOthers})` : (p.ScanningFor || '');
      $infoBody.innerHTML = `
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
        <hr class="my-3" />
        <div class="small text-muted">3D Predict ID: <span class="fw-semibold">${p.Predict3DId}</span></div>`;
      $infoSection.classList.remove('d-none');
      currentPredict = id;
      // Show empty plan immediately; then hydrate with existing details if any
      $planBlock.classList.remove('d-none');
      $totalAmount.disabled = false; $totalAmount.value = '';
      $ptFull && ($ptFull.checked = true); togglePlanType();
      $remaining.textContent = '0.00';
      $sumTotal.textContent = '0.00';
      $sumPaid.textContent = '0.00';
      $sumRemaining.textContent = '0.00';

      const urlG = routeGet.replace('PREDICT_ID', encodeURIComponent(id));
      const resG = await fetch(urlG, { headers: { 'Accept': 'application/json' } });
      if(resG.ok){
        const data = await resG.json();
        const plan = data.plan || null;
        const pays = data.payments || [];
        const paid = parseFloat(data.paid || 0);
        const remaining = plan ? (parseFloat(plan.total_amount) - paid) : 0;
        $remaining.textContent = (remaining||0).toFixed(2);
        $history.innerHTML = pays.map(x=>`<tr><td>${x.payment_date}</td><td>${x.payment_method}</td><td>$${parseFloat(x.amount).toFixed(2)}</td></tr>`).join('');
        // badges
        $sumTotal.textContent = plan ? parseFloat(plan.total_amount).toFixed(2) : '0.00';
        $sumPaid.textContent = (paid||0).toFixed(2);
        $sumRemaining.textContent = (remaining||0).toFixed(2);
        $planBlock.classList.remove('d-none');
        if(plan){
          $totalAmount.value = parseFloat(plan.total_amount).toFixed(2);
          $totalAmount.disabled = true;
          ($pmRadios.forEach(r=> r.checked = (r.value===plan.payment_method)));
          // default to Full if not installment
          if(plan.is_installment){ $ptInstall.checked = true; } else { $ptFull && ($ptFull.checked = true); }
          togglePlanType();
        } else {
          $totalAmount.disabled = false; $totalAmount.value = '';
          $ptFull && ($ptFull.checked = true); togglePlanType();
        }
      } else {
        // If no plan found (or non-OK), still show empty form so user can create one
        $planBlock.classList.remove('d-none');
        $totalAmount.disabled = false; $totalAmount.value = '';
        $ptFull && ($ptFull.checked = true); togglePlanType();
        $history.innerHTML = '';
        $remaining.textContent = '0.00';
        $sumTotal.textContent = '0.00';
        $sumPaid.textContent = '0.00';
        $sumRemaining.textContent = '0.00';
      }
    }catch(e){
      // Even if patient fetch fails, keep a visible error but allow user to try again
      $infoBody.innerHTML = `<div class='alert alert-danger mb-0'>${e.message||'Failed to fetch'}</div>`;
      $infoSection.classList.remove('d-none');
      // Show empty plan to allow manual entry once a valid patient is fetched next
      $planBlock.classList.remove('d-none');
      $totalAmount.disabled = false; $totalAmount.value = '';
      $ptFull && ($ptFull.checked = true); togglePlanType();
      $history.innerHTML = '';
      $remaining.textContent = '0.00';
      $sumTotal.textContent = '0.00';
      $sumPaid.textContent = '0.00';
      $sumRemaining.textContent = '0.00';
      currentPredict='';
    }
  });

  $btnEditTotal?.addEventListener('click', ()=>{
    $totalAmount.disabled = false; $btnSaveTotal.classList.remove('d-none');
  });

  $btnSaveTotal?.addEventListener('click', async ()=>{
    if(!currentPredict){ alert('Fetch a patient first'); return; }
    const amt = parseFloat($totalAmount.value);
    if(Number.isNaN(amt) || amt<0){ alert('Enter a valid total'); return; }
    try{
      const url = routeUpdateTotal.replace('PREDICT_ID', encodeURIComponent(currentPredict));
      const res = await fetch(url, {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf },
        body: JSON.stringify({ total_amount: amt })
      });
      const contentType = res.headers.get('content-type')||'';
      if(!res.ok){
        let msg = 'Failed to update total';
        if(contentType.includes('application/json')){ const j = await res.json(); msg = j.message||msg; }
        else { msg = await res.text() || msg; }
        throw new Error(msg);
      }
      const data = await res.json();
      $totalAmount.value = parseFloat(data.plan.total_amount).toFixed(2);
      $totalAmount.disabled = true; $btnSaveTotal.classList.add('d-none');
      // update badges from current history
      const paidNow = Array.from($history.querySelectorAll('tr td:nth-child(3)')).reduce((s,td)=>{
        const v = parseFloat((td.textContent||'').replace(/[^\d.\-]/g,''));
        return s + (isNaN(v)?0:v);
      },0);
      $sumTotal.textContent = parseFloat(data.plan.total_amount||0).toFixed(2);
      $sumPaid.textContent = paidNow.toFixed(2);
      $sumRemaining.textContent = Math.max(0, parseFloat(data.plan.total_amount||0) - paidNow).toFixed(2);
      alert('Total updated');
    }catch(e){ alert(e.message||'Error updating total'); }
  });

  $btnSavePlan?.addEventListener('click', async ()=>{
    if(!currentPredict){ alert('Fetch a patient first'); return; }
    const amt = parseFloat($totalAmount.value);
    if(Number.isNaN(amt) || amt<=0){ alert('Enter a valid total'); return; }
    const method = selectedMethod();
    const isInstall = $ptInstall.checked;
    const payload = {
      total_amount: amt,
      payment_method: method,
      is_installment: isInstall,
      current_payment_amount: isInstall ? parseFloat($currentAmount.value||0) : 0,
      current_payment_date: isInstall ? ($currentDate.value||null) : null,
      next_payment_date: isInstall ? ($nextDate.value||null) : null,
    };
    try{
      const url = routeSave.replace('PREDICT_ID', encodeURIComponent(currentPredict));
      const res = await fetch(url, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf },
        body: JSON.stringify(payload)
      });
      const contentType = res.headers.get('content-type')||'';
      if(!res.ok){
        let msg = 'Failed to save';
        if(contentType.includes('application/json')){ const j = await res.json(); msg = j.message || msg; if(j.errors){ msg += '\n'+Object.values(j.errors).flat().join('\n'); } }
        else { msg = await res.text() || msg; }
        throw new Error(msg);
      }
      const data = await res.json();
      const plan = data.plan; const pays = data.payments||[];
      $remaining.textContent = parseFloat(plan.remaining_amount||0).toFixed(2);
      $history.innerHTML = pays.map(x=>`<tr><td>${x.payment_date}</td><td>${x.payment_method}</td><td>$${parseFloat(x.amount).toFixed(2)}</td></tr>`).join('');
      $totalAmount.disabled = true;
      // badges
      $sumTotal.textContent = parseFloat(plan.total_amount||0).toFixed(2);
      const paidNow2 = (pays||[]).reduce((s,x)=> s + parseFloat(x.amount||0), 0);
      $sumPaid.textContent = paidNow2.toFixed(2);
      $sumRemaining.textContent = Math.max(0, parseFloat(plan.total_amount||0) - paidNow2).toFixed(2);
      alert('Saved successfully');
    }catch(e){ alert(e.message||'Error saving'); }
  });

  // Full payment save
  $btnSaveFull?.addEventListener('click', async ()=>{
    if(!currentPredict){ alert('Fetch a patient first'); return; }
    const amt = parseFloat(($fullAmount?.value)||0);
    if(Number.isNaN(amt) || amt<=0){ alert('Enter a valid amount'); return; }
    const method = selectedMethod();
    try{
      const url = routeSaveInstallment.replace('PREDICT_ID', encodeURIComponent(currentPredict));
      const res = await fetch(url, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf },
        body: JSON.stringify({ amount: amt, payment_date: ($fullDate?.value)||null, payment_method: method, next_payment_date: null })
      });
      const contentType = res.headers.get('content-type')||'';
      if(!res.ok){
        let msg = 'Failed to save';
        if(contentType.includes('application/json')){ const j = await res.json(); msg = j.message || msg; if(j.errors){ msg += '\n'+Object.values(j.errors).flat().join('\n'); } }
        else { msg = await res.text() || msg; }
        throw new Error(msg);
      }
      const data = await res.json();
      const plan = data.plan; const pays = data.payments||[];
      $remaining.textContent = parseFloat(plan.remaining_amount||0).toFixed(2);
      $history.innerHTML = pays.map(x=>`<tr><td>${x.payment_date}</td><td>${x.payment_method}</td><td>$${parseFloat(x.amount).toFixed(2)}</td></tr>`).join('');
      // badges
      $sumTotal.textContent = parseFloat(plan.total_amount||0).toFixed(2);
      const paidNow3 = (pays||[]).reduce((s,x)=> s + parseFloat(x.amount||0), 0);
      $sumPaid.textContent = paidNow3.toFixed(2);
      $sumRemaining.textContent = Math.max(0, parseFloat(plan.total_amount||0) - paidNow3).toFixed(2);
      alert('Saved successfully');
    }catch(e){ alert(e.message||'Error saving'); }
  });

  // Full payment removed

  // initialize
  togglePlanType();
})();
</script>
@endpush

<div class="row">
    <div class="col-md-12">
        <!-- Predict3DId-based Payment Workflow -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-file-invoice-dollar me-2"></i>Pay by 3D Predict ID</h5>
            </div>
            <div class="card-body">
                <div class="row g-3 align-items-end mb-3">
                    <div class="col-md-3">
                        <label class="form-label">3D Predict ID</label>
                        <input id="predictId" type="text" class="form-control" placeholder="Enter Predict3DId">
                    </div>
                    <div class="col-md-2">
                        <button id="btnFetch" type="button" class="btn w-100 text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);"><i class="fas fa-search me-2"></i>Fetch</button>
                    </div>
                    <div class="col-md-7"></div>
                </div>

                <!-- Patient Information (hidden until fetched) -->
                <div id="patientInfoSection" class="mt-3 d-none">
                    <div class="rounded-top px-3 py-2 text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                        <strong>Patient Information</strong>
                    </div>
                    <div class="border rounded-bottom p-3 bg-white">
                        <div id="patientInfoBody"></div>
                    </div>
                </div>

                <div id="planBlock" class="d-none">
                    <!-- Summary Row -->
                    <div class="row g-3 mb-2">
                        <div class="col-md-12 d-flex gap-3 flex-wrap">
                            <div class="badge bg-light text-dark border p-2">
                                <span class="text-muted">Total:</span>
                                $<span id="sumTotal">0.00</span>
                            </div>
                            <div class="badge bg-light text-dark border p-2">
                                <span class="text-muted">Paid:</span>
                                $<span id="sumPaid">0.00</span>
                            </div>
                            <div class="badge bg-info p-2">
                                <span>Remaining:</span>
                                $<span id="sumRemaining">0.00</span>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Payment Method</label>
                            <div class="d-flex gap-3 mt-1">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="paymentMethod" id="pmCash" value="cash" checked>
                                    <label class="form-check-label" for="pmCash">Cash</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="paymentMethod" id="pmCard" value="card">
                                    <label class="form-check-label" for="pmCard">Card</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="paymentMethod" id="pmBank" value="bank_transfer">
                                    <label class="form-check-label" for="pmBank">Bank Transfer</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Total Amount</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input id="totalAmount" type="number" min="0" step="0.01" class="form-control" placeholder="0.00">
                                @if(auth()->check() && auth()->user()->role === 'admin')
                                  <button id="btnEditTotal" class="btn btn-outline-secondary" type="button"><i class="fas fa-pen"></i></button>
                                  <button id="btnSaveTotal" class="btn btn-outline-success d-none" type="button"><i class="fas fa-save"></i></button>
                                @endif
                            </div>
                            <small class="text-muted">Total locks after save; only admin can update.</small>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Plan Type</label>
                            <div class="d-flex gap-3 mt-1">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="planType" id="ptFull" value="full" checked>
                                    <label class="form-check-label" for="ptFull">Full Payment</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="planType" id="ptInstallment" value="installment">
                                    <label class="form-check-label" for="ptInstallment">Installment</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div id="fullSection" class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Payment Date</label>
                            <input id="fullDate" type="date" class="form-control" value="{{ date('Y-m-d') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Amount</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input id="fullAmount" type="number" min="0" step="0.01" class="form-control" placeholder="0.00">
                            </div>
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button id="btnSaveFull" type="button" class="btn btn-success w-100"><i class="fas fa-save me-2"></i>Save</button>
                        </div>
                        <div class="col-md-3">
                            <div class="alert alert-info mb-0"><strong>Remaining:</strong> $<span id="remaining">0.00</span></div>
                        </div>
                    </div>

                    <div id="installmentSection" class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Current Payment Date</label>
                            <input id="currentDate" type="date" class="form-control" value="{{ date('Y-m-d') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Next Payment Date</label>
                            <input id="nextDate" type="date" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Current Payment Amount</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input id="currentAmount" type="number" min="0" step="0.01" class="form-control" placeholder="0.00">
                            </div>
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button id="btnSavePlan" type="button" class="btn btn-success w-100"><i class="fas fa-save me-2"></i>Save</button>
                        </div>
                    </div>

                    <hr>

                    <h6>Payment History</h6>
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle" id="historyTable">
                            <thead>
                                <tr>
                                    <th style="width: 150px">Date</th>
                                    <th style="width: 150px">Method</th>
                                    <th>Amount</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-money-bill me-2"></i>Payment Information</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.payments.store') }}">
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
                        <a href="{{ route('admin.payments.index') }}" class="btn btn-secondary">
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
