<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Throwable;

class StorageMigrationService
{
    public const DOTFILE_SKIP = true;

    public function migrate(string $fromDisk, string $toDisk, bool $deleteSource = false): array
    {
        $source = Storage::disk($fromDisk);
        $target = Storage::disk($toDisk);

        if ($fromDisk === $toDisk) {
            throw new \InvalidArgumentException('Disk sumber dan tujuan sama.');
        }

        $files = $source->allFiles();
        $result = ['copied' => 0, 'skipped' => 0, 'deleted' => 0, 'total' => count($files)];

        foreach ($files as $file) {
            if ($this->shouldSkip($file)) {
                continue;
            }

            if (! $target->exists($file)) {
                $target->put($file, $source->get($file));
                $result['copied']++;
            } else {
                $result['skipped']++;
            }

            if ($deleteSource) {
                $source->delete($file);
                $result['deleted']++;
            }
        }

        return $result;
    }

    public function countFiles(string $disk): int
    {
        try {
            return count(array_filter(
                Storage::disk($disk)->allFiles(),
                fn (string $file) => ! $this->shouldSkip($file)
            ));
        } catch (Throwable) {
            return 0;
        }
    }

    private function shouldSkip(string $file): bool
    {
        return str_starts_with(basename($file), '.');
    }
}
