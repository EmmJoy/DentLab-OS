@extends('layouts.dashboard')

@section('title', 'Edit Patient - SmileCare')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div class="page-title">
        <i class="fas fa-user-edit me-3"></i>
        <h1>Edit Patient</h1>
    </div>
    <a href="{{ route('lab.patients.show', $patient) }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-2"></i>Back to Patient Details
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-user-edit me-2"></i>Update Patient Information</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('lab.patients.update', $patient) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="Predict3DId" class="form-label">3D Predict ID <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('Predict3DId') is-invalid @enderror" 
                                       id="Predict3DId" name="Predict3DId" value="{{ old('Predict3DId', $patient->Predict3DId) }}" required>
                                @error('Predict3DId')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">This is the unique patient identifier</small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="PhoneNumber" class="form-label">Phone Number <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('PhoneNumber') is-invalid @enderror" 
                                       id="PhoneNumber" name="PhoneNumber" value="{{ old('PhoneNumber', $patient->PhoneNumber) }}" required>
                                @error('PhoneNumber')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="EmergencyContact" class="form-label">Emergency Contact <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('EmergencyContact') is-invalid @enderror" 
                                       id="EmergencyContact" name="EmergencyContact" value="{{ old('EmergencyContact', $patient->EmergencyContact) }}" required>
                                @error('EmergencyContact')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="Gender" class="form-label">Gender <span class="text-danger">*</span></label>
                                <select class="form-select @error('Gender') is-invalid @enderror" id="Gender" name="Gender" required>
                                    <option value="">Select Gender</option>
                                    <option value="Male" {{ old('Gender', $patient->Gender) == 'Male' ? 'selected' : '' }}>Male</option>
                                    <option value="Female" {{ old('Gender', $patient->Gender) == 'Female' ? 'selected' : '' }}>Female</option>
                                    <option value="Custom" {{ old('Gender', $patient->Gender) == 'Custom' ? 'selected' : '' }}>Custom</option>
                                </select>
                                @error('Gender')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="DateOfBirth" class="form-label">Date of Birth <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('DateOfBirth') is-invalid @enderror" 
                                       id="DateOfBirth" name="DateOfBirth" value="{{ old('DateOfBirth', $patient->DateOfBirth ? $patient->DateOfBirth->format('Y-m-d') : '') }}" required>
                                @error('DateOfBirth')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="ScanningFor" class="form-label">Scanning For <span class="text-danger">*</span></label>
                                <select class="form-select @error('ScanningFor') is-invalid @enderror" id="ScanningFor" name="ScanningFor" required>
                                    <option value="">Select Scanning Type</option>
                                    <option value="Aligner" {{ old('ScanningFor', $patient->ScanningFor) == 'Aligner' ? 'selected' : '' }}>Aligner</option>
                                    <option value="Zirconia" {{ old('ScanningFor', $patient->ScanningFor) == 'Zirconia' ? 'selected' : '' }}>Zirconia</option>
                                    <option value="Others" {{ old('ScanningFor', $patient->ScanningFor) == 'Others' ? 'selected' : '' }}>Others</option>
                                </select>
                                @error('ScanningFor')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-3" id="scanningForOthersDiv" style="display: {{ old('ScanningFor', $patient->ScanningFor) == 'Others' ? 'block' : 'none' }};">
                        <label for="ScanningForOthers" class="form-label">Scanning For Others Details</label>
                        <input type="text" class="form-control @error('ScanningForOthers') is-invalid @enderror" 
                               id="ScanningForOthers" name="ScanningForOthers" value="{{ old('ScanningForOthers', $patient->ScanningForOthers) }}" 
                               placeholder="Please specify...">
                        @error('ScanningForOthers')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="DoctorName" class="form-label">Doctor Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('DoctorName') is-invalid @enderror" 
                                       id="DoctorName" name="DoctorName" value="{{ old('DoctorName', $patient->DoctorName) }}" required>
                                @error('DoctorName')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="ChamberName" class="form-label">Chamber Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('ChamberName') is-invalid @enderror" 
                                       id="ChamberName" name="ChamberName" value="{{ old('ChamberName', $patient->ChamberName) }}" required>
                                @error('ChamberName')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="TerritoryName" class="form-label">Territory Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('TerritoryName') is-invalid @enderror" 
                                       id="TerritoryName" name="TerritoryName" value="{{ old('TerritoryName', $patient->TerritoryName) }}" required>
                                @error('TerritoryName')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="RegionalName" class="form-label">Regional Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('RegionalName') is-invalid @enderror" 
                                       id="RegionalName" name="RegionalName" value="{{ old('RegionalName', $patient->RegionalName) }}" required>
                                @error('RegionalName')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="Address" class="form-label">Address <span class="text-danger">*</span></label>
                        <textarea class="form-control @error('Address') is-invalid @enderror" 
                                  id="Address" name="Address" rows="3" required>{{ old('Address', $patient->Address) }}</textarea>
                        @error('Address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="UpperCases" class="form-label">Upper Cases</label>
                                <input type="number" class="form-control @error('UpperCases') is-invalid @enderror" 
                                       id="UpperCases" name="UpperCases" value="{{ old('UpperCases', $patient->UpperCases) }}" min="0">
                                @error('UpperCases')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Number of upper cases (optional)</small>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="LowerCases" class="form-label">Lower Cases</label>
                                <input type="number" class="form-control @error('LowerCases') is-invalid @enderror" 
                                       id="LowerCases" name="LowerCases" value="{{ old('LowerCases', $patient->LowerCases) }}" min="0">
                                @error('LowerCases')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Number of lower cases (optional)</small>
                            </div>
                        </div>
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="{{ route('lab.patients.show', $patient) }}" class="btn btn-secondary me-md-2">
                            <i class="fas fa-times me-2"></i>Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Update Patient
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('ScanningFor').addEventListener('change', function() {
    const othersDiv = document.getElementById('scanningForOthersDiv');
    const othersInput = document.getElementById('ScanningForOthers');
    
    if (this.value === 'Others') {
        othersDiv.style.display = 'block';
        othersInput.required = true;
    } else {
        othersDiv.style.display = 'none';
        othersInput.required = false;
        othersInput.value = '';
    }
});
</script>
@endsection
