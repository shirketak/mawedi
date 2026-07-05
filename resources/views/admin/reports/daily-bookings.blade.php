@extends('layouts.admin')

@section('page-title', 'الحجوزات اليومية')

@section('content')
@include('partials.report-nav')

<div class="card mb-3">
    <div class="card-body">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-12 col-sm-4 col-md-3">
                <label class="form-label">عدد الأيام</label>
                <select name="days" class="form-select" onchange="this.form.submit()">
                    @foreach([7, 14, 30, 60, 90] as $option)
                        <option value="{{ $option }}" @selected($days === $option)>{{ $option }} يوم</option>
                    @endforeach
                </select>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header bg-white"><h6 class="mb-0">الحجوزات اليومية ({{ $days }} يوم)</h6></div>
    <div class="card-body" style="min-height: 360px;">
        <canvas id="reportChart"></canvas>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
new Chart(document.getElementById('reportChart'), {
    type: 'line',
    data: {
        labels: @json($chart['labels']),
        datasets: [{
            label: 'الحجوزات',
            data: @json($chart['values']),
            borderColor: '#3DA8C4',
            backgroundColor: 'rgba(61, 168, 196, 0.1)',
            fill: true,
            tension: 0.3
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }
    }
});
</script>
@endpush
