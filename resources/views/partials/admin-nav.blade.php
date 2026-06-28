<nav class="nav flex-column py-2">
    <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
        <i class="bi bi-speedometer2"></i> لوحة التحكم
    </a>
    <a href="{{ route('admin.hospitals.index') }}" class="nav-link {{ request()->routeIs('admin.hospitals.*') ? 'active' : '' }}">
        <i class="bi bi-building"></i> المستشفيات
    </a>
    <a href="{{ route('admin.specialties.index') }}" class="nav-link {{ request()->routeIs('admin.specialties.*') ? 'active' : '' }}">
        <i class="bi bi-heart-pulse"></i> التخصصات الطبية
    </a>
</nav>
