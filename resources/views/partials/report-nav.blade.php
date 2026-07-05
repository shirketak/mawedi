<nav aria-label="تقارير فرعية" class="mb-3 mb-md-4">
    <div class="d-flex flex-wrap gap-2">
        <a href="{{ route('admin.reports.index') }}" class="btn btn-sm {{ request()->routeIs('admin.reports.index') ? 'btn-primary' : 'btn-outline-secondary' }}">
            <i class="bi bi-grid"></i> كل التقارير
        </a>
        <a href="{{ route('admin.reports.system-overview') }}" class="btn btn-sm {{ request()->routeIs('admin.reports.system-overview') ? 'btn-primary' : 'btn-outline-secondary' }}">نظرة عامة</a>
        <a href="{{ route('admin.reports.hospitals-stats') }}" class="btn btn-sm {{ request()->routeIs('admin.reports.hospitals-stats') ? 'btn-primary' : 'btn-outline-secondary' }}">المستشفيات</a>
        <a href="{{ route('admin.reports.bookings-stats') }}" class="btn btn-sm {{ request()->routeIs('admin.reports.bookings-stats') ? 'btn-primary' : 'btn-outline-secondary' }}">الحجوزات</a>
        <a href="{{ route('admin.reports.financial-stats') }}" class="btn btn-sm {{ request()->routeIs('admin.reports.financial-stats') ? 'btn-primary' : 'btn-outline-secondary' }}">المالية</a>
        <a href="{{ route('admin.reports.daily-bookings') }}" class="btn btn-sm {{ request()->routeIs('admin.reports.daily-bookings') ? 'btn-primary' : 'btn-outline-secondary' }}">يومي</a>
        <a href="{{ route('admin.reports.monthly-bookings') }}" class="btn btn-sm {{ request()->routeIs('admin.reports.monthly-bookings') ? 'btn-primary' : 'btn-outline-secondary' }}">شهري</a>
        <a href="{{ route('admin.reports.user-growth') }}" class="btn btn-sm {{ request()->routeIs('admin.reports.user-growth') ? 'btn-primary' : 'btn-outline-secondary' }}">نمو المستخدمين</a>
        <a href="{{ route('admin.reports.hospital-growth') }}" class="btn btn-sm {{ request()->routeIs('admin.reports.hospital-growth') ? 'btn-primary' : 'btn-outline-secondary' }}">نمو المستشفيات</a>
        <a href="{{ route('admin.reports.top-hospitals') }}" class="btn btn-sm {{ request()->routeIs('admin.reports.top-hospitals') ? 'btn-primary' : 'btn-outline-secondary' }}">أكثر المستشفيات</a>
        <a href="{{ route('admin.reports.top-specialties') }}" class="btn btn-sm {{ request()->routeIs('admin.reports.top-specialties') ? 'btn-primary' : 'btn-outline-secondary' }}">أكثر التخصصات</a>
    </div>
</nav>
