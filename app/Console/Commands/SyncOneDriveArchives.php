<?php

namespace App\Console\Commands;

use App\Services\OneDriveSyncService;
use Illuminate\Console\Command;

class SyncOneDriveArchives extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'onedrive:sync-archives
                            {--resync : Force initial rclone bisync resync}
                            {--force-resync : Alias for --resync to force initial rclone bisync resync}
                            {--skip-db : Skip archiving unarchived approved DB reports before syncing}
                            {--path= : Optional relative subpath to sync}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronize StorageBox archives with Microsoft OneDrive via Rclone and re-index all files';

    /**
     * Execute the console command.
     */
    public function handle(OneDriveSyncService $syncService): int
    {
        $this->info('Starting OneDrive & StorageBox Archive Synchronization...');

        $diagnostics = $syncService->getDiagnostics();
        $this->line(sprintf(
            'Diagnostics: Rclone=%s | RemoteConfigured=%s (%s) | StorageBoxMounted=%s',
            $diagnostics['rclone_installed'] ? ($diagnostics['rclone_version'] ?? 'installed') : 'missing',
            $diagnostics['remote_configured'] ? 'yes' : 'no',
            $diagnostics['remote_target'],
            $diagnostics['storagebox_mounted'] ? 'yes' : 'no'
        ));

        $archiveDb = !$this->option('skip-db');
        $forceResync = (bool) ($this->option('resync') || $this->option('force-resync'));
        $subPath = $this->option('path') ? (string) $this->option('path') : null;

        $state = $syncService->syncAndIndex($archiveDb, $subPath, $forceResync);

        $this->info(sprintf(
            'Sync finished with status [%s]: %d files, %d folders indexed (%s ms).',
            $state['status'],
            $state['total_files'] ?? 0,
            $state['total_dirs'] ?? 0,
            $state['duration_ms'] ?? 0
        ));

        if (!empty($state['error'])) {
            $this->warn('Diagnostic Note / Error: ' . $state['error']);
        }

        return $state['status'] === 'failed' ? Command::FAILURE : Command::SUCCESS;
    }
}

