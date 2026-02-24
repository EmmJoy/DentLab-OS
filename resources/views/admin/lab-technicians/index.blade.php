@extends('layouts.dashboard')

@section('title', 'Lab Technicians - SmileCare')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div class="page-title">
        <i class="fas fa-user-md me-3"></i>
        <h1>Lab Technician Management</h1>
    </div>
    <a href="{{ route('admin.lab-technicians.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i>Add New Technician
    </a>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-list me-2"></i>All Lab Technicians</h5>
    </div>
    <div class="card-body">
        @if(session('error'))
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}
            </div>
        @endif
        @if(session('labtech_created'))
            @php $c = session('labtech_created'); @endphp
            <div class="alert alert-success">
                <div class="d-flex align-items-start">
                    <i class="fas fa-check-circle me-2 mt-1"></i>
                    <div>
                        <div class="fw-bold">Technician created successfully. Please note credentials below:</div>
                        <div class="mt-2 small">
                            <div><strong>Name:</strong> {{ $c['name'] }}</div>
                            <div><strong>Username:</strong> <code>{{ $c['username'] }}</code></div>
                            <div><strong>Email:</strong> {{ $c['email'] ?? '—' }}</div>
                            <div><strong>Password:</strong> <code>{{ $c['password'] }}</code> <span class="text-muted">(shown once)</span></div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
        @if($technicians->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Technician</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Status</th>
                            <th>Created On</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($technicians as $technician)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle me-3" style="width: 45px; height: 45px; background: linear-gradient(135deg, #28a745 0%, #20c997 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 18px;">
                                        {{ strtoupper(substr($technician->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="fw-bold">{{ $technician->name }}</div>
                                        <small class="text-muted">Lab Technician</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark">{{ $technician->username }}</span>
                            </td>
                            <td>{{ $technician->email ?: 'No email' }}</td>
                            <td>
                                <span class="badge {{ $technician->status == 'active' ? 'bg-success' : 'bg-secondary' }}">
                                    <i class="fas fa-{{ $technician->status == 'active' ? 'check' : 'times' }} me-1"></i>
                                    {{ ucfirst($technician->status) }}
                                </span>
                            </td>
                            <td>{{ $technician->created_at->format('M d, Y') }}</td>
                            <td>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('admin.lab-technicians.edit', $technician) }}" class="btn btn-sm btn-outline-warning" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                   
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-4">
                {{ $technicians->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-user-md fa-4x text-muted mb-4"></i>
                <h4 class="text-muted">No lab technicians found</h4>
                <p class="text-muted mb-4">Create accounts for your lab technicians to give them system access.</p>
                <a href="{{ route('admin.lab-technicians.create') }}" class="btn btn-primary btn-lg">
                    <i class="fas fa-plus me-2"></i>Add First Technician
                </a>
            </div>
        @endif
    </div>
</div>

<!-- Technician Details Modal -->
<div class="modal fade" id="technicianModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-user-md me-2"></i>Technician Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="technicianDetails">
                <!-- Details will be loaded here -->
            </div>
        </div>
    </div>
</div>

<script>
function showTechnicianDetails(id) {
    // This would typically make an AJAX call to get technician details
    document.getElementById('technicianDetails').innerHTML = '<p>Technician details would be loaded here...</p>';
    new bootstrap.Modal(document.getElementById('technicianModal')).show();
}

function resetPassword(id) {
    if (confirm('Are you sure you want to reset this technician\'s password?')) {
        // This would typically make an AJAX call to reset password
        alert('Password reset functionality would be implemented here.');
    }
}
</script>
@endsection
