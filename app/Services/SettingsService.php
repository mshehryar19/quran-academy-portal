<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

class SettingsService
{
    private const CACHE_KEY = 'app_settings_kv';

    private const CACHE_TTL_SECONDS = 600;

    /**
     * @return array<string, string|null>
     */
    public function defaults(): array
    {
        return [
            'system_name' => config('app.name', 'Quran Academy Portal'),
            'default_currency' => 'GBP',
            'default_timezone' => config('app.timezone', 'UTC'),
            'invoice_number_prefix' => 'INV',
        ];
    }

    public function get(string $key, ?string $default = null): ?string
    {
        $map = $this->allKeyed();
        $defaults = $this->defaults();
        $v = $map[$key] ?? null;

        if ($v !== null && $v !== '') {
            return $v;
        }

        return $defaults[$key] ?? $default;
    }

    /**
     * @param  array<string, string|null>  $pairs
     */
    public function setMany(array $pairs): void
    {
        $allowed = array_keys($this->defaults());

        foreach ($pairs as $key => $value) {
            if (! in_array($key, $allowed, true)) {
                continue;
            }

            Setting::query()->updateOrCreate(
                ['setting_key' => $key],
                ['value' => $value === null ? null : (string) $value]
            );
        }

        $this->flushCache();
    }

    public function flushCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    /**
     * @return array<string, string|null>
     */
    public function allKeyed(): array
    {
        return Cache::remember(self::CACHE_KEY, self::CACHE_TTL_SECONDS, function (): array {
            $defaults = $this->defaults();
            /** @var array<string, string|null> $rows */
            $rows = Setting::query()->pluck('value', 'setting_key')->all();

            return array_replace($defaults, $rows);
        });
    }

    /**
     * Merged values for forms (defaults overwritten by DB).
     *
     * @return array<string, string|null>
     */
    public function forForm(): array
    {
        return $this->allKeyed();
    }
}
