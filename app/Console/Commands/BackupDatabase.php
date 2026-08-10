<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Symfony\Component\Process\Process;
use Throwable;

class BackupDatabase extends Command
{
    protected $signature = 'database:backup
        {--keep=30 : Jumlah backup yang disimpan (default 30)}';

    protected $description = 'Membuat backup database dan menyimpannya ke storage/app/backups';

    public function handle(): int
    {
        $keep = max(1, (int) $this->option('keep'));
        $dir = storage_path('app/backups');

        if (! is_dir($dir)) {
            mkdir($dir, 0775, true);
        }

        $this->info('Membuat backup database...');
        $filename = 'backup-'.now()->format('Y-m-d_H-i-s');

        try {
            $path = $this->dump($dir, $filename);
        } catch (Throwable $e) {
            $this->error('Gagal membuat backup: '.$e->getMessage());

            return self::FAILURE;
        }

        $size = round(filesize($path) / 1024 / 1024, 2);
        $this->info("Backup berhasil: {$path} ({$size} MB)");

        $this->prune($dir, $keep);

        return self::SUCCESS;
    }

    private function dump(string $dir, string $filename): string
    {
        $driver = Config::get('database.default');

        if ($driver === 'sqlite') {
            return $this->dumpSqlite($dir, $filename);
        }

        return $this->dumpMysql($dir, $filename);
    }

    private function dumpSqlite(string $dir, string $filename): string
    {
        $dbPath = Config::get('database.connections.sqlite.database');
        $raw = "{$dir}/{$filename}.sqlite";
        $target = "{$dir}/{$filename}.sqlite.gz";

        $process = new Process([
            'sqlite3', $dbPath, ".backup '{$raw}'",
        ]);
        $process->setTimeout(300);
        $process->run();

        if (! $process->isSuccessful()) {
            throw new \RuntimeException('Gagal menjalankan sqlite3: '.$process->getErrorOutput());
        }

        $this->gzip($raw, $target);

        return $target;
    }

    private function dumpMysql(string $dir, string $filename): string
    {
        $config = Config::get('database.connections.mysql');
        $target = "{$dir}/{$filename}.sql.gz";

        $command = array_values(array_filter([
            'mysqldump',
            $config['host'] ? '--host='.$config['host'] : null,
            $config['port'] ? '--port='.$config['port'] : null,
            $config['username'] ? '--user='.$config['username'] : null,
            $config['password'] !== null ? '--password='.$config['password'] : null,
            '--single-transaction',
            '--routines',
            '--triggers',
            $config['database'],
        ]));

        $process = new Process($command);
        $process->setTimeout(600);
        $process->run();

        if (! $process->isSuccessful()) {
            throw new \RuntimeException('Gagal menjalankan mysqldump: '.$process->getErrorOutput());
        }

        $raw = "{$dir}/{$filename}.sql";
        file_put_contents($raw, $process->getOutput());

        $this->gzip($raw, $target);

        return $target;
    }

    private function gzip(string $source, string $target): void
    {
        $handle = gzopen($target, 'wb9');
        $input = fopen($source, 'rb');

        while (! feof($input)) {
            gzwrite($handle, fread($input, 8192));
        }

        fclose($input);
        gzclose($handle);
        unlink($source);
    }

    private function prune(string $dir, int $keep): void
    {
        $files = glob("{$dir}/backup-*.sql.gz") ?: [];
        usort($files, fn ($a, $b) => filemtime($b) <=> filemtime($a));

        foreach (array_slice($files, $keep) as $file) {
            unlink($file);
            $this->line('Menghapus backup lama: '.basename($file));
        }
    }
}
