<?php

namespace App\Console\Commands;

use App\Services\OneDriveSyncService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use FilesystemIterator;

class CacheStorageboxArchives extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'storagebox:cache-archives';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Index and cache StorageBox archive files and directories with sizes and modified timestamps';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Starting StorageBox archive indexing...');

        try {
            $storageboxRoot = Storage::disk('storagebox')->path('');
        } catch (\Throwable $e) {
            $storageboxRoot = (string) config('filesystems.disks.storagebox.root', '/mnt/storagebox-archives');
        }

        if ($storageboxRoot === '' || !file_exists($storageboxRoot) || !is_dir($storageboxRoot) || !is_readable($storageboxRoot)) {
            $this->error("StorageBox root directory is not accessible: {$storageboxRoot}");
            Log::warning("StorageBox root directory is not accessible: {$storageboxRoot}");
            return Command::FAILURE;
        }

        $archiveList = [];
        $agendaList = [];
        $dirList = [];
        $totalSize = 0;

        // Fast path: use linux 'find' command if available for SMB/CIFS mount efficiency
        $fileCommand = 'find ' . escapeshellarg($storageboxRoot) . ' -type f -printf "%P\t%s\t%T@\n" 2>/dev/null';
        $fileOutput = shell_exec($fileCommand);

        $dirCommand = 'find ' . escapeshellarg($storageboxRoot) . ' -mindepth 1 -type d -printf "%P\t%T@\n" 2>/dev/null';
        $dirOutput = shell_exec($dirCommand);

        if ($fileOutput !== null && trim($fileOutput) !== '') {
            $lines = explode("\n", trim($fileOutput));
            foreach ($lines as $line) {
                if (empty($line)) {
                    continue;
                }
                $parts = explode("\t", $line);
                if (count($parts) < 2) {
                    continue;
                }

                $relativePath = str_replace('\\', '/', $parts[0]);
                $fileName = basename($relativePath);
                $size = (int) $parts[1];
                $mtime = isset($parts[2]) ? (int) floor((float) $parts[2]) : null;

                // Skip hidden files or rclone internal metadata
                if (str_starts_with($fileName, '.') || str_contains($relativePath, '/.')) {
                    continue;
                }

                $item = [
                    'name' => $fileName,
                    'path' => $relativePath,
                    'size' => $size,
                    'mtime' => $mtime,
                ];

                $archiveList[] = $item;
                $agendaList[] = $item;
                $totalSize += $size;
            }

            if ($dirOutput !== null && trim($dirOutput) !== '') {
                $dirLines = explode("\n", trim($dirOutput));
                foreach ($dirLines as $dLine) {
                    if (empty($dLine)) {
                        continue;
                    }
                    $dParts = explode("\t", $dLine);
                    $relDir = str_replace('\\', '/', trim($dParts[0]));
                    if ($relDir === '' || str_starts_with(basename($relDir), '.') || str_contains($relDir, '/.')) {
                        continue;
                    }
                    $dirList[] = [
                        'name' => basename($relDir),
                        'path' => $relDir,
                        'mtime' => isset($dParts[1]) ? (int) floor((float) $dParts[1]) : null,
                    ];
                }
            }
        } else {
            // Fallback to PHP iterator if find command returns nothing
            try {
                $iterator = new RecursiveIteratorIterator(
                    new RecursiveDirectoryIterator($storageboxRoot, FilesystemIterator::SKIP_DOTS),
                    RecursiveIteratorIterator::SELF_FIRST
                );

                foreach ($iterator as $fileInfo) {
                    $fullPath = $fileInfo->getPathname();
                    $relativePath = ltrim(substr($fullPath, strlen($storageboxRoot)), '/\\');
                    $relativePath = str_replace('\\', '/', $relativePath);
                    $fileName = $fileInfo->getFilename();

                    if ($relativePath === '' || str_starts_with($fileName, '.') || str_contains($relativePath, '/.')) {
                        continue;
                    }

                    $mtime = (int) $fileInfo->getMTime();

                    if ($fileInfo->isDir()) {
                        $dirList[] = [
                            'name' => $fileName,
                            'path' => $relativePath,
                            'mtime' => $mtime,
                        ];
                        continue;
                    }

                    if (!$fileInfo->isFile()) {
                        continue;
                    }

                    $size = (int) $fileInfo->getSize();
                    $item = [
                        'name' => $fileName,
                        'path' => $relativePath,
                        'size' => $size,
                        'mtime' => $mtime,
                    ];

                    $archiveList[] = $item;
                    $agendaList[] = $item;
                    $totalSize += $size;
                }
            } catch (\Throwable $e) {
                $this->error("Error indexing StorageBox files: " . $e->getMessage());
                Log::error("Error indexing StorageBox files: " . $e->getMessage());
                return Command::FAILURE;
            }
        }

        // Cache for 24 hours (86400 seconds)
        Cache::put('storagebox_archive_files_list', $archiveList, 86400);
        Cache::put('storagebox_archive_dirs_list', $dirList, 86400);
        Cache::put('storagebox_agendas_files_list', $agendaList, 86400);

        // Update sync state metrics
        $currentSyncState = Cache::get(OneDriveSyncService::CACHE_STATE_KEY, []);
        $nowIso = now()->toIso8601String();
        Cache::put(OneDriveSyncService::CACHE_STATE_KEY, array_merge($currentSyncState, [
            'last_indexed_at' => $nowIso,
            'last_synced_at' => $currentSyncState['last_synced_at'] ?? $nowIso,
            'total_files' => count($archiveList),
            'total_dirs' => count($dirList),
            'total_size' => $totalSize,
        ]), 86400 * 7);

        $this->info("Successfully indexed " . count($archiveList) . " archive files and " . count($dirList) . " directories.");
        Log::info("StorageBox archives indexed: " . count($archiveList) . " archive files, " . count($dirList) . " directories.");

        return Command::SUCCESS;
    }
}
