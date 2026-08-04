<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class MigrateFilesToCloud extends Command
{
    protected $signature = 'files:to-cloud
        {--disk= : Disk tujuan (default: dari FILESYSTEM_DISK)}
        {--delete : Hapus file sumber lokal setelah berhasil diunggah}';

    protected $description = 'Salin semua file dari storage/app/public ke disk tujuan (mis. R2/S3)';

    public function handle(): int
    {
        $source = Storage::disk('public');
        $target = Storage::disk($this->option('disk') ?: config('filesystems.default'));

        $files = $source->allFiles();
        $total = count($files);
        $copied = 0;
        $skipped = 0;

        if ($total === 0) {
            $this->info('Tidak ada file untuk dimigrasi.');

            return self::SUCCESS;
        }

        $this->info('Disk tujuan = '.config('filesystems.default'));
        $this->info("Menemukan {$total} file, tujuan = ".class_basename(get_class($target)));
        $this->newLine();

        $bar = $this->output->createProgressBar($total);
        $bar->start();

        foreach ($files as $file) {
            $basename = basename($file);

            if (str_starts_with($basename, '.')) {
                $bar->advance();
                continue;
            }

            if ($target->exists($file)) {
                $skipped++;
                $bar->advance();
                continue;
            }

            $target->put($file, $source->get($file));

            if ($this->option('delete')) {
                $source->delete($file);
            }

            $copied++;
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->info("Selesai: {$copied} disalin, {$skipped} sudah ada, {$total} total.");

        return self::SUCCESS;
    }
}