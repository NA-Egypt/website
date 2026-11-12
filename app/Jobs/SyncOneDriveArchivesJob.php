<?php

namespace App\Jobs;

use App\Services\OneDriveSyncService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SyncOneDriveArchivesJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 1;
    public int $timeout = 85;
    public int $uniqueFor = 120;

    public function __construct(
        public ?string $subPath = null,
        public bool $archiveDbReports = true,
        public bool $forceResync = false
    ) {
    }

    public function uniqueId(): string
    {
        return 'onedrive_archive_sync_' . ($this->subPath ?: 'root');
    }

    public function handle(OneDriveSyncService $syncService): void
    {
        $syncService->syncAndIndex($this->archiveDbReports, $this->subPath, $this->forceResync);
    }
}
