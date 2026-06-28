<?php

namespace App\Services;

use App\Helpers\FileUploader;
use App\Models\Hospital;
use Illuminate\Support\Facades\DB;

class HospitalProfileService
{
    public function __construct(
        private readonly HospitalService $hospitalService,
    ) {}

    public function update(Hospital $hospital, array $data, ?\Illuminate\Http\UploadedFile $logo = null): Hospital
    {
        return DB::transaction(function () use ($hospital, $data, $logo) {
            if ($logo) {
                FileUploader::delete($hospital->logo);
                $data['logo'] = FileUploader::upload($logo, 'hospitals/logos');
            }

            return $this->hospitalService->update($hospital, $data);
        });
    }
}
