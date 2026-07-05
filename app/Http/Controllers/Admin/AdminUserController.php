<?php

namespace App\Http\Controllers\Admin;

use App\Enums\AdminRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreAdminUserRequest;
use App\Http\Requests\Admin\UpdateAdminUserRequest;
use App\Models\Admin;
use App\Services\AdminUserService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminUserController extends Controller
{
    public function __construct(
        private readonly AdminUserService $adminUserService,
    ) {}

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Admin::class);

        $admins = $this->adminUserService->list($request->only(['search', 'role', 'is_active']));
        $roles = AdminRole::options();

        return view('admin.admin-users.index', compact('admins', 'roles'));
    }

    public function create(): View
    {
        $this->authorize('create', Admin::class);

        $roles = AdminRole::options();

        return view('admin.admin-users.create', compact('roles'));
    }

    public function store(StoreAdminUserRequest $request): RedirectResponse
    {
        $this->authorize('create', Admin::class);

        $this->adminUserService->create($request->validated());

        return redirect()->route('admin.admin-users.index')
            ->with('success', 'تم إضافة المستخدم بنجاح.');
    }

    public function edit(Admin $admin_user): View
    {
        $this->authorize('update', $admin_user);

        $roles = AdminRole::options();

        return view('admin.admin-users.edit', compact('admin_user', 'roles'));
    }

    public function update(UpdateAdminUserRequest $request, Admin $admin_user): RedirectResponse
    {
        $this->authorize('update', $admin_user);

        $this->adminUserService->update($admin_user, $request->validated());

        return redirect()->route('admin.admin-users.index')
            ->with('success', 'تم تحديث المستخدم بنجاح.');
    }

    public function destroy(Admin $admin_user): RedirectResponse
    {
        $this->authorize('delete', $admin_user);

        try {
            $this->adminUserService->delete($admin_user, auth('admin')->user());
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()->route('admin.admin-users.index')
            ->with('success', 'تم حذف المستخدم بنجاح.');
    }

    public function toggleStatus(Admin $admin_user): RedirectResponse
    {
        $this->authorize('toggleStatus', $admin_user);

        try {
            $this->adminUserService->toggleStatus($admin_user, auth('admin')->user());
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        $status = $admin_user->fresh()->is_active ? 'تفعيل' : 'إيقاف';

        return back()->with('success', "تم {$status} المستخدم بنجاح.");
    }
}
