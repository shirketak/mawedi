<?php

namespace App\Services;

use App\Models\SystemSetting;
use Illuminate\Support\Facades\Cache;

class SystemSettingService
{
    private const CACHE_KEY = 'system_settings.all';

    public function all(): array
    {
        return Cache::rememberForever(self::CACHE_KEY, function () {
            return SystemSetting::query()->pluck('value', 'key')->all();
        });
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->all()[$key] ?? $default;
    }

    public function set(string $key, mixed $value, string $group = 'general'): void
    {
        SystemSetting::updateOrCreate(
            ['key' => $key],
            ['value' => is_array($value) ? json_encode($value) : (string) $value, 'group' => $group]
        );

        Cache::forget(self::CACHE_KEY);
    }

    public function setMany(array $settings, string $group = 'general'): void
    {
        foreach ($settings as $key => $value) {
            $this->set($key, $value, $group);
        }
    }

    public function defaults(): array
    {
        return [
            'app_name' => 'موعدي',
            'app_logo' => null,
            'app_favicon' => null,
            'contact_email' => null,
            'contact_phone' => null,
            'contact_address' => null,
            'social_facebook' => null,
            'social_twitter' => null,
            'social_instagram' => null,
            'privacy_policy' => null,
            'terms_of_use' => null,
            'default_monthly_price' => '100.00',
            'default_usage_fee_per_booking' => '5.00',
            'default_free_trial_days' => '14',
        ];
    }

    public function ensureDefaults(): void
    {
        foreach ($this->defaults() as $key => $value) {
            if (! SystemSetting::where('key', $key)->exists()) {
                $this->set($key, $value);
            }
        }
    }
}
