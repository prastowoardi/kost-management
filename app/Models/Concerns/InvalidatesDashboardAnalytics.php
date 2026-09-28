<?php

namespace App\Models\Concerns;

use App\Services\DashboardAnalyticsService;

/**
 * Mengosongkan cache analitik dashboard saat model berubah, supaya tren
 * pendapatan, ageing tunggakan, dan okupansi tidak menampilkan data basi.
 */
trait InvalidatesDashboardAnalytics
{
    public static function bootInvalidatesDashboardAnalytics(): void
    {
        foreach (['saved', 'deleted', 'restored', 'forceDeleted'] as $event) {
            static::$event(function (): void {
                app(DashboardAnalyticsService::class)->clearCache();
            });
        }
    }
}
