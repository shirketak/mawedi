<nav class="nav flex-column py-2">
    <a href="{{ route('hospital.dashboard') }}" class="nav-link {{ request()->routeIs('hospital.dashboard') ? 'active' : '' }}">
        <i class="bi bi-speedometer2"></i> لوحة التحكم
    </a>
    <a href="{{ route('hospital.profile.edit') }}" class="nav-link {{ request()->routeIs('hospital.profile.*') ? 'active' : '' }}">
        <i class="bi bi-building-gear"></i> بيانات المستشفى
    </a>
    <a href="{{ route('hospital.specialties.index') }}" class="nav-link {{ request()->routeIs('hospital.specialties.*') ? 'active' : '' }}">
        <i class="bi bi-heart-pulse"></i> التخصصات
    </a>
    <a href="{{ route('hospital.doctors.index') }}" class="nav-link {{ request()->routeIs('hospital.doctors.*') ? 'active' : '' }}">
        <i class="bi bi-person-badge"></i> الأطباء
    </a>
    <a href="{{ route('hospital.bookings.index') }}" class="nav-link {{ request()->routeIs('hospital.bookings.*') ? 'active' : '' }}">
        <i class="bi bi-calendar-check"></i> الحجوزات
    </a>
    <a href="{{ route('hospital.reschedule-logs.index') }}" class="nav-link {{ request()->routeIs('hospital.reschedule-logs.*') ? 'active' : '' }}">
        <i class="bi bi-clock-history"></i> سجل التأجيل
    </a>
</nav>
