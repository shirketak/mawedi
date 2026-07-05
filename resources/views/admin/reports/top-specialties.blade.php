@extends('layouts.admin')

@section('page-title', 'أكثر التخصصات حجزاً')

@section('content')
@include('partials.report-nav')

<div class="card mb-3">
    <div class="card-body">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-12 col-sm-4 col-md-3">
                <label class="form-label">عدد النتائج</label>
                <select name="limit" class="form-select" onchange="this.form.submit()">
                    @foreach([5, 10, 15, 20] as $option)
                        <option value="{{ $option }}" @selected($limit === $option)>أفضل {{ $option }}</option>
                    @endforeach
                </select>
            </div>
        </form>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header bg-white"><h6 class="mb-0">الرسم البياني</h6></div>
    <div class="card-body" style="min-height: {{ max(280, count($items) * 40) }}px;">
        <canvas id="reportChart"></canvas>
    </div>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead><tr><th>#</th><th>التخصص</th><th>عدد الحجوزات</th></tr></thead>
            <tbody>
                @forelse($items as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item['name'] }}</td>
                    <td><span class="badge bg-success">{{ number_format($item['total']) }}</span></td>
                </tr>
                @empty
                <tr><td colspan="3" class="text-center text-muted py-4">لا توجد بيانات</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const items = @json($items);
new Chart(document.getElementById('reportChart'), {
    type: 'bar',
    data: {
        labels: items.map(i => i.name),
        datasets: [{
            label: 'الحجوزات',
            data: items.map(i => i.total),
            backgroundColor: '#3E9B52',
            borderRadius: 6
        }]
    },
    options: {
        indexAxis: 'y',
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: { x: { beginAtZero: true, ticks: { precision: 0 } } }
    }
});
</script>
@endpush
