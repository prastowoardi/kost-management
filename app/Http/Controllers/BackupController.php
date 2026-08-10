<?php

namespace App\Http\Controllers;

use App\Helpers\LogHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class BackupController extends Controller
{
    public function index()
    {
        $dir = $this->backupDir();
        $files = [];

        foreach (glob($dir.'/backup-*.sql.gz') ?: [] as $file) {
            $files[] = [
                'name' => basename($file),
                'size' => round(filesize($file) / 1024 / 1024, 2),
                'created_at' => \Carbon\Carbon::createFromTimestamp(filemtime($file)),
            ];
        }

        usort($files, fn ($a, $b) => $b['created_at']->timestamp <=> $a['created_at']->timestamp);

        return view('settings.backup', compact('files'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'keep' => 'nullable|integer|min:1|max:365',
        ]);

        $keep = $request->integer('keep', 30);

        $exitCode = Artisan::call('database:backup', ['--keep' => $keep]);

        LogHelper::log('BACKUP_DATABASE', 'Membuat backup database manual');

        if ($exitCode !== 0) {
            return back()->with('error', 'Backup gagal dibuat. Lihat log untuk detail.');
        }

        return back()->with('success', 'Backup database berhasil dibuat.');
    }

    public function download(string $name)
    {
        $path = $this->backupDir().'/'.$name;

        if (! str_starts_with($name, 'backup-') || ! str_ends_with($name, '.sql.gz') || ! file_exists($path)) {
            abort(404);
        }

        return response()->download($path, $name);
    }

    public function destroy(string $name)
    {
        $path = $this->backupDir().'/'.$name;

        if (! str_starts_with($name, 'backup-') || ! str_ends_with($name, '.sql.gz') || ! file_exists($path)) {
            abort(404);
        }

        unlink($path);

        LogHelper::log('DELETE_BACKUP', "Menghapus backup database {$name}");

        return back()->with('success', 'Backup berhasil dihapus.');
    }

    private function backupDir(): string
    {
        $dir = storage_path('app/backups');

        if (! is_dir($dir)) {
            mkdir($dir, 0775, true);
        }

        return $dir;
    }
}
