<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Throwable;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
        if (app()->environment('production')) {
            URL::forceScheme('https');
        }

        $this->applyStorageDiskSetting();
    }

    /**
     * Override filesystems.default dengan pilihan storage dari database.
     */
    protected function applyStorageDiskSetting(): void
    {
        try {
            $disk = Cache::remember('setting.storage_disk', now()->addDay(), function () {
                return Setting::where('key', Setting::STORAGE_DISK_KEY)->value('value');
            });

            if (in_array($disk, array_keys(Setting::DISKS), true)) {
                config(['filesystems.default' => $disk]);
            }
        } catch (Throwable) {
            // Tabel settings belum ada (pertama kali migrate) — abaikan.
        }
    }
}
