@extends('layouts.dashboard')

@section('title', 'Patients - SmileCare')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div class="page-title">
        <i class="fas fa-users me-3"></i>
        <h1>Patient Management</h1>
    </div>
<!-- Export by Date Range Modal -->
<div class="modal fade" id="exportRangeModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fas fa-calendar me-2"></i>Export Patients by Date Range</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form method="GET" action="{{ route('admin.patients.export.range') }}" class="needs-validation" novalidate>
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">From <span class="text-danger">*</span></label>
              <input type="date" name="from" class="form-control" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">To <span class="text-danger">*</span></label>
              <input type="date" name="to" class="form-control" required>
            </div>
          </div>
          <div class="mt-2 small text-muted">The exported file will be an Excel (.xlsx) file.</div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-success"><i class="fas fa-file-export me-2"></i>Export</button>
        </div>
      </form>
    </div>
  </div>
</div>
    <div class="d-flex gap-2">
        <div class="btn-group">
            <button type="button" class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fas fa-filter me-2"></i>Shortlist
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                <li>
                    <a class="dropdown-item" href="{{ route('admin.patients.shortlist.patients') }}">
                        <i class="fas fa-user me-2"></i>Patient (last 15 days)
                    </a>
                </li>
                <li>
                    <a class="dropdown-item" href="{{ route('admin.patients.shortlist.doctors') }}">
                        <i class="fas fa-user-md me-2"></i>Doctor (last 15 days)
                    </a>
                </li>
            </ul>
        </div>

        <div class="btn-group">
            <button type="button" class="btn btn-outline-success dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fas fa-file-excel me-2"></i>Download Patient Details
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                <li>
                    <button class="dropdown-item" type="button" data-bs-toggle="modal" data-bs-target="#exportRangeModal">
                        <i class="fas fa-calendar-day me-2"></i>Download by date range
                    </button>
                </li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <a class="dropdown-item" href="{{ route('admin.patients.export.all') }}">
                        <i class="fas fa-download me-2"></i>Download all patient details
                    </a>
                </li>
                <li>
                    <a class="dropdown-item" href="{{ route('admin.patients.export.last_month') }}">
                        <i class="fas fa-calendar-alt me-2"></i>Download last 1 month patient details
                    </a>
                </li>
            </ul>
        </div>

        <a href="{{ route('admin.patients.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Add New Patient
        </a>
    </div>
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
                                        <small class="text-muted">{{ $patient->ScanningFor }}</small>
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
                                    {{ $patient->Gender }}
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
                                    <a href="{{ route('admin.patients.show', $patient) }}" class="btn btn-sm btn-outline-primary me-1">
                                        <i class="fas fa-eye me-1"></i>View
                                    </a>
                                    <a href="{{ route('admin.patients.edit', $patient) }}" class="btn btn-sm btn-outline-warning me-1">
                                        <i class="fas fa-edit me-1"></i>Edit
                                    </a>
                                    <form action="{{ route('admin.patients.destroy', $patient) }}" method="POST" style="display: inline;" 
                                          onsubmit="return confirm('Are you sure you want to delete this patient?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="fas fa-trash me-1"></i>Delete
                                        </button>
                                    </form>
                                  
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
                <p class="text-muted mb-4">Start building your patient database by adding the first patient.</p>
                <a href="{{ route('admin.patients.create') }}" class="btn btn-primary btn-lg">
                    <i class="fas fa-plus me-2"></i>Add First Patient
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
