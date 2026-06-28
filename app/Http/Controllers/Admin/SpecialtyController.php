<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\FileUploader;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreSpecialtyRequest;
use App\Http\Requests\Admin\UpdateSpecialtyRequest;
use App\Models\Specialty;
use App\Services\SpecialtyService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SpecialtyController extends Controller
{
    public function __construct(
        private readonly SpecialtyService $specialtyService,
    ) {}

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Specialty::class);

        $specialties = $this->specialtyService->list($request->only(['search', 'is_active']));

        return view('admin.specialties.index', compact('specialties'));
    }

    public function create(): View
    {
        $this->authorize('create', Specialty::class);

        return view('admin.specialties.create');
    }

    public function store(StoreSpecialtyRequest $request): RedirectResponse
    {
        $this->authorize('create', Specialty::class);

        $data = $request->validated();
        $data['is_active'] = true;

        if ($request->hasFile('icon')) {
            $data['icon'] = FileUploader::upload($request->file('icon'), 'specialties/icons');
        }

        $this->specialtyService->create($data);

        return redirect()->route('admin.specialties.index')
            ->with('success', 'تم إضافة التخصص بنجاح.');
    }

    public function edit(Specialty $specialty): View
    {
        $this->authorize('update', $specialty);

        return view('admin.specialties.edit', compact('specialty'));
    }

    public function update(UpdateSpecialtyRequest $request, Specialty $specialty): RedirectResponse
    {
        $this->authorize('update', $specialty);

        $data = $request->validated();

        if ($request->hasFile('icon')) {
            FileUploader::delete($specialty->icon);
            $data['icon'] = FileUploader::upload($request->file('icon'), 'specialties/icons');
        }

        $this->specialtyService->update($specialty, $data);

        return redirect()->route('admin.specialties.index')
            ->with('success', 'تم تحديث التخصص بنجاح.');
    }

    public function destroy(Specialty $specialty): RedirectResponse
    {
        $this->authorize('delete', $specialty);

        FileUploader::delete($specialty->icon);
        $this->specialtyService->delete($specialty);

        return redirect()->route('admin.specialties.index')
            ->with('success', 'تم حذف التخصص بنجاح.');
    }

    public function toggleStatus(Specialty $specialty): RedirectResponse
    {
        $this->authorize('update', $specialty);

        $this->specialtyService->toggleStatus($specialty);
        $status = $specialty->fresh()->is_active ? 'تفعيل' : 'إيقاف';

        return back()->with('success', "تم {$status} التخصص بنجاح.");
    }
}
