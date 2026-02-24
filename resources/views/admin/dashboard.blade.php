@extends('layouts.dashboard')

@section('title', 'Admin Dashboard - SmileCare')

@section('content')
<div class="page-title">
    <i class="fas fa-tachometer-alt me-3"></i>
    <h1>Admin Dashboard</h1>
</div>


<!-- Analytics Charts -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #6366F1 0%, #8B5CF6 100%); color: #fff;">
                <h5 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Patients Added Per Month (Stacked by Case Type)</h5>
                <span class="badge bg-light text-dark">Last 12 months</span>
            </div>
            <div class="card-body">
                <canvas id="patientsStackedChart" height="110"></canvas>
            </div>
        </div>
    </div>
    <div class="col-12 mt-4">
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #10B981 0%, #06B6D4 100%); color: #fff;">
                <h5 class="mb-0"><i class="fas fa-chart-line me-2"></i>Monthly Payment Plan Totals</h5>
                <span class="badge bg-light text-dark">Last 12 months</span>
            </div>
            <div class="card-body">
                <canvas id="paymentsLineChart" height="110"></canvas>
            </div>
        </div>
    </div>
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
(() => {
  const labels = @json($charts['labels'] ?? []);
  const caseTypes = @json($caseTypes ?? []);
  const patientsData = @json($charts['patients'] ?? []);
  const paymentsData = @json($charts['payments'] ?? []);

  // Nice distinct colors
  const palette = [
    '#6366F1', '#84CC16', '#F59E0B', '#EC4899', '#06B6D4', '#A855F7', '#10B981'
  ];

  // Stacked Bar: Patients by Case Type
  const stackedDatasets = (caseTypes||[]).map((ct, idx) => ({
    label: ct,
    data: patientsData[ct] || Array(labels.length).fill(0),
    backgroundColor: palette[idx % palette.length] + 'E6', // semi-transparent
    borderColor: palette[idx % palette.length],
    borderWidth: 1,
    borderRadius: 4,
  }));

  const patientsCtx = document.getElementById('patientsStackedChart');
  if (patientsCtx) {
    new Chart(patientsCtx, {
      type: 'bar',
      data: { labels, datasets: stackedDatasets },
      options: {
        responsive: true,
        interaction: { mode: 'index', intersect: false },
        plugins: {
          legend: { position: 'top' },
          tooltip: { callbacks: {
            footer: (items) => {
              const total = items.reduce((s, it) => s + it.parsed.y, 0);
              return 'Total: ' + total;
            }
          }},
        },
        scales: {
          x: { stacked: true, grid: { display: false } },
          y: { stacked: true, beginAtZero: true, ticks: { stepSize: 1 } }
        }
      }
    });
  }

  // Line: Monthly Payments
  const paymentsCtx = document.getElementById('paymentsLineChart');
  if (paymentsCtx) {
    new Chart(paymentsCtx, {
      type: 'line',
      data: {
        labels,
        datasets: [{
          label: 'Payments (BDT)',
          data: paymentsData || [],
          fill: true,
          tension: 0.35,
          borderColor: '#10B981',
          backgroundColor: 'rgba(16, 185, 129, 0.15)',
          pointBackgroundColor: '#10B981',
          pointBorderColor: '#064E3B',
          pointRadius: 4,
          pointHoverRadius: 6,
        }]
      },
      options: {
        responsive: true,
        plugins: {
          legend: { display: true },
          tooltip: { mode: 'index', intersect: false },
        },
        scales: {
          x: { grid: { display: false } },
          y: { beginAtZero: true }
        }
      }
    });
  }
})();
</script>
@endpush
</div>

@endsection
