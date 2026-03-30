<?php

namespace Database\Seeders;

use App\Models\Setting;
use App\Services\SettingsService;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        $service = app(SettingsService::class);
        $defaults = $service->defaults();

        foreach ($defaults as $key => $value) {
            Setting::query()->firstOrCreate(
                ['setting_key' => $key],
                ['value' => $value]
            );
        }

        $service->flushCache();
    }
}
