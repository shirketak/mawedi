<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'موعدي') - الإدارة</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">
    <link href="{{ asset('css/dashboard.css') }}" rel="stylesheet">
    @stack('styles')
</head>
<body>
    {{-- Mobile offcanvas sidebar --}}
    <div class="offcanvas offcanvas-end offcanvas-sidebar offcanvas-sidebar--admin d-lg-none" tabindex="-1" id="adminSidebar" aria-labelledby="adminSidebarLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="adminSidebarLabel"><i class="bi bi-hospital"></i> موعدي - الإدارة</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="إغلاق"></button>
        </div>
        <div class="offcanvas-body p-0">
            @include('partials.admin-nav')
        </div>
    </div>

    {{-- Desktop sidebar --}}
    <aside class="app-sidebar app-sidebar--admin">
        <div class="brand"><i class="bi bi-hospital"></i> موعدي - الإدارة</div>
        @include('partials.admin-nav')
    </aside>

    <div class="main-content">
        <header class="topbar d-flex flex-wrap justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-2 min-w-0 flex-grow-1">
                <button class="btn btn-outline-primary d-lg-none flex-shrink-0" type="button" data-bs-toggle="offcanvas" data-bs-target="#adminSidebar" aria-controls="adminSidebar" aria-label="فتح القائمة">
                    <i class="bi bi-list fs-5"></i>
                </button>
                <h1 class="topbar-title">@yield('page-title', 'لوحة التحكم')</h1>
            </div>
            <div class="d-flex align-items-center gap-2 flex-shrink-0">
                <span class="text-muted topbar-user d-none d-sm-inline">{{ auth('admin')->user()->name }}</span>
                <form action="{{ route('admin.logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger btn-sm">
                        <i class="bi bi-box-arrow-right"></i>
                        <span class="d-none d-sm-inline"> خروج</span>
                    </button>
                </form>
            </div>
        </header>
        <main class="content-area">
            @include('partials.alerts')
            @yield('content')
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('adminSidebar')?.querySelectorAll('.nav-link').forEach(link => {
            link.addEventListener('click', () => {
                bootstrap.Offcanvas.getInstance(document.getElementById('adminSidebar'))?.hide();
            });
        });
    </script>
    @stack('scripts')
</body>
</html>
