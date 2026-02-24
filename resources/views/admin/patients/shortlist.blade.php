@extends('layouts.dashboard')

@section('title', 'Shortlist - SmileCare')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div class="page-title">
        <i class="fas fa-filter me-3"></i>
        <h1>Shortlist</h1>
        <small class="text-muted ms-2">
            @if($type === 'patients')
                Patients added in the last 15 days (since {{ optional($since)->format('M d, Y') }})
            @else
                Doctors added in the last 15 days (since {{ optional($since)->format('M d, Y') }})
            @endif
        </small>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.patients.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Patients
        </a>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0">
            @if($type === 'patients')
                <i class="fas fa-users me-2"></i>Last 15 Days Patients
            @else
                <i class="fas fa-user-md me-2"></i>Last 15 Days Doctors
            @endif
        </h5>
    </div>
    <div class="card-body">
        @if($type === 'patients')
            @if(($patients ?? collect())->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped align-middle">
                        <thead>
                            <tr>
                                <th>Predict3DId</th>
                                <th>Full Name</th>
                                <th>Phone</th>
                                <th>Gender</th>
                                <th>DOB</th>
                                <th>Doctor</th>
                                <th>Doctor Email</th>
                                <th>Created At</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($patients as $p)
                                <tr>
                                    <td><code>{{ $p->Predict3DId }}</code></td>
                                    <td>{{ $p->FullName }}</td>
                                    <td>{{ $p->PhoneNumber }}</td>
                                    <td>{{ $p->Gender }}</td>
                                    <td>{{ optional($p->DateOfBirth)->format('Y-m-d') }}</td>
                                    <td>{{ $p->DoctorName }}</td>
                                    <td>{{ $p->doctor_email ?? '—' }}</td>
                                    <td>{{ optional($p->created_at)->format('Y-m-d H:i') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-users fa-3x text-muted mb-3"></i>
                    <h6 class="text-muted">No patients found in the last 15 days.</h6>
                </div>
            @endif
        @else
            @if(($doctors ?? collect())->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped align-middle">
                        <thead>
                            <tr>
                                <th>Doctor Name</th>
                                <th>Email</th>
                                <th>First Seen</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($doctors as $d)
                                <tr>
                                    <td>{{ $d->DoctorName }}</td>
                                    <td>{{ $d->doctor_email ?? '—' }}</td>
                                    <td>{{ optional($d->created_at)->format('Y-m-d H:i') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-user-md fa-3x text-muted mb-3"></i>
                    <h6 class="text-muted">No doctors found in the last 15 days.</h6>
                </div>
            @endif
        @endif
    </div>
</div>
@endsection
