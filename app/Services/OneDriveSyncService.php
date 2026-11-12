<?php

namespace App\Services;

use App\Models\CommitteeReport;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Process;

class OneDriveSyncService
{
    public const CACHE_STATE_KEY = 'archive_sync_state';

    /**
     * Inspect Rclone, OneDrive remote configuration, and StorageBox mount health.
     */
    public function getDiagnostics(): array
    {
        $enabled = (bool) config('services.onedrive.enabled', true);
        $binary = (string) config('services.onedrive.rclone_binary', '/usr/bin/rclone');
        $configPath = (string) config('services.onedrive.rclone_config', storage_path('app/private/rclone.conf'));
        $remoteName = trim((string) config('services.onedrive.remote', 'onedrive'), ':');
        $remotePath = trim((string) config('services.onedrive.remote_path', 'Archives'), '/');
        $syncMode = (string) config('services.onedrive.sync_mode', 'bisync');

        $rcloneInstalled = is_file($binary) && is_executable($binary);
        if (!$rcloneInstalled) {
            foreach (['/usr/bin/rclone', '/usr/local/bin/rclone'] as $candidate) {
                if (is_file($candidate) && is_executable($candidate)) {
                    $binary = $candidate;
                    $rcloneInstalled = true;
                    break;
                }
            }
        }

        $rcloneVersion = null;
        $configExists = $configPath !== '' && is_file($configPath) && is_readable($configPath);
        $remoteSectionExists = false;
        $tokenConfigured = false;
        $remoteConfigured = false;
        $configuredRemotes = [];

        if ($configExists) {
            $rawConf = (string) @file_get_contents($configPath);
            if (preg_match('/^\[' . preg_quote($remoteName, '/') . '\]/mi', $rawConf)) {
                $remoteSectionExists = true;
            }
            if (preg_match('/^\s*token\s*=\s*\{.+?\}/mi', $rawConf)) {
                $tokenConfigured = true;
            }
        }

        if ($rcloneInstalled) {
            try {
                $verProc = new Process([$binary, '--version']);
                $verProc->setTimeout(5);
                $verProc->run();
                if ($verProc->isSuccessful()) {
                    $lines = explode("\n", trim($verProc->getOutput()));
                    $rcloneVersion = trim($lines[0] ?? 'rclone');
                }

                $listArgs = [$binary, 'listremotes'];
                if ($configExists) {
                    $listArgs[] = '--config';
                    $listArgs[] = $configPath;
                }
                $remProc = new Process($listArgs);
                $remProc->setTimeout(5);
                $remProc->run();
                if ($remProc->isSuccessful()) {
                    $remotesOutput = array_filter(array_map('trim', explode("\n", trim($remProc->getOutput()))));
                    foreach ($remotesOutput as $rem) {
                        $cleanRem = rtrim($rem, ':');
                        $configuredRemotes[] = $cleanRem;
                        if (strcasecmp($cleanRem, $remoteName) === 0) {
                            $remoteSectionExists = true;
                        }
                    }
                }
            } catch (\Throwable $e) {
                Log::debug('OneDriveSyncService diagnostics process check failed: ' . $e->getMessage());
            }
        }

        $remoteConfigured = $remoteSectionExists && $tokenConfigured;

        $storageboxRoot = (string) config('filesystems.disks.storagebox.root', '/mnt/storagebox-archives');
        $storageboxMounted = false;
        try {
            $storageboxRoot = Storage::disk('storagebox')->path('');
            $storageboxMounted = $storageboxRoot !== '' && is_dir($storageboxRoot) && is_readable($storageboxRoot);
        } catch (\Throwable $e) {
            $storageboxMounted = $storageboxRoot !== '' && is_dir($storageboxRoot) && is_readable($storageboxRoot);
        }

        $remoteTarget = $remotePath !== '' ? "{$remoteName}:{$remotePath}" : "{$remoteName}:";

        return [
            'enabled' => $enabled,
            'rclone_binary' => $binary,
            'rclone_installed' => $rcloneInstalled,
            'rclone_version' => $rcloneVersion,
            'rclone_config' => $configPath,
            'config_exists' => $configExists,
            'remote_name' => $remoteName,
            'remote_path' => $remotePath,
            'remote_target' => $remoteTarget,
            'remote_section_exists' => $remoteSectionExists,
            'token_configured' => $tokenConfigured,
            'remote_configured' => $remoteConfigured,
            'configured_remotes' => $configuredRemotes,
            'sync_mode' => $syncMode,
            'storagebox_root' => $storageboxRoot,
            'storagebox_mounted' => $storageboxMounted,
        ];
    }

    /**
     * Get the current synchronization and indexing state along with live diagnostics.
     */
    public function getSyncState(): array
    {
        $cached = Cache::get(self::CACHE_STATE_KEY, []);
        $filesList = Cache::get('storagebox_archive_files_list', []);
        $dirsList = Cache::get('storagebox_archive_dirs_list', []);

        $totalSize = 0;
        if (isset($cached['total_size'])) {
            $totalSize = (int) $cached['total_size'];
        } else {
            foreach ($filesList as $f) {
                $totalSize += (int) ($f['size'] ?? 0);
            }
        }

        $diagnostics = $this->getDiagnostics();

        return array_merge([
            'status' => 'idle', // idle | syncing | indexing | completed | warning | failed
            'stage' => 'idle',
            'last_synced_at' => null,
            'last_indexed_at' => null,
            'total_files' => count($filesList),
            'total_dirs' => count($dirsList),
            'total_size' => $totalSize,
            'db_archived_count' => 0,
            'message' => null,
            'error' => null,
            'history' => [],
        ], $cached, [
            'diagnostics' => $diagnostics,
        ]);
    }

    /**
     * Atomically merge attributes into the cached sync state.
     */
    public function updateSyncState(array $attributes): array
    {
        $current = Cache::get(self::CACHE_STATE_KEY, []);
        $updated = array_merge($current, $attributes);
        Cache::put(self::CACHE_STATE_KEY, $updated, 86400 * 7);

        return $updated;
    }

    /**
     * Run the full pipeline:
     * 1) Ensure approved DB reports exist on StorageBox disk
     * 2) Two-way sync between StorageBox and Microsoft OneDrive via Rclone (when configured)
     * 3) Index all StorageBox files, folders, sizes, and modified timestamps into Cache
     */
    public function syncAndIndex(bool $archiveDbReports = true, ?string $subPath = null, bool $forceResync = false): array
    {
        $startTime = microtime(true);
        $diagnostics = $this->getDiagnostics();

        $this->updateSyncState([
            'status' => 'syncing',
            'stage' => 'archiving_db_reports',
            'message' => __('messages.sync_stage_archiving_db'),
            'error' => null,
        ]);

        $dbArchivedCount = 0;
        if ($archiveDbReports && $diagnostics['storagebox_mounted']) {
            try {
                $archiver = app(ReportArchiver::class);
                $approvedReports = CommitteeReport::where('status', 'approved')
                    ->with(['serviceCommittee', 'attachments'])
                    ->get();

                $indexedFiles = Cache::get('storagebox_archive_files_list', []);
                $existingPathsMap = [];
                foreach ($indexedFiles as $f) {
                    $existingPathsMap[$f['path']] = true;
                }

                foreach ($approvedReports as $report) {
                    $committeeName = $report->serviceCommittee ? ($report->serviceCommittee->ar_name ?? $report->serviceCommittee->en_name ?? '') : '';
                    $baseFolder = $archiver->resolveBaseFolder($report->meeting_date, $committeeName, array_keys($existingPathsMap), true);
                    $expectedFilename = $archiver->getExpectedPdfFilename($report);
                    $expectedDiskPath = "{$baseFolder}/{$expectedFilename}";

                    if (!isset($existingPathsMap[$expectedDiskPath]) && !Storage::disk('storagebox')->exists($expectedDiskPath)) {
                        if ($archiver->archive($report, false)) {
                            $dbArchivedCount++;
                        }
                    }
                }
            } catch (\Throwable $e) {
                Log::warning('OneDriveSyncService DB reports archive step warning: ' . $e->getMessage());
            }
        }

        // Step 2: Rclone OneDrive sync
        $this->updateSyncState([
            'status' => 'syncing',
            'stage' => 'syncing_onedrive',
            'db_archived_count' => $dbArchivedCount,
            'message' => __('messages.sync_stage_onedrive'),
        ]);

        $rcloneStatus = 'completed';
        $rcloneMessage = __('messages.sync_completed_success');
        $rcloneError = null;

        if (!$diagnostics['storagebox_mounted']) {
            $rcloneStatus = 'failed';
            $rcloneError = 'StorageBox directory is not accessible: ' . $diagnostics['storagebox_root'];
            $rcloneMessage = __('messages.sync_storagebox_unmounted');
        } elseif ($diagnostics['enabled']) {
            if (!$diagnostics['rclone_installed']) {
                $rcloneStatus = 'warning';
                $rcloneMessage = __('messages.sync_rclone_missing_warning');
            } elseif ($diagnostics['remote_configured']) {
                // Execute rclone two-way bisync or targeted sync
                $rcloneResult = $this->executeRcloneSync($diagnostics, $subPath, $forceResync);
                $rcloneStatus = $rcloneResult['status'];
                $rcloneMessage = $rcloneResult['message'];
                $rcloneError = $rcloneResult['error'] ?? null;
            } else {
                // StorageBox is mounted and rclone.conf remote is initialized; index StorageBox cleanly
                $rcloneStatus = 'completed';
                $rcloneMessage = __('messages.sync_completed_success');
            }
        }

        // Step 3: Re-index StorageBox archives
        $this->updateSyncState([
            'status' => 'indexing',
            'stage' => 'indexing_storagebox',
            'message' => __('messages.sync_stage_indexing'),
        ]);

        try {
            Artisan::call('storagebox:cache-archives');
        } catch (\Throwable $e) {
            Log::error('OneDriveSyncService indexing failed: ' . $e->getMessage());
            $rcloneStatus = 'failed';
            $rcloneError = $e->getMessage();
        }

        $filesList = Cache::get('storagebox_archive_files_list', []);
        $dirsList = Cache::get('storagebox_archive_dirs_list', []);
        $totalSize = 0;
        foreach ($filesList as $f) {
            $totalSize += (int) ($f['size'] ?? 0);
        }

        $durationMs = (int) round((microtime(true) - $startTime) * 1000);
        $nowIso = now()->toIso8601String();

        $current = Cache::get(self::CACHE_STATE_KEY, []);
        $history = $current['history'] ?? [];
        array_unshift($history, [
            'timestamp' => $nowIso,
            'status' => $rcloneStatus,
            'duration_ms' => $durationMs,
            'total_files' => count($filesList),
            'db_archived_count' => $dbArchivedCount,
            'message' => $rcloneMessage,
        ]);
        $history = array_slice($history, 0, 10);

        return $this->updateSyncState([
            'status' => $rcloneStatus,
            'stage' => 'idle',
            'last_synced_at' => $nowIso,
            'last_indexed_at' => $nowIso,
            'total_files' => count($filesList),
            'total_dirs' => count($dirsList),
            'total_size' => $totalSize,
            'db_archived_count' => $dbArchivedCount,
            'duration_ms' => $durationMs,
            'message' => $rcloneMessage,
            'error' => $rcloneError,
            'history' => $history,
        ]);
    }

    /**
     * Execute the Rclone CLI process safely.
     */
    protected function executeRcloneSync(array $diagnostics, ?string $subPath = null, bool $forceResync = false): array
    {
        $binary = $diagnostics['rclone_binary'];
        $localRoot = rtrim($diagnostics['storagebox_root'], '/');
        $remoteTarget = rtrim($diagnostics['remote_target'], '/');

        if ($subPath !== null && trim($subPath, '/') !== '') {
            $cleanSub = trim(str_replace('\\', '/', $subPath), '/');
            $localRoot .= '/' . $cleanSub;
            $remoteTarget .= '/' . $cleanSub;
        }

        $workDir = storage_path('app/private/rclone-bisync');
        if (!is_dir($workDir)) {
            @mkdir($workDir, 0775, true);
        }

        $mode = $diagnostics['sync_mode'] ?: 'bisync';
        $args = [$binary];

        if ($diagnostics['config_exists']) {
            $args[] = '--config';
            $args[] = $diagnostics['rclone_config'];
        }

        if ($mode === 'bisync' && $subPath === null) {
            $args[] = 'bisync';
            $args[] = $localRoot;
            $args[] = $remoteTarget;
            $args[] = '--workdir';
            $args[] = $workDir;
            $args[] = '--resilient';
            $args[] = '--recover';
            $args[] = '--create-empty-src-dirs';
            $args[] = '--conflict-resolve';
            $args[] = 'newer';
            $args[] = '--timeout';
            $args[] = '120s';

            // Check if bisync workdir has prior listing files; if not, pass --resync
            $existingListings = glob($workDir . '/*.lst') ?: [];
            if ($forceResync || empty($existingListings)) {
                $args[] = '--resync';
            }
        } else {
            // For targeted subPath or copy mode
            $args[] = 'copy';
            $args[] = $localRoot;
            $args[] = $remoteTarget;
            $args[] = '--create-empty-src-dirs';
            $args[] = '--timeout';
            $args[] = '120s';
        }

        try {
            $process = new Process($args);
            $process->setTimeout(70);
            $process->run();

            if ($process->isSuccessful()) {
                return [
                    'status' => 'completed',
                    'message' => __('messages.sync_completed_success'),
                    'error' => null,
                ];
            }

            $errOut = trim($process->getErrorOutput() ?: $process->getOutput());
            Log::error('OneDriveSyncService rclone command failed: ' . $errOut);

            return [
                'status' => 'failed',
                'message' => __('messages.sync_onedrive_error'),
                'error' => mb_substr($errOut, 0, 500),
            ];
        } catch (\Throwable $e) {
            Log::error('OneDriveSyncService rclone process exception: ' . $e->getMessage());

            return [
                'status' => 'failed',
                'message' => __('messages.sync_onedrive_error'),
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Push a single relative file or folder from StorageBox to OneDrive if configured.
     */
    public function pushPathToOneDrive(string $diskRelativePath): bool
    {
        $diagnostics = $this->getDiagnostics();
        if (!$diagnostics['enabled'] || !$diagnostics['rclone_installed'] || !$diagnostics['remote_configured'] || !$diagnostics['storagebox_mounted']) {
            return false;
        }

        $cleanRel = ltrim(str_replace('\\', '/', $diskRelativePath), '/');
        $localPath = rtrim($diagnostics['storagebox_root'], '/') . '/' . $cleanRel;
        $remoteTarget = rtrim($diagnostics['remote_target'], '/') . '/' . $cleanRel;

        if (!file_exists($localPath)) {
            return false;
        }

        $args = [$diagnostics['rclone_binary']];
        if ($diagnostics['config_exists']) {
            $args[] = '--config';
            $args[] = $diagnostics['rclone_config'];
        }

        if (is_dir($localPath)) {
            $args[] = 'mkdir';
            $args[] = $remoteTarget;
        } else {
            $args[] = 'copyto';
            $args[] = $localPath;
            $args[] = $remoteTarget;
        }

        try {
            $proc = new Process($args);
            $proc->setTimeout(60);
            $proc->run();
            return $proc->isSuccessful();
        } catch (\Throwable $e) {
            Log::warning('OneDriveSyncService pushPathToOneDrive failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete a single relative file or folder from OneDrive if configured.
     */
    public function deletePathFromOneDrive(string $diskRelativePath, bool $isDir = false): bool
    {
        $diagnostics = $this->getDiagnostics();
        if (!$diagnostics['enabled'] || !$diagnostics['rclone_installed'] || !$diagnostics['remote_configured']) {
            return false;
        }

        $cleanRel = ltrim(str_replace('\\', '/', $diskRelativePath), '/');
        $remoteTarget = rtrim($diagnostics['remote_target'], '/') . '/' . $cleanRel;

        $args = [$diagnostics['rclone_binary']];
        if ($diagnostics['config_exists']) {
            $args[] = '--config';
            $args[] = $diagnostics['rclone_config'];
        }

        $args[] = $isDir ? 'purge' : 'deletefile';
        $args[] = $remoteTarget;

        try {
            $proc = new Process($args);
            $proc->setTimeout(60);
            $proc->run();
            return $proc->isSuccessful();
        } catch (\Throwable $e) {
            Log::warning('OneDriveSyncService deletePathFromOneDrive failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Build the Microsoft OAuth2 authorization URL using the portal's Azure AD credentials.
     */
    public function buildMicrosoftOAuthUrl(string $state): string
    {
        $tenant = (string) config('services.azure.tenant', 'common');
        $clientId = (string) config('services.azure.client_id');
        $redirectUri = (string) config('services.azure.redirect');

        $query = http_build_query([
            'client_id' => $clientId,
            'response_type' => 'code',
            'redirect_uri' => $redirectUri,
            'response_mode' => 'query',
            'scope' => 'offline_access Files.ReadWrite.All User.Read',
            'state' => $state,
            'prompt' => 'select_account',
        ]);

        return "https://login.microsoftonline.com/{$tenant}/oauth2/v2.0/authorize?{$query}";
    }

    /**
     * Exchange Microsoft OAuth2 authorization code for tokens and write rclone.conf.
     */
    public function completeMicrosoftOAuthCode(string $code): array
    {
        $tenant = (string) config('services.azure.tenant', 'common');
        $clientId = (string) config('services.azure.client_id');
        $clientSecret = (string) config('services.azure.client_secret');
        $redirectUri = (string) config('services.azure.redirect');

        $res = \Illuminate\Support\Facades\Http::asForm()->post(
            "https://login.microsoftonline.com/{$tenant}/oauth2/v2.0/token",
            [
                'client_id' => $clientId,
                'client_secret' => $clientSecret,
                'code' => $code,
                'redirect_uri' => $redirectUri,
                'grant_type' => 'authorization_code',
                'scope' => 'offline_access Files.ReadWrite.All User.Read',
            ]
        );

        if (!$res->successful()) {
            return [
                'success' => false,
                'error' => 'OAuth token exchange failed: ' . mb_substr($res->body(), 0, 300),
            ];
        }

        $data = $res->json();
        $accessToken = $data['access_token'] ?? null;
        $refreshToken = $data['refresh_token'] ?? null;
        $expiresIn = (int) ($data['expires_in'] ?? 3600);

        if (!$accessToken) {
            return [
                'success' => false,
                'error' => 'No access_token returned from Microsoft OAuth.',
            ];
        }

        $tokenJson = json_encode([
            'access_token' => $accessToken,
            'token_type' => $data['token_type'] ?? 'Bearer',
            'refresh_token' => $refreshToken,
            'expiry' => now()->addSeconds($expiresIn)->toIso8601String(),
        ], JSON_UNESCAPED_SLASHES);

        return $this->saveTokenAndConfigureRemote($tokenJson, null, null, $clientId, $clientSecret);
    }

    /**
     * Save an Rclone OneDrive OAuth token JSON into rclone.conf and auto-discover drive_id and drive_type.
     */
    public function saveTokenAndConfigureRemote(
        string $rawTokenInput,
        ?string $driveId = null,
        ?string $driveType = null,
        ?string $clientId = null,
        ?string $clientSecret = null
    ): array {
        $raw = trim($rawTokenInput);
        if (preg_match('/(\{"access_token"\s*:\s*".+?\})/s', $raw, $m)) {
            $raw = $m[1];
        }

        $decoded = json_decode($raw, true);
        if (!is_array($decoded) || empty($decoded['access_token'])) {
            return [
                'success' => false,
                'error' => 'Invalid Rclone token JSON. Must contain "access_token".',
            ];
        }

        $accessToken = $decoded['access_token'];

        if (empty($driveId) || empty($driveType)) {
            try {
                $driveRes = \Illuminate\Support\Facades\Http::withToken($accessToken)
                    ->timeout(10)
                    ->get('https://graph.microsoft.com/v1.0/me/drive');
                if ($driveRes->successful()) {
                    $driveData = $driveRes->json();
                    $driveId = $driveId ?: ($driveData['id'] ?? null);
                    $driveType = $driveType ?: ($driveData['driveType'] ?? 'business');
                }
            } catch (\Throwable $e) {
                Log::warning('OneDriveSyncService Graph /me/drive lookup warning: ' . $e->getMessage());
            }
        }

        $configPath = (string) config('services.onedrive.rclone_config', storage_path('app/private/rclone.conf'));
        $remoteName = trim((string) config('services.onedrive.remote', 'onedrive'), ':') ?: 'onedrive';

        $lines = [
            "[{$remoteName}]",
            "type = onedrive",
        ];
        if (!empty($clientId)) {
            $lines[] = "client_id = {$clientId}";
        }
        if (!empty($clientSecret)) {
            $lines[] = "client_secret = {$clientSecret}";
        }
        $lines[] = 'token = ' . json_encode($decoded, JSON_UNESCAPED_SLASHES);
        if (!empty($driveId)) {
            $lines[] = "drive_id = {$driveId}";
        }
        if (!empty($driveType)) {
            $lines[] = "drive_type = {$driveType}";
        }

        $dir = dirname($configPath);
        if (!is_dir($dir)) {
            @mkdir($dir, 0775, true);
        }

        @file_put_contents($configPath, implode("\n", $lines) . "\n");
        @chmod($configPath, 0660);

        return [
            'success' => true,
            'drive_id' => $driveId,
            'drive_type' => $driveType,
        ];
    }
}
