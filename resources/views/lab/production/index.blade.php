@extends('layouts.dashboard')

@section('title', 'Production Field - SmileCare')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div class="page-title">
        <i class="fas fa-industry me-3"></i>
        <h1>Production Field</h1>
        <small class="text-muted ms-2">Track production progress by 3D Predict ID</small>
    </div>
</div>

<!-- Search Card -->
<div class="card mb-4">
    <div class="card-body">
        <h5 class="card-title mb-4">Patient Lookup</h5>
        <div class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label">3D Predict ID</label>
                <input type="text" id="predictId" class="form-control" placeholder="Enter Predict3DId">
            </div>
            <div class="col-md-2">
                <button id="btnFetch" class="btn btn-primary w-100">
                    <i class="fas fa-search me-2"></i>Fetch
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Patient Details Card -->
<div class="card mb-4 d-none" id="patientDetailsCard">
    <div class="card-header">
        <h5 class="mb-0">Patient Information</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <table class="table table-borderless">
                    <tr>
                        <th style="width: 150px;">Patient Name:</th>
                        <td id="patientName">-</td>
                    </tr>
                    <tr>
                        <th>Doctor Name:</th>
                        <td id="doctorName">-</td>
                    </tr>
                    <tr>
                        <th>Scanning For:</th>
                        <td>
                            <span id="scanningFor">-</span>
                            <span id="scanningForOthers" class="text-muted"></span>
                        </td>
                    </tr>
                    <tr>
                        <th>Chamber Name:</th>
                        <td id="chamberName">-</td>
                    </tr>
                </table>
            </div>
            <div class="col-md-6">
                <table class="table table-borderless">
                    <tr>
                        <th style="width: 150px;">Gender:</th>
                        <td id="gender">-</td>
                    </tr>
                    <tr>
                        <th>Age:</th>
                        <td id="age">-</td>
                    </tr>
                    <tr>
                        <th>Phone Number:</th>
                        <td id="phoneNumber">-</td>
                    </tr>
                    <tr>
                        <th>Cases:</th>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    <span class="fw-bold">Upper:</span>
                                    <span id="upperCases" class="badge bg-primary ms-1">0</span>
                                </div>
                                <div class="me-3">
                                    <span class="fw-bold">Lower:</span>
                                    <span id="lowerCases" class="badge bg-primary ms-1">0</span>
                                </div>
                                <small class="text-muted">Edit cases in patient details</small>
                            </div>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Production Steps -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Production Steps</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered align-middle" id="stepsTable">
                <thead>
                    <tr>
                        <th style="width:120px">Steps</th>
                        <th>1</th>
                        <th>2</th>
                        <th>3</th>
                        <th>4</th>
                        <th>5</th>
                        <th class="text-center" style="width:60px">
                            <button id="addCol" type="button" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-plus"></i>
                            </button>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th>Upper</th>
                        <td><input class="form-control"/></td>
                        <td><input class="form-control"/></td>
                        <td><input class="form-control"/></td>
                        <td><input class="form-control"/></td>
                        <td><input class="form-control"/></td>
                        <td></td>
                    </tr>
                    <tr>
                        <th>Lower</th>
                        <td><input class="form-control"/></td>
                        <td><input class="form-control"/></td>
                        <td><input class="form-control"/></td>
                        <td><input class="form-control"/></td>
                        <td><input class="form-control"/></td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="mt-3 text-end">
            <button id="btnSaveSteps" class="btn btn-success">
                <i class="fas fa-save me-1"></i> Save Production Steps
            </button>
        </div>
    </div>
</div>

@push('styles')
<style>
    .patient-detail-label { font-weight: 600; color: #495057; }
    .patient-detail-value { padding-left: 1rem; }
    .badge { font-size: 0.85em; padding: 0.35em 0.65em; }
</style>
@endpush

@push('scripts')
<script>
(function(){
    const routeFind = "{{ route('lab.production.find-patient', ['predict3dId' => 'PREDICT_ID']) }}";
    const routeStepsGet = "{{ route('lab.production.steps.get', ['predict3dId' => 'PREDICT_ID']) }}";
    const routeStepsSave = "{{ route('lab.production.steps.save', ['predict3dId' => 'PREDICT_ID']) }}";
    const $btnFetch = document.getElementById('btnFetch');
    const $predict = document.getElementById('predictId');
    const $patientCard = document.getElementById('patientDetailsCard');
    const $add = document.getElementById('addCol');
    const $table = document.getElementById('stepsTable');
    const $btnSaveSteps = document.getElementById('btnSaveSteps');
    const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    let currentPredictId = '';

    function calculateAge(dateString) {
        if (!dateString) return 'N/A';
        const birthDate = new Date(dateString);
        const today = new Date();
        let age = today.getFullYear() - birthDate.getFullYear();
        const m = today.getMonth() - birthDate.getMonth();
        if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) { age--; }
        return age + ' years';
    }
    function formatPhoneNumber(phone) {
        if (!phone) return '-';
        return phone.replace(/(\d{3})(\d{3})(\d{4})/, '$1-$2-$3');
    }

    $btnFetch.addEventListener('click', async () => {
        const id = ($predict.value||'').trim();
        if(!id) { alert('Please enter a Predict3D ID'); return; }
        try {
            const url = routeFind.replace('PREDICT_ID', encodeURIComponent(id));
            const res = await fetch(url, { headers: { 'Accept': 'application/json','X-Requested-With':'XMLHttpRequest' } });
            if(!res.ok) { throw new Error('Patient not found'); }
            const patient = await res.json();
            document.getElementById('patientName').textContent = patient.FullName || '-';
            document.getElementById('doctorName').textContent = patient.DoctorName || '-';
            const scanningFor = patient.ScanningFor || '';
            document.getElementById('scanningFor').textContent = scanningFor;
            const scanningForOthers = patient.ScanningForOthers;
            const othersSpan = document.getElementById('scanningForOthers');
            if (scanningFor === 'Others' && scanningForOthers) {
                othersSpan.textContent = ` (${scanningForOthers})`;
                othersSpan.style.display = 'inline';
            } else {
                othersSpan.style.display = 'none';
            }
            document.getElementById('chamberName').textContent = patient.ChamberName || '-';
            document.getElementById('gender').textContent = patient.Gender || '-';
            document.getElementById('age').textContent = calculateAge(patient.DateOfBirth);
            document.getElementById('phoneNumber').textContent = formatPhoneNumber(patient.PhoneNumber);
            const upper = Number.isInteger(patient.UpperCases) ? patient.UpperCases : (parseInt(patient.UpperCases) || 0);
            const lower = Number.isInteger(patient.LowerCases) ? patient.LowerCases : (parseInt(patient.LowerCases) || 0);
            document.getElementById('upperCases').textContent = upper;
            document.getElementById('lowerCases').textContent = lower;
            $patientCard.classList.remove('d-none');
            $patientCard.scrollIntoView({ behavior: 'smooth' });
            currentPredictId = id;
            await loadProductionSteps(id);
        } catch(e) {
            alert('Error: ' + (e.message || 'Failed to fetch patient details'));
            console.error('Error fetching patient:', e);
            $patientCard.classList.add('d-none');
            currentPredictId = '';
        }
    });

    $predict.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') { e.preventDefault(); $btnFetch.click(); }
    });

    $add.addEventListener('click', () => {
        const headerRow = $table.tHead.rows[0];
        const newIndex = headerRow.cells.length - 1;
        const th = document.createElement('th');
        th.textContent = newIndex.toString();
        headerRow.insertBefore(th, headerRow.lastElementChild);
        for (const row of $table.tBodies[0].rows) {
            const td = document.createElement('td');
            const input = document.createElement('input');
            input.className = 'form-control';
            input.type = 'text';
            td.appendChild(input);
            row.insertBefore(td, row.lastElementChild);
        }
    });

    function getDataColumnCount() { const headerCells = $table.tHead.rows[0].cells; return headerCells.length - 2; }
    function ensureColumns(n) { while (getDataColumnCount() < n) { $add.click(); } }

    async function loadProductionSteps(predictId) {
        try {
            const url = routeStepsGet.replace('PREDICT_ID', encodeURIComponent(predictId));
            const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
            if (!res.ok) return;
            const data = await res.json();
            const steps = data.steps || [];
            if (steps.length === 0) return;
            const maxStep = Math.max(...steps.map(s => parseInt(s.step_number)));
            ensureColumns(maxStep);
            const bodyRows = $table.tBodies[0].rows;
            const upperRow = bodyRows[0];
            const lowerRow = bodyRows[1];
            for (let i = 1; i <= getDataColumnCount(); i++) {
                const uc = upperRow.cells[i].querySelector('input');
                const lc = lowerRow.cells[i].querySelector('input');
                if (uc) uc.value = '';
                if (lc) lc.value = '';
            }
            steps.forEach(s => {
                const idx = parseInt(s.step_number);
                const uc = upperRow.cells[idx]?.querySelector('input');
                const lc = lowerRow.cells[idx]?.querySelector('input');
                if (uc) uc.value = s.upper_value ?? '';
                if (lc) lc.value = s.lower_value ?? '';
            });
        } catch (e) { console.error('Failed to load steps', e); }
    }

    $btnSaveSteps.addEventListener('click', async () => {
        if (!currentPredictId) { alert('Please fetch a patient first.'); return; }
        const cols = getDataColumnCount();
        const bodyRows = $table.tBodies[0].rows;
        const upperRow = bodyRows[0];
        const lowerRow = bodyRows[1];
        const steps = [];
        for (let i = 1; i <= cols; i++) {
            const upperVal = parseInt(upperRow.cells[i].querySelector('input')?.value || '');
            const lowerVal = parseInt(lowerRow.cells[i].querySelector('input')?.value || '');
            const hasAny = !Number.isNaN(upperVal) || !Number.isNaN(lowerVal);
            if (hasAny) { steps.push({ step_number: i, upper: Number.isNaN(upperVal) ? null : upperVal, lower: Number.isNaN(lowerVal) ? null : lowerVal }); }
        }
        try {
            const url = routeStepsSave.replace('PREDICT_ID', encodeURIComponent(currentPredictId));
            const res = await fetch(url, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf },
                body: JSON.stringify({ steps })
            });
            if (!res.ok) {
                const contentType = res.headers.get('content-type') || '';
                if (contentType.includes('application/json')) {
                    const j = await res.json();
                    let msg = 'Failed to save';
                    if (j.message) msg = j.message;
                    if (j.errors) {
                        const lines = [];
                        for (const k in j.errors) { lines.push(`${k}: ${j.errors[k].join(', ')}`); }
                        msg += `\n\n` + lines.join('\n');
                    }
                    throw new Error(msg);
                } else {
                    const t = await res.text();
                    throw new Error(t || 'Failed to save');
                }
            }
            await res.json();
            alert('Production steps saved successfully');
        } catch (e) { console.error(e); alert(e && e.message ? e.message : 'Error saving production steps'); }
    });
})();
</script>
@endpush
@endsection
