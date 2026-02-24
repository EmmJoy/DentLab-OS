@extends('layouts.dashboard')

@section('title', 'Patients - SmileCare')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div class="page-title">
        <i class="fas fa-users me-3"></i>
        <h1>Patients</h1>
    </div>
    <a href="{{ route('lab.patients.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i>Add New Patient
    </a>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-list me-2"></i>All Patients</h5>
    </div>
    <div class="card-body">
        @if($patients->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Patient</th>
                            <th>Contact</th>
                            <th>Gender</th>
                            <th>Age</th>
                            <th>Status</th>
                            <th>Added By</th>
                            <th>Date Added</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($patients as $patient)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle me-3" style="width: 45px; height: 45px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 18px;">
                                        {{ strtoupper(substr($patient->FullName, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="fw-bold">{{ $patient->FullName }}</div>
                                        <small class="text-muted">{{ $patient->DoctorName }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div>
                                    <i class="fas fa-phone me-1"></i>{{ $patient->PhoneNumber }}
                                </div>
                                <small class="text-muted">
                                    <i class="fas fa-exclamation-triangle me-1"></i>{{ $patient->EmergencyContact }}
                                </small>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark">
                                    <i class="fas fa-{{ $patient->Gender == 'Male' ? 'mars' : ($patient->Gender == 'Female' ? 'venus' : 'genderless') }} me-1"></i>
                                    {{ ucfirst($patient->Gender) }}
                                </span>
                            </td>
                            <td>{{ \Carbon\Carbon::parse($patient->DateOfBirth)->age }} years</td>
                            <td>
                                <span class="badge {{ $patient->status == 'active' ? 'bg-success' : 'bg-secondary' }}">
                                    <i class="fas fa-{{ $patient->status == 'active' ? 'check' : 'times' }} me-1"></i>
                                    {{ ucfirst($patient->status) }}
                                </span>
                            </td>
                            <td>
                                <small class="text-muted">{{ $patient->creator->name }}</small>
                            </td>
                            <td>{{ $patient->created_at->format('M d, Y') }}</td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('lab.patients.show', $patient) }}" class="btn btn-sm btn-outline-primary" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('lab.patients.edit', $patient) }}" class="btn btn-sm btn-outline-warning" title="Edit Patient">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="{{ route('lab.payments.create') }}?patient_id={{ $patient->id }}" class="btn btn-sm btn-outline-success" title="Add Payment">
                                        <i class="fas fa-credit-card"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-outline-danger" title="Delete Patient" 
                                            data-bs-toggle="modal" data-bs-target="#deleteModal{{ $patient->id }}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-4">
                {{ $patients->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-users fa-4x text-muted mb-4"></i>
                <h4 class="text-muted">No patients found</h4>
                <p class="text-muted mb-4">No patients have been added to the system yet.</p>
            </div>
        @endif
    </div>
</div>

<!-- Delete Confirmation Modals -->
@if(count($patients) > 0)
    @foreach($patients as $patient)
        <div class="modal fade" id="deleteModal{{ $patient->id }}" tabindex="-1" aria-labelledby="deleteModalLabel{{ $patient->id }}" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteModalLabel{{ $patient->id }}">Confirm Delete</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to delete patient <strong>{{ $patient->FullName }}</strong>?</p>
                        <p class="text-danger"><small><i class="fas fa-exclamation-triangle me-1"></i>This action cannot be undone.</small></p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <form action="{{ route('lab.patients.destroy', $patient) }}" method="POST" style="display: inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">Delete Patient</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
@endif
@endsection
