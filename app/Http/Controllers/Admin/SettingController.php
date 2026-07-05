<?php

namespace App\Http\Controllers\Admin;

use App\Enums\AuditAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateSystemSettingsRequest;
use App\Services\AuditLogService;
use App\Services\SystemSettingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function __construct(
        private readonly SystemSettingService $settings,
        private readonly AuditLogService $auditLogService,
    ) {}

    public function edit(): View
    {
        abort_unless(auth('admin')->user()->hasPermission('settings'), 403);

        $settings = array_merge($this->settings->defaults(), $this->settings->all());

        return view('admin.settings.edit', compact('settings'));
    }

    public function update(UpdateSystemSettingsRequest $request): RedirectResponse
    {
        abort_unless(auth('admin')->user()->hasPermission('settings'), 403);

        $old = $this->settings->all();
        $this->settings->setMany($request->validated());
        $this->auditLogService->log(
            AuditAction::SettingsUpdated,
            null,
            $old,
            $request->validated(),
        );

        return back()->with('success', 'تم حفظ الإعدادات بنجاح.');
    }
}
