@php $admin = auth('admin')->user(); @endphp
<nav class="nav flex-column py-2">
    @if($admin->hasPermission('dashboard'))
    <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
        <i class="bi bi-speedometer2"></i> لوحة التحكم
    </a>
    @endif

    @if($admin->hasPermission('reports') || $admin->hasPermission('dashboard'))
    <a href="{{ route('admin.reports.index') }}" class="nav-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
        <i class="bi bi-bar-chart-line"></i> التقارير والإحصاءات
    </a>
    @endif

    @if($admin->hasPermission('hospitals') || $admin->hasPermission('hospitals.view'))
    <a href="{{ route('admin.hospitals.index') }}" class="nav-link {{ request()->routeIs('admin.hospitals.*') ? 'active' : '' }}">
        <i class="bi bi-building"></i> المستشفيات
    </a>
    @endif

    @if($admin->hasPermission('patients'))
    <a href="{{ route('admin.patients.index') }}" class="nav-link {{ request()->routeIs('admin.patients.*') ? 'active' : '' }}">
        <i class="bi bi-people"></i> المستخدمون
    </a>
    @endif

    @if($admin->hasPermission('specialties'))
    <a href="{{ route('admin.specialties.index') }}" class="nav-link {{ request()->routeIs('admin.specialties.*') ? 'active' : '' }}">
        <i class="bi bi-heart-pulse"></i> التخصصات الطبية
    </a>
    @endif

    @if($admin->hasPermission('audit_logs'))
    <a href="{{ route('admin.audit-logs.index') }}" class="nav-link {{ request()->routeIs('admin.audit-logs.*') ? 'active' : '' }}">
        <i class="bi bi-journal-text"></i> سجل التدقيق
    </a>
    @endif

    @if($admin->hasPermission('settings'))
    <a href="{{ route('admin.settings.edit') }}" class="nav-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
        <i class="bi bi-gear"></i> الإعدادات
    </a>
    @endif

    @if($admin->hasPermission('admin_users'))
    <a href="{{ route('admin.admin-users.index') }}" class="nav-link {{ request()->routeIs('admin.admin-users.*') ? 'active' : '' }}">
        <i class="bi bi-shield-lock"></i> مستخدمو الإدارة
    </a>
    @endif
</nav>
