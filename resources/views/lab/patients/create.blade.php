@extends('layouts.dashboard')

@section('title', 'Add New Patient')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-user-plus me-2"></i>Add New Patient
                    </h5>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('lab.patients.store') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="Predict3DId" class="form-label">3D Predict ID *</label>
                                <input type="text" class="form-control" id="Predict3DId" name="Predict3DId" value="{{ old('Predict3DId') }}" required>
                                <small class="text-muted">This will be the unique patient identifier</small>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Scanning For *</label>
                                <div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="ScanningFor" id="aligner" value="Aligner" {{ old('ScanningFor') == 'Aligner' ? 'checked' : '' }} onchange="toggleOthersField()">
                                        <label class="form-check-label" for="aligner">Aligner</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="ScanningFor" id="zirconia" value="Zirconia" {{ old('ScanningFor') == 'Zirconia' ? 'checked' : '' }} onchange="toggleOthersField()">
                                        <label class="form-check-label" for="zirconia">Zirconia</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="ScanningFor" id="others" value="Others" {{ old('ScanningFor') == 'Others' ? 'checked' : '' }} onchange="toggleOthersField()">
                                        <label class="form-check-label" for="others">Others</label>
                                    </div>
                                </div>
                                <div id="othersField" class="mt-2" style="display: none;">
                                    <input type="text" class="form-control" name="ScanningForOthers" placeholder="Please specify..." value="{{ old('ScanningForOthers') }}">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="DoctorName" class="form-label">Doctor Name *</label>
                                <input type="text" class="form-control" id="DoctorName" name="DoctorName" value="{{ old('DoctorName') }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="ChamberName" class="form-label">Chamber Name *</label>
                                <input type="text" class="form-control" id="ChamberName" name="ChamberName" value="{{ old('ChamberName') }}" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="TerritoryName" class="form-label">Territory Name *</label>
                                <input type="text" class="form-control" id="TerritoryName" name="TerritoryName" value="{{ old('TerritoryName') }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="RegionalName" class="form-label">Regional Name *</label>
                                <input type="text" class="form-control" id="RegionalName" name="RegionalName" value="{{ old('RegionalName') }}" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="PhoneNumber" class="form-label">Phone Number *</label>
                                <input type="text" class="form-control" id="PhoneNumber" name="PhoneNumber" value="{{ old('PhoneNumber') }}" maxlength="13" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="EmergencyContact" class="form-label">Emergency Contact *</label>
                                <input type="text" class="form-control" id="EmergencyContact" name="EmergencyContact" value="{{ old('EmergencyContact') }}" maxlength="13" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Gender *</label>
                                <div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="Gender" id="male" value="Male" {{ old('Gender') == 'Male' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="male">Male</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="Gender" id="female" value="Female" {{ old('Gender') == 'Female' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="female">Female</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="Gender" id="custom" value="Custom" {{ old('Gender') == 'Custom' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="custom">Custom</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="DateOfBirth" class="form-label">Date of Birth *</label>
                                <input type="date" class="form-control" id="DateOfBirth" name="DateOfBirth" value="{{ old('DateOfBirth') }}" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="Address" class="form-label">Address *</label>
                            <textarea class="form-control" id="Address" name="Address" rows="3" required>{{ old('Address') }}</textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="UpperCases" class="form-label">Upper Cases</label>
                                <input type="number" class="form-control" id="UpperCases" name="UpperCases" value="{{ old('UpperCases') }}" min="0">
                                <small class="text-muted">Number of upper cases (optional)</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="LowerCases" class="form-label">Lower Cases</label>
                                <input type="number" class="form-control" id="LowerCases" name="LowerCases" value="{{ old('LowerCases') }}" min="0">
                                <small class="text-muted">Number of lower cases (optional)</small>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('lab.patients.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-1"></i>Back to Patients
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i>Save Patient
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-header bg-info text-white">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-info-circle me-2"></i>Patient Information Tips
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h6><i class="fas fa-user text-primary me-1"></i>Patient Details</h6>
                        <small class="text-muted">Patient ID will be auto-generated starting from 1000. Ensure all information is accurate.</small>
                    </div>
                    
                    <div class="mb-3">
                        <h6><i class="fas fa-tooth text-success me-1"></i>Scanning Information</h6>
                        <small class="text-muted">Select the appropriate scanning type. If "Others" is selected, please specify the details.</small>
                    </div>
                    
                    <div class="mb-3">
                        <h6><i class="fas fa-map-marker-alt text-warning me-1"></i>Location Details</h6>
                        <small class="text-muted">Territory and Regional information helps in proper categorization and management.</small>
                    </div>
                    
                    <div class="alert alert-warning">
                        <small><strong>Lab Technician Note:</strong> You can only add new patients. Edit/Delete permissions are restricted to administrators only.</small>
                    </div>
                    
                    <div class="alert alert-light border-left-primary">
                        <small><strong>Note:</strong> Fields marked with * are required. All patient information is kept confidential and secure.</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function toggleOthersField() {
    const othersRadio = document.getElementById('others');
    const othersField = document.getElementById('othersField');
    
    if (othersRadio.checked) {
        othersField.style.display = 'block';
        othersField.querySelector('input').required = true;
    } else {
        othersField.style.display = 'none';
        othersField.querySelector('input').required = false;
        othersField.querySelector('input').value = '';
    }
}

// Check on page load if Others was previously selected
document.addEventListener('DOMContentLoaded', function() {
    toggleOthersField();
});
</script>
@endsection
