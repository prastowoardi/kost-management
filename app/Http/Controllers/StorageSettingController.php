<?php

namespace App\Http\Controllers;

use App\Helpers\LogHelper;
use App\Models\Setting;
use App\Services\StorageMigrationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Throwable;

class StorageSettingController extends Controller
{
    public function __construct(
        private StorageMigrationService $migration,
    ) {}

    public function index()
    {
        $currentDisk = config('filesystems.default');
        $disks = Setting::DISKS;

        $r2Configured = filled(Config::get('filesystems.disks.s3.bucket'))
            && filled(Config::get('filesystems.disks.s3.key'))
            && filled(Config::get('filesystems.disks.s3.secret'));

        $counts = [
            'public' => $this->migration->countFiles('public'),
            's3' => $this->migration->countFiles('s3'),
        ];

        $otherDisk = $currentDisk === 's3' ? 'public' : 's3';
        $inconsistent = [
            'has' => $counts[$otherDisk] > 0,
            'count' => $counts[$otherDisk],
            'disk' => Setting::DISKS[$otherDisk],
            'current' => Setting::DISKS[$currentDisk] ?? $currentDisk,
        ];

        return view('settings.storage', compact('currentDisk', 'disks', 'r2Configured', 'counts', 'inconsistent'));
    }

    public function switch(Request $request)
    {
        $validated = $request->validate([
            'disk' => 'required|in:public,s3',
        ]);

        $target = $validated['disk'];

        if ($target === config('filesystems.default')) {
            return back()->with('success', 'Penyimpanan sudah menggunakan disk tersebut.');
        }

        Setting::set(Setting::STORAGE_DISK_KEY, $target);
        Cache::forget('setting.storage_disk');

        LogHelper::log('SWITCH_STORAGE', "Mengganti penyimpanan file ke ".Setting::DISKS[$target]);

        return back()->with('success', "Penyimpanan diganti ke ".Setting::DISKS[$target].". Jangan lupa migrasi file.");
    }

    public function migrate(Request $request)
    {
        try {
            $currentDisk = config('filesystems.default');
            $deleteSource = $request->boolean('delete_source');

            $result = match ($currentDisk) {
                's3' => $this->migration->migrate('public', 's3', $deleteSource),
                'public' => $this->migration->migrate('s3', 'public', $deleteSource),
                default => throw new \RuntimeException("Disk '$currentDisk' tidak didukung."),
            };

            LogHelper::log('MIGRATE_STORAGE', "Memigrasi file ke ".Setting::DISKS[$currentDisk], null, $result);

            return back()->with('success', sprintf(
                'Migrasi selesai: %d disalin, %d sudah ada, %d dihapus, %d total.',
                $result['copied'],
                $result['skipped'],
                $result['deleted'],
                $result['total']
            ));
        } catch (Throwable $e) {
            return back()->with('error', 'Gagal migrasi file: '.$e->getMessage());
        }
    }
}
