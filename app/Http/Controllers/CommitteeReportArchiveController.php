<?php

namespace App\Http\Controllers;

use App\Models\CommitteeReport;
use App\Models\ServiceCommittee;
use App\Services\OneDriveSyncService;
use App\Services\ReportArchiver;
use App\Traits\ResolvesCommitteeReportAccess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CommitteeReportArchiveController extends Controller
{
    use ResolvesCommitteeReportAccess;

    public function archive(Request $request)
    {
        $archiver = app(ReportArchiver::class);
        $syncService = app(OneDriveSyncService::class);
        $canManageArchive = $this->isRsc();

        // 1. Get database reports matching user visibility & filters
        $query = CommitteeReport::with(['serviceCommittee', 'attachments']);
        $now = now();
        $user = auth()->user();

        if (! $this->isRsc()) {
            $committee = $this->getServiceCommittee();
            $committeeId = $committee ? $committee->id : null;
            $query->where(function ($q) use ($committeeId, $now, $user) {
                if ($committeeId) {
                    $q->where('service_committee_id', $committeeId);
                }
                if ($this->isRestrictedConsumer($user)) {
                    $q->orWhere(function ($sub) use ($now) {
                        $sub->where('status', 'approved');
                        $threshold = $now->day >= 10 ? $now : $now->copy()->subMonth();
                        $sub->where(function ($dateQ) use ($threshold) {
                            $dateQ->whereYear('meeting_date', '<', $threshold->year)
                                ->orWhere(function ($inner) use ($threshold) {
                                    $inner->whereYear('meeting_date', $threshold->year)
                                        ->whereMonth('meeting_date', '<', $threshold->month);
                                });
                        });
                    });
                } else {
                    $q->orWhereIn('status', ['submitted', 'approved']);
                }
            });
        } else {
            if ($user && $user->hasRole('super admin')) {
                $query->whereIn('status', ['draft', 'submitted', 'approved']);
            } else {
                $query->whereIn('status', ['submitted', 'approved']);
            }
        }

        $hasSearch = $request->filled('search');
        $hasCommittee = $request->filled('committee_id');
        $hasStartDate = $request->filled('start_date');
        $hasEndDate = $request->filled('end_date');
        $hasExceptional = $request->input('exceptional') == '1';
        $isFilterActive = $hasSearch || $hasCommittee || $hasStartDate || $hasEndDate || $hasExceptional;

        if ($hasSearch) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('meeting_day_description', 'like', "%$search%")
                    ->orWhere('body', 'like', "%$search%");
            });
        }

        if ($hasCommittee) {
            $query->where('service_committee_id', $request->committee_id);
        }

        if ($hasStartDate) {
            $query->where('meeting_date', '>=', $request->start_date);
        }

        if ($hasEndDate) {
            $query->where('meeting_date', '<=', $request->end_date);
        }

        if ($hasExceptional) {
            $query->where('is_exceptional', true);
        }

        $dbReports = $query->orderBy('meeting_date', 'desc')->get();

        // 2. Load indexed files and directories from StorageBox cache
        $cacheKey = 'storagebox_archive_files_list';
        $dirsCacheKey = 'storagebox_archive_dirs_list';

        if ($request->query('refresh') == '1') {
            exec('php '.escapeshellarg(base_path('artisan')).' onedrive:sync-archives > /dev/null 2>&1 &');
            session()->flash('info', __('messages.archive_refresh_started'));
        }

        $allStorageboxFiles = Cache::get($cacheKey);
        if ($allStorageboxFiles === null) {
            exec('php '.escapeshellarg(base_path('artisan')).' storagebox:cache-archives > /dev/null 2>&1 &');
            $allStorageboxFiles = [];
        }
        $allStorageboxDirs = Cache::get($dirsCacheKey, []);

        // Build lookup maps of all physical files before filtering so DB reports can deduplicate accurately
        $allPhysicalByTreePath = [];
        $allPhysicalByFilename = [];
        $allIndexedPaths = [];

        foreach ($allStorageboxDirs as $dirInfo) {
            $dPath = is_array($dirInfo) ? ($dirInfo['path'] ?? '') : (string) $dirInfo;
            if ($dPath !== '') {
                $allIndexedPaths[] = $dPath;
            }
        }

        foreach ($allStorageboxFiles as $fileInfo) {
            $rawPath = $fileInfo['path'] ?? '';
            if ($rawPath === '') {
                continue;
            }
            $allIndexedPaths[] = $rawPath;
            $treePath = $archiver->toTreePath($rawPath);
            $diskRelPath = $archiver->toDiskRelativePath($rawPath);

            $entry = [
                'is_dir' => false,
                'name' => $fileInfo['name'],
                'path' => $treePath,
                'encrypted_path' => Crypt::encryptString($diskRelPath),
                'size' => (int) ($fileInfo['size'] ?? 0),
                'mtime' => $fileInfo['mtime'] ?? null,
                'source' => 'storagebox',
            ];

            $allPhysicalByTreePath[$treePath] = $entry;
            $allPhysicalByFilename[$fileInfo['name']][] = $treePath;
        }

        // If user is a restricted consumer (ServiceBody / GSR), identify restricted current-month report filenames to hide
        $restrictedFilenames = [];
        if (! $this->isRsc() && $this->isRestrictedConsumer($user)) {
            $threshold = $now->day >= 10 ? $now : $now->copy()->subMonth();
            $restrictedReports = CommitteeReport::with('serviceCommittee')
                ->where('status', 'approved')
                ->where(function ($q) use ($threshold) {
                    $q->whereYear('meeting_date', '>', $threshold->year)
                        ->orWhere(function ($sub) use ($threshold) {
                            $sub->whereYear('meeting_date', $threshold->year)
                                ->whereMonth('meeting_date', '>=', $threshold->month);
                        });
                })
                ->get();

            foreach ($restrictedReports as $rReport) {
                $restrictedFilenames[$archiver->getExpectedPdfFilename($rReport)] = true;
            }
        }

        $committees = ServiceCommittee::orderBy('ar_name')->get();
        $filesByPath = [];

        // Filter standalone physical files (skip standalone physical files when filtering strictly by exceptional DB flag)
        if (! $hasExceptional) {
            foreach ($allPhysicalByTreePath as $treePath => $entry) {
                $fileName = $entry['name'];

                if (isset($restrictedFilenames[$fileName])) {
                    continue;
                }

                // Committee filter for storagebox files
                if ($hasCommittee) {
                    $selectedCommittee = $committees->firstWhere('id', $request->committee_id);
                    if ($selectedCommittee) {
                        $normAr = $selectedCommittee->ar_name ? $archiver->normalizeArabicForMatch($selectedCommittee->ar_name) : null;
                        $normEn = $selectedCommittee->en_name ? mb_strtolower($selectedCommittee->en_name, 'UTF-8') : null;
                        $normPath = $archiver->normalizeArabicForMatch($treePath);
                        $normName = $archiver->normalizeArabicForMatch($fileName);

                        $match = false;
                        if ($normAr && (str_contains($normPath, $normAr) || str_contains($normName, $normAr))) {
                            $match = true;
                        }
                        if ($normEn && (str_contains(mb_strtolower($treePath, 'UTF-8'), $normEn) || str_contains(mb_strtolower($fileName, 'UTF-8'), $normEn))) {
                            $match = true;
                        }
                        if (! $match) {
                            continue;
                        }
                    }
                }

                // Search filter
                if ($hasSearch) {
                    $normSearch = $archiver->normalizeArabicForMatch($request->search);
                    $normFile = $archiver->normalizeArabicForMatch($fileName);
                    $normPath = $archiver->normalizeArabicForMatch($treePath);
                    if (! str_contains($normFile, $normSearch) && ! str_contains($normPath, $normSearch)) {
                        continue;
                    }
                }

                // Extract year for date filtering (supporting both Western and Eastern numerals)
                $westernPath = $archiver->toWesternNumerals($treePath);
                $fileYear = null;
                if (preg_match('/\b([12]\d{3})\b/', $westernPath, $yearMatches)) {
                    $fileYear = (int) $yearMatches[1];
                }

                if ($hasStartDate) {
                    $startDateYear = (int) date('Y', strtotime($request->start_date));
                    if ($fileYear && $fileYear < $startDateYear) {
                        continue;
                    }
                }
                if ($hasEndDate) {
                    $endDateYear = (int) date('Y', strtotime($request->end_date));
                    if ($fileYear && $fileYear > $endDateYear) {
                        continue;
                    }
                }

                $filesByPath[$treePath] = $entry;
            }
        }

        // 3. Merge & deduplicate database reports with physical files
        $reportsByCommitteeMonth = [];
        foreach ($dbReports as $rep) {
            if ($rep->status === 'approved' && $rep->meeting_date) {
                $ymKey = $rep->service_committee_id.'_'.$rep->meeting_date->format('Y_m');
                $reportsByCommitteeMonth[$ymKey][] = $rep->id;
            }
        }
        foreach ($reportsByCommitteeMonth as $ymKey => $ids) {
            sort($ids);
            $reportsByCommitteeMonth[$ymKey] = $ids;
        }

        foreach ($dbReports as $report) {
            $committeeName = $report->serviceCommittee ? ($report->serviceCommittee->ar_name ?? $report->serviceCommittee->en_name ?? '') : '';
            $baseFolder = $archiver->resolveBaseFolder($report->meeting_date, $committeeName, $allIndexedPaths, false);
            $dateStr = $report->meeting_date ? $report->meeting_date->format('Y-m-d') : $report->created_at->format('Y-m-d');

            $ymKey = $report->service_committee_id.'_'.($report->meeting_date ? $report->meeting_date->format('Y_m') : '');
            $monthIds = $reportsByCommitteeMonth[$ymKey] ?? null;
            $expectedPdfFilename = $archiver->getExpectedPdfFilename($report, $monthIds);
            $expectedTreePath = "{$baseFolder}/{$expectedPdfFilename}";

            // Check if physical file exists at expected path or by unique expected filename
            $matchedPhysicalPath = null;
            if (isset($allPhysicalByTreePath[$expectedTreePath])) {
                $matchedPhysicalPath = $expectedTreePath;
            } elseif (! empty($allPhysicalByFilename[$expectedPdfFilename])) {
                $matchedPhysicalPath = $allPhysicalByFilename[$expectedPdfFilename][0];
            }

            $reportDisplayTitle = ($report->serviceCommittee->ar_name ?? $report->serviceCommittee->en_name ?? 'Committee Report')
                .' - '.$dateStr.' ('.$report->meeting_day_description.').pdf';

            if ($matchedPhysicalPath !== null) {
                $physicalEntry = $filesByPath[$matchedPhysicalPath] ?? $allPhysicalByTreePath[$matchedPhysicalPath];
                $physicalEntry['db_report_id'] = $report->id;
                $physicalEntry['report_status'] = $report->status;
                $physicalEntry['meeting_date'] = $dateStr;
                $physicalEntry['meeting_day_description'] = $report->meeting_day_description;
                $physicalEntry['report_title'] = $reportDisplayTitle;
                $physicalEntry['committee_name'] = $committeeName;
                $physicalEntry['is_exceptional'] = (bool) $report->is_exceptional;
                $physicalEntry['source'] = 'synced';
                $filesByPath[$matchedPhysicalPath] = $physicalEntry;
                $resolvedReportFolder = dirname($matchedPhysicalPath);
            } else {
                $virtualFilename = sprintf('report_%d_%s.pdf', $report->id, $dateStr);
                $virtualPath = "{$baseFolder}/{$virtualFilename}";

                $filesByPath[$virtualPath] = [
                    'is_dir' => false,
                    'name' => $reportDisplayTitle,
                    'path' => $virtualPath,
                    'db_report_id' => $report->id,
                    'report_status' => $report->status,
                    'meeting_date' => $dateStr,
                    'meeting_day_description' => $report->meeting_day_description,
                    'report_title' => $reportDisplayTitle,
                    'committee_name' => $committeeName,
                    'is_exceptional' => (bool) $report->is_exceptional,
                    'source' => 'db',
                    'size' => 0,
                    'mtime' => $report->updated_at ? $report->updated_at->timestamp : null,
                ];
                $resolvedReportFolder = $baseFolder;
            }

            // Merge & deduplicate report attachments
            foreach ($report->attachments as $attachment) {
                $originalName = basename($attachment->original_name);
                $attFilename = sprintf('attachment_%d_%s', $attachment->id, $originalName);
                $attExpectedPath = "{$resolvedReportFolder}/المرفقات/{$attFilename}";

                $matchedAttPath = null;
                if (isset($allPhysicalByTreePath[$attExpectedPath])) {
                    $matchedAttPath = $attExpectedPath;
                } elseif (! empty($allPhysicalByFilename[$attFilename])) {
                    $matchedAttPath = $allPhysicalByFilename[$attFilename][0];
                }

                if ($matchedAttPath !== null) {
                    $attEntry = $filesByPath[$matchedAttPath] ?? $allPhysicalByTreePath[$matchedAttPath];
                    $attEntry['db_attachment_id'] = $attachment->id;
                    $attEntry['db_report_id'] = $report->id;
                    $attEntry['source'] = 'synced';
                    $filesByPath[$matchedAttPath] = $attEntry;
                } else {
                    $filesByPath[$attExpectedPath] = [
                        'is_dir' => false,
                        'name' => $originalName,
                        'path' => $attExpectedPath,
                        'db_attachment_id' => $attachment->id,
                        'db_report_id' => $report->id,
                        'source' => 'db',
                        'size' => (int) $attachment->file_size,
                        'mtime' => $attachment->updated_at ? $attachment->updated_at->timestamp : null,
                    ];
                }
            }
        }

        // 4. Build structured directory tree matching Archives hierarchy
        $tree = [];

        // Include indexed directories (including empty folders) when no search filter is narrowing results
        if (! $isFilterActive) {
            foreach ($allStorageboxDirs as $dirInfo) {
                $rawDirPath = is_array($dirInfo) ? ($dirInfo['path'] ?? '') : (string) $dirInfo;
                $dirMtime = is_array($dirInfo) ? ($dirInfo['mtime'] ?? null) : null;
                if ($rawDirPath === '') {
                    continue;
                }

                $treeDirPath = $archiver->toTreePath($rawDirPath);
                $parts = explode('/', $treeDirPath);
                if (isset($parts[0]) && $parts[0] === 'Archives') {
                    array_shift($parts);
                }
                if (empty($parts)) {
                    continue;
                }

                $currentLevel = &$tree;
                $accumulatedPath = 'Archives';
                foreach ($parts as $dirName) {
                    if ($dirName === '') {
                        continue;
                    }
                    $accumulatedPath .= '/'.$dirName;
                    if (! isset($currentLevel[$dirName])) {
                        $currentLevel[$dirName] = [
                            'is_dir' => true,
                            'name' => $dirName,
                            'path' => $accumulatedPath,
                            'mtime' => $dirMtime,
                            'children' => [],
                        ];
                    } elseif ($dirMtime && empty($currentLevel[$dirName]['mtime'])) {
                        $currentLevel[$dirName]['mtime'] = $dirMtime;
                    }
                    $currentLevel = &$currentLevel[$dirName]['children'];
                }
                unset($currentLevel);
            }
        }

        foreach ($filesByPath as $file) {
            $parts = explode('/', $file['path']);
            if (isset($parts[0]) && $parts[0] === 'Archives') {
                array_shift($parts);
            }

            if (empty($parts)) {
                continue;
            }

            $currentLevel = &$tree;
            $accumulatedPath = 'Archives';

            for ($i = 0; $i < count($parts) - 1; $i++) {
                $dirName = $parts[$i];
                if ($dirName === '') {
                    continue;
                }
                $accumulatedPath .= '/'.$dirName;
                if (! isset($currentLevel[$dirName])) {
                    $currentLevel[$dirName] = [
                        'is_dir' => true,
                        'name' => $dirName,
                        'path' => $accumulatedPath,
                        'mtime' => null,
                        'children' => [],
                    ];
                }
                $currentLevel = &$currentLevel[$dirName]['children'];
            }

            $fileKey = end($parts);
            $currentLevel[$fileKey] = [
                'is_dir' => false,
                'name' => $file['name'],
                'path' => $file['path'],
                'encrypted_path' => $file['encrypted_path'] ?? null,
                'db_report_id' => $file['db_report_id'] ?? null,
                'db_attachment_id' => $file['db_attachment_id'] ?? null,
                'report_status' => $file['report_status'] ?? null,
                'meeting_date' => $file['meeting_date'] ?? null,
                'meeting_day_description' => $file['meeting_day_description'] ?? null,
                'report_title' => $file['report_title'] ?? null,
                'committee_name' => $file['committee_name'] ?? null,
                'is_exceptional' => $file['is_exceptional'] ?? false,
                'source' => $file['source'] ?? 'storagebox',
                'size' => (int) ($file['size'] ?? 0),
                'mtime' => $file['mtime'] ?? null,
            ];
            unset($currentLevel);
        }

        // Recursively convert associative children arrays to indexed lists, compute recursive counts/sizes, and sort
        $sanitizeTree = function ($nodes) use (&$sanitizeTree) {
            $result = [];
            foreach ($nodes as $node) {
                if ($node['is_dir']) {
                    $children = $sanitizeTree($node['children']);
                    $node['children'] = $children;
                    $node['items_count'] = count($children);

                    $recFiles = 0;
                    $recSize = 0;
                    $maxMtime = $node['mtime'] ?? 0;

                    foreach ($children as $child) {
                        if ($child['is_dir']) {
                            $recFiles += (int) ($child['recursive_files_count'] ?? 0);
                            $recSize += (int) ($child['size'] ?? 0);
                        } else {
                            $recFiles += 1;
                            $recSize += (int) ($child['size'] ?? 0);
                        }
                        if (! empty($child['mtime']) && $child['mtime'] > $maxMtime) {
                            $maxMtime = $child['mtime'];
                        }
                    }

                    $node['recursive_files_count'] = $recFiles;
                    $node['size'] = $recSize;
                    $node['mtime'] = $maxMtime > 0 ? $maxMtime : null;
                }
                $result[] = $node;
            }

            usort($result, function ($a, $b) {
                if ($a['is_dir'] && ! $b['is_dir']) {
                    return -1;
                }
                if (! $a['is_dir'] && $b['is_dir']) {
                    return 1;
                }
                // Sort numeric year folders descending (newest years first)
                if ($a['is_dir'] && $b['is_dir'] && preg_match('/^\d{4}$/', $a['name']) && preg_match('/^\d{4}$/', $b['name'])) {
                    return strcmp($b['name'], $a['name']);
                }

                return strcasecmp($a['name'], $b['name']);
            });

            return $result;
        };

        $archiveTree = $sanitizeTree($tree);
        $syncState = $syncService->getSyncState();

        return view('reports.archive', compact('archiveTree', 'committees', 'syncState', 'canManageArchive'));
    }

    public function archiveSyncStatus()
    {
        $syncService = app(OneDriveSyncService::class);

        return response()->json($syncService->getSyncState());
    }

    public function connectOneDrive()
    {
        if (! $this->isRsc()) {
            abort(403, 'Unauthorized');
        }

        $state = 'onedrive_rclone_'.Str::random(32);
        Cache::put('onedrive_oauth_state_'.$state, Auth::id(), 600);

        $syncService = app(OneDriveSyncService::class);

        return redirect()->away($syncService->buildMicrosoftOAuthUrl($state));
    }

    public function saveOneDriveToken(Request $request)
    {
        if (! $this->isRsc()) {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'token_json' => 'required|string',
            'drive_id' => 'nullable|string|max:255',
            'drive_type' => 'nullable|string|max:50',
        ]);

        $syncService = app(OneDriveSyncService::class);
        $res = $syncService->saveTokenAndConfigureRemote(
            $request->input('token_json'),
            $request->input('drive_id'),
            $request->input('drive_type')
        );

        if (empty($res['success'])) {
            return redirect()->back()->with('error', $res['error'] ?? __('messages.flash.committee_reports.invalid_token_json'));
        }

        exec('php '.escapeshellarg(base_path('artisan')).' onedrive:sync-archives --force-resync > /dev/null 2>&1 &');

        return redirect()->route('committee-reports.archive')->with('success', __('messages.sync_started_background'));
    }

    public function triggerArchiveSync(Request $request)
    {
        if (! $this->isRsc()) {
            abort(403, 'Unauthorized');
        }

        $syncService = app(OneDriveSyncService::class);
        $subPath = $request->input('sub_path');
        $forceResync = $request->boolean('force_resync');

        if ($request->boolean('inline') || app()->environment('testing')) {
            $state = $syncService->syncAndIndex(true, $subPath, $forceResync);
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $state['message'] ?? __('messages.sync_completed_success'),
                    'state' => $state,
                ]);
            }

            return redirect()->back()->with('success', $state['message'] ?? __('messages.sync_completed_success'));
        }

        $syncService->updateSyncState([
            'status' => 'syncing',
            'stage' => 'starting',
            'message' => __('messages.sync_started_background'),
            'error' => null,
        ]);

        $cmd = 'php '.escapeshellarg(base_path('artisan')).' onedrive:sync-archives';
        if ($subPath) {
            $cmd .= ' --path='.escapeshellarg($subPath);
        }
        if ($forceResync) {
            $cmd .= ' --force-resync';
        }
        exec($cmd.' > /dev/null 2>&1 &');

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => __('messages.sync_started_background'),
                'state' => $syncService->getSyncState(),
            ]);
        }

        return redirect()->back()->with('info', __('messages.sync_started_background'));
    }

    public function downloadArchiveZip(Request $request)
    {
        $request->validate([
            'folder_path' => 'nullable|string|max:1000',
            'paths' => 'nullable|array|max:200',
            'paths.*' => 'string|max:1000',
        ]);

        $archiver = app(ReportArchiver::class);
        $folderPath = $request->input('folder_path');
        $paths = $request->input('paths', []);

        if (empty($folderPath) && empty($paths)) {
            abort(400, 'No folder or file paths specified for zip download.');
        }

        $zipDir = storage_path('app/private/tmp');
        if (! is_dir($zipDir)) {
            @mkdir($zipDir, 0775, true);
        }

        $zipFilename = 'archive_export_'.date('Y-m-d_His').'.zip';
        $zipFilePath = $zipDir.'/'.uniqid('archive_', true).'.zip';

        $zip = new \ZipArchive;
        if ($zip->open($zipFilePath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
            abort(500, 'Could not create ZIP archive.');
        }

        $addedCount = 0;
        $disk = Storage::disk('storagebox');

        $addDiskDirectoryToZip = function (string $diskRelDir, string $zipPrefix) use (&$addDiskDirectoryToZip, $disk, $zip, &$addedCount) {
            $files = $disk->allFiles($diskRelDir);
            foreach ($files as $f) {
                $relInside = ltrim(substr($f, strlen($diskRelDir)), '/');
                $entryName = trim($zipPrefix.'/'.$relInside, '/');
                $fullPath = $disk->path($f);
                if (is_file($fullPath) && is_readable($fullPath)) {
                    $zip->addFile($fullPath, $entryName);
                    $addedCount++;
                }
            }
        };

        if (! empty($folderPath)) {
            if (str_contains($folderPath, '..')) {
                abort(403, 'Invalid folder path.');
            }
            $diskRel = $archiver->toDiskRelativePath($folderPath);
            $folderBase = basename($folderPath) ?: 'Archives';
            $zipFilename = $folderBase.'_'.date('Y-m-d').'.zip';
            $addDiskDirectoryToZip($diskRel, $folderBase);
        } else {
            foreach ($paths as $rawPath) {
                if (str_contains($rawPath, '..')) {
                    continue;
                }
                $diskRel = $archiver->toDiskRelativePath($rawPath);
                if ($disk->exists($diskRel)) {
                    $fullPath = $disk->path($diskRel);
                    if (is_dir($fullPath)) {
                        $addDiskDirectoryToZip($diskRel, basename($diskRel));
                    } elseif (is_file($fullPath) && is_readable($fullPath)) {
                        $zip->addFile($fullPath, basename($diskRel));
                        $addedCount++;
                    }
                }
            }
        }

        if ($addedCount === 0) {
            $zip->addFromString('README.txt', 'No physical files found in the selected archive path.');
        }

        $zip->close();

        return response()->download($zipFilePath, $zipFilename, [
            'Content-Type' => 'application/zip',
            'X-Content-Type-Options' => 'nosniff',
        ])->deleteFileAfterSend(true);
    }

    public function uploadArchiveFiles(Request $request)
    {
        if (! $this->isRsc()) {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'target_path' => 'required|string|max:1000',
            'files' => 'required|array|min:1|max:20',
            'files.*' => 'required|file|mimes:pdf,png,jpg,jpeg,gif,webp,doc,docx,xls,xlsx,csv,ppt,pptx,txt,zip|max:25600',
        ]);

        $targetPath = trim(str_replace('\\', '/', $request->input('target_path')), '/');
        if (str_contains($targetPath, '..')) {
            abort(403, 'Invalid target path.');
        }

        $archiver = app(ReportArchiver::class);
        $syncService = app(OneDriveSyncService::class);
        $diskRelFolder = $archiver->toDiskRelativePath($targetPath);
        $treePath = $archiver->toTreePath($targetPath);

        $uploadedFiles = [];
        foreach ($request->file('files') as $file) {
            if (! $file || ! $file->isValid()) {
                continue;
            }
            $safeName = str_replace(['/', '\\', "\0", '..'], '', basename($file->getClientOriginalName()));
            if ($safeName === '') {
                continue;
            }

            $storedPath = ($diskRelFolder !== '' ? "{$diskRelFolder}/{$safeName}" : $safeName);
            $stream = fopen($file->getRealPath(), 'rb');
            Storage::disk('storagebox')->writeStream($storedPath, $stream);
            if (is_resource($stream)) {
                fclose($stream);
            }

            $syncService->pushPathToOneDrive($storedPath);
            $uploadedFiles[] = $safeName;
        }

        try {
            Artisan::call('storagebox:cache-archives');
        } catch (\Throwable $e) {
            Log::warning('Could not re-index archives after upload: '.$e->getMessage());
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => __('messages.archive_files_uploaded_success'),
                'uploaded' => $uploadedFiles,
                'path' => $treePath,
            ]);
        }

        return redirect()->route('committee-reports.archive', ['path' => $treePath])
            ->with('success', __('messages.archive_files_uploaded_success'));
    }

    public function createArchiveFolder(Request $request)
    {
        if (! $this->isRsc()) {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'parent_path' => 'required|string|max:1000',
            'folder_name' => 'required|string|max:255',
        ]);

        $parentPath = trim(str_replace('\\', '/', $request->input('parent_path')), '/');
        $folderName = trim(str_replace(['/', '\\', "\0", '..'], '', $request->input('folder_name')));

        if (str_contains($parentPath, '..') || $folderName === '') {
            abort(422, 'Invalid folder name or parent path.');
        }

        $archiver = app(ReportArchiver::class);
        $syncService = app(OneDriveSyncService::class);

        $newTreePath = $archiver->toTreePath($parentPath.'/'.$folderName);
        $diskRelPath = $archiver->toDiskRelativePath($newTreePath);

        Storage::disk('storagebox')->makeDirectory($diskRelPath);
        $syncService->pushPathToOneDrive($diskRelPath);

        try {
            Artisan::call('storagebox:cache-archives');
        } catch (\Throwable $e) {
            Log::warning('Could not re-index archives after folder creation: '.$e->getMessage());
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => __('messages.archive_folder_created_success'),
                'path' => $newTreePath,
            ]);
        }

        return redirect()->route('committee-reports.archive', ['path' => $newTreePath])
            ->with('success', __('messages.archive_folder_created_success'));
    }

    public function renameArchiveItem(Request $request)
    {
        if (! $this->isRsc()) {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'path' => 'required|string|max:1000',
            'new_name' => 'required|string|max:255',
        ]);

        $rawPath = trim(str_replace('\\', '/', $request->input('path')), '/');
        $newName = trim(str_replace(['/', '\\', "\0", '..'], '', $request->input('new_name')));

        if (str_contains($rawPath, '..') || $rawPath === '' || $rawPath === 'Archives' || $newName === '') {
            abort(422, 'Invalid path or new name.');
        }

        $archiver = app(ReportArchiver::class);
        $oldTreePath = $archiver->toTreePath($rawPath);
        $parentTreePath = dirname($oldTreePath);
        if ($parentTreePath === '.' || $parentTreePath === '') {
            $parentTreePath = 'Archives';
        }
        $newTreePath = $parentTreePath.'/'.$newName;

        $oldDiskRel = $archiver->toDiskRelativePath($oldTreePath);
        $newDiskRel = $archiver->toDiskRelativePath($newTreePath);

        if (! Storage::disk('storagebox')->exists($oldDiskRel)) {
            abort(404, 'Archive item not found on StorageBox.');
        }

        Storage::disk('storagebox')->move($oldDiskRel, $newDiskRel);

        try {
            Artisan::call('storagebox:cache-archives');
        } catch (\Throwable $e) {
            Log::warning('Could not re-index archives after rename: '.$e->getMessage());
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => __('messages.archive_item_renamed_success'),
                'path' => $parentTreePath,
                'new_item_path' => $newTreePath,
            ]);
        }

        return redirect()->route('committee-reports.archive', ['path' => $parentTreePath])
            ->with('success', __('messages.archive_item_renamed_success'));
    }

    public function deleteArchiveItem(Request $request)
    {
        if (! $this->isRsc()) {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'path' => 'required|string|max:1000',
            'is_dir' => 'nullable|boolean',
        ]);

        $rawPath = trim(str_replace('\\', '/', $request->input('path')), '/');
        if (str_contains($rawPath, '..') || $rawPath === '' || $rawPath === 'Archives') {
            abort(422, 'Cannot delete root archive folder.');
        }

        $archiver = app(ReportArchiver::class);
        $syncService = app(OneDriveSyncService::class);

        $treePath = $archiver->toTreePath($rawPath);
        $parentTreePath = dirname($treePath);
        if ($parentTreePath === '.' || $parentTreePath === '') {
            $parentTreePath = 'Archives';
        }

        $diskRel = $archiver->toDiskRelativePath($treePath);
        $disk = Storage::disk('storagebox');

        if (! $disk->exists($diskRel)) {
            abort(404, 'Archive item not found on StorageBox.');
        }

        $fullPath = $disk->path($diskRel);
        $isDir = is_dir($fullPath) || $request->boolean('is_dir');

        if ($isDir) {
            $disk->deleteDirectory($diskRel);
        } else {
            $disk->delete($diskRel);
        }

        $syncService->deletePathFromOneDrive($diskRel, $isDir);

        try {
            Artisan::call('storagebox:cache-archives');
        } catch (\Throwable $e) {
            Log::warning('Could not re-index archives after delete: '.$e->getMessage());
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => __('messages.archive_item_deleted_success'),
                'path' => $parentTreePath,
            ]);
        }

        return redirect()->route('committee-reports.archive', ['path' => $parentTreePath])
            ->with('success', __('messages.archive_item_deleted_success'));
    }

    public function downloadStorageboxFile(Request $request)
    {
        $encryptedPath = $request->query('file');
        if (! $encryptedPath) {
            abort(400, 'Missing file parameter.');
        }

        try {
            $filePath = Crypt::decryptString($encryptedPath);

            // Check if file exists on storagebox disk
            if (! Storage::disk('storagebox')->exists($filePath)) {
                abort(404, 'File not found on storage box.');
            }

            // Path & File System Security check: ensure path is safe
            $safePath = basename($filePath);

            $disposition = request()->query('disposition', 'attachment');
            if (! in_array($disposition, ['attachment', 'inline'])) {
                $disposition = 'attachment';
            }

            if ($disposition === 'inline') {
                return response()->file(Storage::disk('storagebox')->path($filePath), [
                    'Content-Type' => Storage::disk('storagebox')->mimeType($filePath) ?? 'application/pdf',
                    'Content-Disposition' => 'inline; filename="'.$safePath.'"',
                    'X-Content-Type-Options' => 'nosniff',
                ]);
            }

            return Storage::disk('storagebox')->download(
                $filePath,
                $safePath,
                [
                    'Content-Type' => Storage::disk('storagebox')->mimeType($filePath) ?? 'application/octet-stream',
                    'X-Content-Type-Options' => 'nosniff',
                ]
            );
        } catch (\Exception $e) {
            Log::error('Failed to decrypt or download file from storagebox: '.$e->getMessage());
            abort(403, 'Invalid download request.');
        }
    }
}
