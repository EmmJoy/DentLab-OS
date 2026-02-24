@extends('layouts.dashboard')

@section('title', 'Payments by 3D Predict ID - SmileCare')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
  <div class="page-title">
    <i class="fas fa-file-invoice-dollar me-3"></i>
    <h1>Payments by 3D Predict ID</h1>
    <small class="text-muted ms-2">Search by Predict3DId, set total, and record payments</small>
  </div>
</div>

<div class="card mb-3">
  <div class="card-body">
    <div class="row g-3 align-items-end">
      <div class="col-md-3">
        <label class="form-label">3D Predict ID</label>
        <input id="predictId" type="text" class="form-control" placeholder="Enter Predict3DId">
      </div>
      <div class="col-md-2">
        <button id="btnFetch" type="button" class="btn w-100 text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);"><i class="fas fa-search me-2"></i>Fetch</button>
      </div>
      <div class="col-md-7"></div>
    </div>
  </div>
</div>

<!-- Patient Information (hidden until fetched) -->
<div id="patientInfoSection" class="mb-3 d-none">
  <div class="rounded-top px-3 py-2 text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
    <strong>Patient Information</strong>
  </div>
  <div class="border rounded-bottom p-3 bg-white">
    <div id="patientInfoBody"></div>
  </div>
</div>

<div id="planCard" class="card d-none">
  <div class="card-header">
    <h5 class="mb-0">Payment Plan</h5>
  </div>
  <div class="card-body">
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
        <small class="text-muted">Total is locked after save. Admins can click edit to update later.</small>
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

    <div id="installmentSection" class="row g-3 d-none">
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
        <button id="btnSavePlan" class="btn btn-success w-100"><i class="fas fa-save me-2"></i>Save</button>
      </div>
    </div>

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
        <button id="btnSaveFull" class="btn btn-success w-100"><i class="fas fa-save me-2"></i>Save</button>
      </div>
      <div class="col-md-3">
        <div class="alert alert-info mb-0"><strong>Remaining:</strong> $<span id="remaining">0.00</span></div>
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

@push('scripts')
<script>
(function(){
  const routeFind = "{{ route('admin.payments.plan.find-patient', ['predict3dId' => 'PREDICT_ID']) }}";
  const routeGet = "{{ route('admin.payments.plan.get', ['predict3dId' => 'PREDICT_ID']) }}";
  const routeSave = "{{ route('admin.payments.plan.save', ['predict3dId' => 'PREDICT_ID']) }}";
  const routeSaveInstallment = "{{ route('admin.payments.plan.add-installment', ['predict3dId' => 'PREDICT_ID']) }}";
  const routeUpdateTotal = "{{ route('admin.payments.plan.update-total', ['predict3dId' => 'PREDICT_ID']) }}";

  const $predict = document.getElementById('predictId');
  const $btnFetch = document.getElementById('btnFetch');
  const $summary = document.getElementById('patientSummary'); // legacy inline summary (kept but unused)
  const $infoSection = document.getElementById('patientInfoSection');
  const $infoBody = document.getElementById('patientInfoBody');
  const $planCard = document.getElementById('planCard');
  const $install = document.getElementById('installmentSection');
  const $full = document.getElementById('fullSection');
  const $remaining = document.getElementById('remaining');
  const $history = document.querySelector('#historyTable tbody');
  const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

  // Summary badges
  const $sumTotal = document.getElementById('sumTotal');
  const $sumPaid = document.getElementById('sumPaid');
  const $sumRemaining = document.getElementById('sumRemaining');

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

  let currentPredict = '';
  let totalLocked = false;

  function selectedMethod(){
    for(const r of $pmRadios){ if(r.checked) return r.value; }
    return 'cash';
  }

  function togglePlanType(){
    if($ptInstall.checked){ $install.classList.remove('d-none'); $full.classList.add('d-none'); }
    else { $install.classList.add('d-none'); $full.classList.remove('d-none'); }
  }
  document.querySelectorAll('input[name="planType"]').forEach(r => r.addEventListener('change', togglePlanType));

  function computeAge(iso){
    if(!iso) return '';
    const d = new Date(iso); if(Number.isNaN(d.getTime())) return '';
    const years = Math.floor((Date.now()-d.getTime())/(365.25*24*60*60*1000));
    return years>0 ? years+" years" : '';
  }

  function fmtDate(val){
    if(!val) return '';
    const d = new Date(val);
    if(Number.isNaN(d.getTime())) return String(val);
    const y = d.getFullYear();
    const m = String(d.getMonth()+1).padStart(2,'0');
    const day = String(d.getDate()).padStart(2,'0');
    return `${y}-${m}-${day}`;
  }

  function fmtMethod(val){
    if(!val) return '';
    return String(val).replace(/_/g,' ').replace(/^./, c => c.toUpperCase());
  }

  $btnFetch.addEventListener('click', async () => {
    const id = ($predict.value||'').trim();
    if(!id){ alert('Enter Predict3DId'); return; }
    $summary && $summary.classList.add('d-none');
    $infoSection.classList.add('d-none');
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
            <div class="d-flex align-items-center"><span class="fw-semibold me-2">Patient Name:</span><span>${p.FullName ?? ''}</span></div>
            <div class="d-flex align-items-center"><span class="fw-semibold me-2">Doctor Name:</span><span>${p.DoctorName ?? ''}</span></div>
            <div class="d-flex align-items-center"><span class="fw-semibold me-2">Scanning For:</span><span>${scanningFor}</span></div>
          </div>
          <div class="col-md-6">
            <div class="d-flex align-items-center"><span class="fw-semibold me-2">Gender:</span><span>${p.Gender ?? ''}</span></div>
            <div class="d-flex align-items-center"><span class="fw-semibold me-2">Age:</span><span>${age}</span></div>
            <div class="d-flex align-items-center"><span class="fw-semibold me-2">Phone Number:</span><span>${p.PhoneNumber ?? ''}</span></div>
          </div>
        </div>
        <hr class="my-3"/>
        <div class="small text-muted">3D Predict ID: <span class="fw-semibold">${p.Predict3DId}</span></div>`;
      $infoSection.classList.remove('d-none');
      currentPredict = id;

      // Show plan UI immediately with defaults; fill actual data after we fetch plan
      $planCard.classList.remove('d-none');
      $totalAmount.disabled = false; $totalAmount.value = '';
      $ptFull.checked = true; togglePlanType();
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
        $history.innerHTML = pays.map(x=>`<tr><td>${fmtDate(x.payment_date)}</td><td>${fmtMethod(x.payment_method)}</td><td>$${parseFloat(x.amount).toFixed(2)}</td></tr>`).join('');
        // Update badges
        $sumTotal.textContent = plan ? parseFloat(plan.total_amount).toFixed(2) : '0.00';
        $sumPaid.textContent = (paid||0).toFixed(2);
        $sumRemaining.textContent = (remaining||0).toFixed(2);
        // Ensure patient info stays visible
        $infoSection.classList.remove('d-none');
        if(plan){
          $planCard.classList.remove('d-none');
          $totalAmount.value = parseFloat(plan.total_amount).toFixed(2);
          totalLocked = true; $totalAmount.disabled = true;
          ($pmRadios.forEach(r=> r.checked = (r.value===plan.payment_method)));
          if(plan.is_installment){ $ptInstall.checked = true; } else { $ptFull.checked = true; }
          togglePlanType();
        } else {
          $planCard.classList.remove('d-none');
          totalLocked = false; $totalAmount.disabled = false; $totalAmount.value = '';
          $ptFull.checked = true; togglePlanType();
        }
      } else {
        // Keep patient info visible and allow creating a new plan
        $infoSection.classList.remove('d-none');
        $planCard.classList.remove('d-none');
        $totalAmount.disabled = false; $totalAmount.value = '';
        $ptFull.checked = true; togglePlanType();
        $history.innerHTML = '';
        $remaining.textContent = '0.00';
        $sumTotal.textContent = '0.00';
        $sumPaid.textContent = '0.00';
        $sumRemaining.textContent = '0.00';
      }
    }catch(e){
      $summary.innerHTML = `<span class='text-danger'>${e.message||'Failed to fetch'}</span>`;
      $summary.classList.remove('d-none');
      // Keep patient info visible and show empty plan so user can proceed after re-fetch
      $infoSection.classList.remove('d-none');
      $planCard.classList.remove('d-none');
      $totalAmount.disabled = false; $totalAmount.value = '';
      $ptFull.checked = true; togglePlanType();
      $history.innerHTML = '';
      $remaining.textContent = '0.00';
      $sumTotal.textContent = '0.00';
      $sumPaid.textContent = '0.00';
      $sumRemaining.textContent = '0.00';
      currentPredict='';
    }
  });

  $btnEditTotal.addEventListener('click', ()=>{
    $totalAmount.disabled = false; $btnSaveTotal.classList.remove('d-none');
  });

  $btnSaveTotal.addEventListener('click', async ()=>{
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
      // Refresh badges; remaining = total - already paid
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

  document.querySelectorAll('input[name="planType"]').forEach(r=>r.addEventListener('change', ()=>{
    togglePlanType();
  }));

  $btnSavePlan.addEventListener('click', async ()=>{
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
      $history.innerHTML = pays.map(x=>`<tr><td>${fmtDate(x.payment_date)}</td><td>${fmtMethod(x.payment_method)}</td><td>$${parseFloat(x.amount).toFixed(2)}</td></tr>`).join('');
      $totalAmount.disabled = true; totalLocked = true;
      // Update badges
      $sumTotal.textContent = parseFloat(plan.total_amount||0).toFixed(2);
      const paidNow2 = (pays||[]).reduce((s,x)=> s + parseFloat(x.amount||0), 0);
      $sumPaid.textContent = paidNow2.toFixed(2);
      $sumRemaining.textContent = Math.max(0, parseFloat(plan.total_amount||0) - paidNow2).toFixed(2);
      alert('Saved successfully');
    }catch(e){ alert(e.message||'Error saving'); }
  });

  $btnSaveFull.addEventListener('click', async ()=>{
    if(!currentPredict){ alert('Fetch a patient first'); return; }
    const amt = parseFloat($fullAmount.value);
    if(Number.isNaN(amt) || amt<=0){ alert('Enter a valid amount'); return; }
    const method = selectedMethod();
    try{
      const url = routeSaveInstallment.replace('PREDICT_ID', encodeURIComponent(currentPredict));
      const res = await fetch(url, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf },
        body: JSON.stringify({ amount: amt, payment_date: $fullDate.value, payment_method: method, next_payment_date: null })
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
      // Update badges
      $sumTotal.textContent = parseFloat(plan.total_amount||0).toFixed(2);
      const paidNow3 = (pays||[]).reduce((s,x)=> s + parseFloat(x.amount||0), 0);
      $sumPaid.textContent = paidNow3.toFixed(2);
      $sumRemaining.textContent = Math.max(0, parseFloat(plan.total_amount||0) - paidNow3).toFixed(2);
      alert('Saved successfully');
    }catch(e){ alert(e.message||'Error saving'); }
  });
})();
</script>
@endpush
@endsection
