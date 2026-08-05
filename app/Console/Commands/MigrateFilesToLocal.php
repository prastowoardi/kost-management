<?php

namespace App\Console\Commands;

use App\Services\StorageMigrationService;
use Illuminate\Console\Command;

class MigrateFilesToLocal extends Command
{
    protected $signature = 'files:to-local
        {--delete : Hapus file sumber di R2 setelah berhasil disalin}';

    protected $description = 'Salin semua file dari Cloudflare R2 (s3) ke storage/app/public lokal';

    public function handle(StorageMigrationService $service): int
    {
        $this->info('Disk sumber = s3 (Cloudflare R2), tujuan = public (lokal)');

        $result = $service->migrate('s3', 'public', (bool) $this->option('delete'));

        if ($result['total'] === 0) {
            $this->info('Tidak ada file untuk dimigrasi.');

            return self::SUCCESS;
        }

        $this->info(sprintf(
            'Selesai: %d disalin, %d sudah ada, %d dihapus, %d total.',
            $result['copied'],
            $result['skipped'],
            $result['deleted'],
            $result['total']
        ));

        return self::SUCCESS;
    }
}