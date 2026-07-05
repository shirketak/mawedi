<?php

namespace App\Http\Controllers\Admin;

use App\Enums\AuditAction;
use App\Http\Controllers\Controller;
use App\Repositories\Contracts\AuditLogRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuditLogController extends Controller
{
    public function __construct(
        private readonly AuditLogRepositoryInterface $auditLogRepository,
    ) {}

    public function index(Request $request): View
    {
        abort_unless(auth('admin')->user()->hasPermission('audit_logs'), 403);

        $logs = $this->auditLogRepository->paginateWithFilters(
            $request->only(['search', 'action', 'date_from', 'date_to'])
        );
        $actions = AuditAction::cases();

        return view('admin.audit-logs.index', compact('logs', 'actions'));
    }
}
