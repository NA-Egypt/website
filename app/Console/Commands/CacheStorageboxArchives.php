<?php

namespace App\Console\Commands;

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
    protected $description = 'Index and cache StorageBox archive files in the background';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Starting StorageBox archive indexing...');

        $storageboxRoot = Storage::disk('storagebox')->path('');
        if (!file_exists($storageboxRoot) || !is_dir($storageboxRoot)) {
            $this->error("StorageBox root directory does not exist: {$storageboxRoot}");
            Log::error("StorageBox root directory does not exist: {$storageboxRoot}");
            return Command::FAILURE;
        }

        $archiveList = [];
        $agendaList = [];

        // Fast path: use linux 'find' command if available for SMB mount efficiency
        $command = 'find ' . escapeshellarg($storageboxRoot) . ' -type f -printf "%P\t%s\n" 2>/dev/null';
        $output = shell_exec($command);

        if ($output !== null && trim($output) !== '') {
            $lines = explode("\n", trim($output));
            foreach ($lines as $line) {
                if (empty($line)) continue;
                $parts = explode("\t", $line);
                if (count($parts) < 2) continue;

                $relativePath = str_replace('\\', '/', $parts[0]);
                $fileName = basename($relativePath);
                $size = (int) $parts[1];

                // Skip hidden files
                if (str_starts_with($fileName, '.') || str_contains($relativePath, '/.')) {
                    continue;
                }

                $item = [
                    'name' => $fileName,
                    'path' => $relativePath,
                    'size' => $size,
                ];

                $archiveList[] = $item;
                $agendaList[] = $item;
            }
        } else {
            // Fallback to PHP iterator if find command returns nothing
            try {
                $iterator = new RecursiveIteratorIterator(
                    new RecursiveDirectoryIterator($storageboxRoot, FilesystemIterator::SKIP_DOTS),
                    RecursiveIteratorIterator::SELF_FIRST
                );

                foreach ($iterator as $fileInfo) {
                    if (!$fileInfo->isFile()) continue;

                    $fullPath = $fileInfo->getPathname();
                    $relativePath = ltrim(substr($fullPath, strlen($storageboxRoot)), '/\\');
                    $relativePath = str_replace('\\', '/', $relativePath);
                    $fileName = $fileInfo->getFilename();

                    if (str_starts_with($fileName, '.') || str_contains($relativePath, '/.')) continue;

                    $item = [
                        'name' => $fileName,
                        'path' => $relativePath,
                        'size' => (int) $fileInfo->getSize(),
                    ];

                    $archiveList[] = $item;
                    $agendaList[] = $item;
                }
            } catch (\Throwable $e) {
                $this->error("Error indexing StorageBox files: " . $e->getMessage());
                Log::error("Error indexing StorageBox files: " . $e->getMessage());
                return Command::FAILURE;
            }
        }

        // Cache for 24 hours (86400 seconds)
        Cache::put('storagebox_archive_files_list', $archiveList, 86400);
        Cache::put('storagebox_agendas_files_list', $agendaList, 86400);

        $this->info("Successfully indexed " . count($archiveList) . " archive files and " . count($agendaList) . " agenda files.");
        Log::info("StorageBox archives indexed: " . count($archiveList) . " archive files, " . count($agendaList) . " agenda files.");

        return Command::SUCCESS;
    }
}
