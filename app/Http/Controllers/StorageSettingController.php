<?php

namespace App\Http\Controllers;

use App\Helpers\LogHelper;
use App\Models\Setting;
use App\Services\StorageMigrationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Throwable;

class StorageSettingController extends Controller
{
    private const PROGRESS_KEY = 'storage_migration.progress';

    private const BATCH_SIZE = 10;

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

        $files = [
            'public' => $this->migration->fileList('public'),
            's3' => $this->migration->fileList('s3'),
        ];

        $counts = [
            'public' => count($files['public']),
            's3' => count($files['s3']),
        ];

        $otherDisk = $currentDisk === 's3' ? 'public' : 's3';
        $activeSet = array_flip($files[$currentDisk]);
        $missing = array_values(array_filter(
            $files[$otherDisk],
            fn (string $file) => ! isset($activeSet[$file])
        ));

        $inconsistent = [
            'has' => count($missing) > 0,
            'count' => count($missing),
            'disk' => Setting::DISKS[$otherDisk],
            'current' => Setting::DISKS[$currentDisk] ?? $currentDisk,
        ];

        $progress = $this->progressState();

        return view('settings.storage', compact(
            'currentDisk',
            'disks',
            'r2Configured',
            'counts',
            'inconsistent',
            'progress'
        ));
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
        $currentDisk = config('filesystems.default');
        if (! in_array($currentDisk, ['s3', 'public'], true)) {
            return response()->json(['error' => "Disk '$currentDisk' tidak didukung."], 422);
        }

        $state = Cache::get(self::PROGRESS_KEY);

        if (! $state) {
            $deleteSource = $request->boolean('delete_source');
            $sourceDisk = $currentDisk === 's3' ? 'public' : 's3';

            $files = array_values(array_filter(
                Storage::disk($sourceDisk)->allFiles(),
                fn (string $file) => ! str_starts_with(basename($file), '.')
            ));

            $state = [
                'source' => $sourceDisk,
                'target' => $currentDisk,
                'delete_source' => $deleteSource,
                'files' => $files,
                'index' => 0,
                'total' => count($files),
            ];
        }

        if ($state['total'] === 0) {
            Cache::forget(self::PROGRESS_KEY);

            return response()->json([
                'finished' => true,
                'percent' => 100,
                'done' => 0,
                'total' => 0,
                'message' => 'Tidak ada file untuk dimigrasi.',
            ]);
        }

        $source = Storage::disk($state['source']);
        $target = Storage::disk($state['target']);
        $files = $state['files'];

        $batch = min(self::BATCH_SIZE, $state['total'] - $state['index']);
        $failed = 0;

        for ($i = 0; $i < $batch; $i++) {
            $file = $files[$state['index']];

            try {
                if (! $target->exists($file)) {
                    $target->put($file, $source->get($file));
                }

                if ($state['delete_source']) {
                    $source->delete($file);
                }
            } catch (Throwable) {
                $failed++;
            }

            $state['index']++;
        }

        $done = $state['index'];
        $percent = (int) round($done / $state['total'] * 100);

        if ($done >= $state['total']) {
            Cache::forget(self::PROGRESS_KEY);
            LogHelper::log('MIGRATE_STORAGE', 'Migrasi file selesai ke '.Setting::DISKS[$currentDisk], null, [
                'done' => $done,
                'failed' => $failed,
            ]);

            return response()->json([
                'finished' => true,
                'percent' => 100,
                'done' => $done,
                'total' => $state['total'],
                'failed' => $failed,
            ]);
        }

        Cache::put(self::PROGRESS_KEY, $state, now()->addHour());

        return response()->json([
            'finished' => false,
            'percent' => $percent,
            'done' => $done,
            'total' => $state['total'],
            'failed' => $failed,
        ]);
    }

    private function progressState(): array
    {
        $state = Cache::get(self::PROGRESS_KEY);

        if (! $state) {
            return ['running' => false, 'percent' => 0, 'done' => 0, 'total' => 0];
        }

        return [
            'running' => $state['index'] < $state['total'],
            'percent' => (int) round($state['index'] / max(1, $state['total']) * 100),
            'done' => $state['index'],
            'total' => $state['total'],
        ];
    }
}
