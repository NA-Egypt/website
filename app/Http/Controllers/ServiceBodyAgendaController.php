<?php

namespace App\Http\Controllers;

use App\Models\ServiceBodyAgenda;
use App\Models\ServiceBody;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Mpdf\Mpdf;
use Carbon\Carbon;
use Exception;

class ServiceBodyAgendaController extends Controller
{
    protected $arabicMonths = [
        1 => 'يناير',
        2 => 'فبراير',
        3 => 'مارس',
        4 => 'أبريل',
        5 => 'مايو',
        6 => 'يونيو',
        7 => 'يوليو',
        8 => 'أغسطس',
        9 => 'سبتمبر',
        10 => 'أكتوبر',
        11 => 'نوفمبر',
        12 => 'ديسمبر'
    ];

    protected function getServiceBody()
    {
        $user = Auth::user();
        if (!$user) return null;
        
        return ServiceBody::find($user->service_body_id);
    }

    protected function isRsc()
    {
        if (!Auth::check()) {
            return false;
        }
        $user = Auth::user();
        return $user->hasRole('super admin') || $user->hasRole('rsc');
    }

    protected function isRestrictedConsumer($user)
    {
        if (!$user) {
            return true;
        }
        // General users, GSRs, or other roles without RSC or own ServiceBody permissions
        return $user->hasRole('gsr') || !$user->hasRole('super admin') && !$user->hasRole('rsc') && !$user->hasRole('ServiceBody');
    }

    protected function isAgendaVisibleToUser(ServiceBodyAgenda $agenda)
    {
        $user = auth()->user();

        // RSC can see it
        if ($this->isRsc()) {
            return true;
        }

        // Owner ServiceBody can see it
        $sb = $this->getServiceBody();
        if ($sb && $sb->id === $agenda->service_body_id) {
            return true;
        }

        // Restricted consumers can only see approved agendas that are released
        if ($agenda->status !== 'approved') {
            return false;
        }

        if ($agenda->is_exceptional) {
            return true;
        }

        // Release schedule: available of the coming 10th.
        // If meeting_date <= 10th of its month, release date is 10th of that month.
        // If meeting_date > 10th of its month, release date is 10th of next month.
        $mDate = $agenda->meeting_date;
        if ($mDate->day <= 10) {
            $releaseDate = Carbon::create($mDate->year, $mDate->month, 10);
        } else {
            $releaseDate = Carbon::create($mDate->year, $mDate->month, 10)->addMonth();
        }

        return now()->greaterThanOrEqualTo($releaseDate);
    }

    public function index(Request $request)
    {
        $isRsc = $this->isRsc();
        $user = Auth::user();
        $query = ServiceBodyAgenda::with('serviceBody');

        if ($this->isRestrictedConsumer($user)) {
            // Only approved and released
            $query->where('status', 'approved');
            // We'll filter the collection or query to match the release date logic:
            $query->where(function($q) {
                // Exceptional are visible immediately
                $q->where('is_exceptional', true)
                  ->orWhere(function($subQ) {
                      $now = now();
                      // Released if:
                      // Case A: meeting_date <= 10th of current month, and today is >= 10th of that month.
                      // Case B: meeting_date > 10th of current month, and today is >= 10th of next month.
                      // Simply put, we can filter using raw sql or eloquent where date queries.
                      // Let's filter out anything where today is before the release date:
                      // release_date = (day(meeting_date) <= 10) ? 10th of same month : 10th of next month.
                      // For a query, we can check:
                      // If meeting_date's release date is in the past:
                      $subQ->where(function($inner) use ($now) {
                          $inner->whereRaw("DAY(meeting_date) <= 10")
                                ->where(function($dateQ) use ($now) {
                                    $dateQ->whereYear('meeting_date', '<', $now->year)
                                          ->orWhere(function($yQ) use ($now) {
                                              $yQ->whereYear('meeting_date', $now->year)
                                                 ->where(function($mQ) use ($now) {
                                                     $mQ->whereMonth('meeting_date', '<', $now->month)
                                                        ->orWhere(function($dQ) use ($now) {
                                                            $dQ->whereMonth('meeting_date', $now->month)
                                                               ->whereDay('meeting_date', '<=', $now->day); // Wait, if meeting_date is June 5, release is June 10. If today is June 10, then we are fine.
                                                        });
                                                 });
                                          });
                                });
                      })->orWhere(function($inner) use ($now) {
                          $inner->whereRaw("DAY(meeting_date) > 10")
                                ->where(function($dateQ) use ($now) {
                                    // Release date is 10th of next month. So meeting_date must be from previous months.
                                    // If meeting_date is in same year: month(meeting_date) < month(now), or (month(meeting_date) == month(now) - 1 and day(now) >= 10)
                                    $dateQ->whereYear('meeting_date', '<', $now->year)
                                          ->orWhere(function($yQ) use ($now) {
                                              $yQ->whereYear('meeting_date', $now->year)
                                                 ->where(function($mQ) use ($now) {
                                                     // If meeting_date's month is at least 2 months ago:
                                                     $mQ->where('meeting_date', '<', Carbon::now()->startOfMonth()->subMonth()->day(10));
                                                 });
                                          });
                                });
                      });
                  });
            });
        } elseif (!$isRsc) {
            $sb = $this->getServiceBody();
            if (!$sb) {
                abort(403, 'You are not assigned to any Service Body.');
            }
            $query->where('service_body_id', $sb->id);
        } else {
            // RSC sees submitted and approved
            if ($user->hasRole('super admin')) {
                $query->whereIn('status', ['draft', 'submitted', 'approved']);
            } else {
                $query->whereIn('status', ['submitted', 'approved']);
            }
            
            if ($request->has('service_body_id') && $request->service_body_id) {
                $query->where('service_body_id', $request->service_body_id);
            }
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('body', 'like', "%$search%")
                  ->orWhereDate('meeting_date', $search);
            });
        }

        $agendas = $query->latest('meeting_date')->paginate(10);
        $serviceBodies = $isRsc ? ServiceBody::all() : [];

        return view('service-body-agendas.index', compact('agendas', 'isRsc', 'serviceBodies'));
    }

    public function create()
    {
        $isRsc = $this->isRsc();
        $sb = $this->getServiceBody();
        $serviceBodies = [];

        if ($isRsc) {
            $serviceBodies = ServiceBody::with('groups')->get();
        } elseif (!$sb) {
            abort(403, 'Only Service Body members (RCM) can create agendas.');
        } else {
            $sb->load('groups');
        }

        return view('service-body-agendas.create', compact('sb', 'serviceBodies', 'isRsc'));
    }

    public function store(Request $request)
    {
        $isRsc = $this->isRsc();
        $sb = $this->getServiceBody();

        if (!$isRsc && !$sb) {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'service_body_id' => $isRsc ? 'required|exists:service_bodies,id' : 'nullable',
            'meeting_date' => 'required|date',
            'sections' => 'required|array|min:1',
            'sections.*.headline' => 'nullable|string|max:255',
            'sections.*.content' => 'required|string',
            'groups_joined' => 'nullable|array',
            'groups_joined.*' => 'nullable|string|max:255',
            'status' => 'required|in:draft,submitted,approved',
            'is_exceptional' => 'nullable|boolean',
        ]);

        $sbId = $isRsc ? $request->service_body_id : $sb->id;

        $agenda = ServiceBodyAgenda::create([
            'service_body_id' => $sbId,
            'agenda_date' => now()->toDateString(),
            'meeting_date' => $request->meeting_date,
            'groups_joined' => $request->groups_joined ? array_filter($request->groups_joined) : [],
            'body' => $request->sections,
            'status' => $request->status,
            'is_exceptional' => $request->boolean('is_exceptional'),
        ]);

        if ($agenda->status === 'submitted') {
            $this->sendNotificationEmail($agenda);
        }

        if ($agenda->status === 'approved') {
            app(\App\Services\ServiceBodyAgendaArchiver::class)->archive($agenda);
        }

        return redirect()->route('service-body-agendas.index')->with('success', 'Agenda created successfully.');
    }

    public function show($id)
    {
        $agenda = ServiceBodyAgenda::with('serviceBody')->findOrFail($id);
        
        if (!$this->isAgendaVisibleToUser($agenda)) {
            abort(403, 'Unauthorized');
        }

        return view('service-body-agendas.show', compact('agenda'));
    }

    public function edit($id)
    {
        $agenda = ServiceBodyAgenda::with('serviceBody')->findOrFail($id);

        if (!$this->isRsc()) {
            $sb = $this->getServiceBody();
            if (!$sb || $sb->id !== $agenda->service_body_id) {
                abort(403, 'Unauthorized');
            }
            if ($agenda->status !== 'draft') {
                return redirect()->route('service-body-agendas.show', $agenda->id)
                    ->with('error', 'Submitted agendas cannot be edited.');
            }
        }

        $serviceBodies = $this->isRsc() ? ServiceBody::with('groups')->get() : [];
        $sb = !$this->isRsc() ? $this->getServiceBody()->load('groups') : null;

        return view('service-body-agendas.edit', compact('agenda', 'sb', 'serviceBodies', 'isRsc'));
    }

    public function update(Request $request, $id)
    {
        $agenda = ServiceBodyAgenda::findOrFail($id);

        if (!$this->isRsc()) {
            $sb = $this->getServiceBody();
            if (!$sb || $sb->id !== $agenda->service_body_id) {
                abort(403, 'Unauthorized');
            }
            if ($agenda->status !== 'draft') {
                return redirect()->route('service-body-agendas.show', $agenda->id)
                    ->with('error', 'Submitted agendas cannot be edited.');
            }
        }

        $request->validate([
            'service_body_id' => $this->isRsc() ? 'required|exists:service_bodies,id' : 'nullable',
            'meeting_date' => 'required|date',
            'sections' => 'required|array|min:1',
            'sections.*.headline' => 'nullable|string|max:255',
            'sections.*.content' => 'required|string',
            'groups_joined' => 'nullable|array',
            'groups_joined.*' => 'nullable|string|max:255',
            'status' => 'required|in:draft,submitted,approved',
            'is_exceptional' => 'nullable|boolean',
        ]);

        $sbId = $this->isRsc() ? $request->service_body_id : $agenda->service_body_id;
        $wasDraft = $agenda->status === 'draft';

        $agenda->update([
            'service_body_id' => $sbId,
            'meeting_date' => $request->meeting_date,
            'groups_joined' => $request->groups_joined ? array_filter($request->groups_joined) : [],
            'body' => $request->sections,
            'status' => $request->status,
            'is_exceptional' => $request->boolean('is_exceptional'),
        ]);

        if ($wasDraft && $agenda->status === 'submitted') {
            $this->sendNotificationEmail($agenda);
        }

        if ($agenda->status === 'approved') {
            app(\App\Services\ServiceBodyAgendaArchiver::class)->archive($agenda->fresh());
        }

        return redirect()->route('service-body-agendas.index')->with('success', 'Agenda updated successfully.');
    }

    public function destroy($id)
    {
        $agenda = ServiceBodyAgenda::findOrFail($id);

        if (!$this->isRsc()) {
            $sb = $this->getServiceBody();
            if (!$sb || $sb->id !== $agenda->service_body_id) {
                abort(403, 'Unauthorized');
            }
            if ($agenda->status !== 'draft') {
                abort(403, 'Only drafts can be deleted.');
            }
        }

        $agenda->delete();

        return redirect()->route('service-body-agendas.index')->with('success', 'Agenda deleted successfully.');
    }

    public function pdf($id)
    {
        $agenda = ServiceBodyAgenda::with('serviceBody')->findOrFail($id);

        if (!$this->isAgendaVisibleToUser($agenda)) {
            abort(403, 'Unauthorized');
        }

        $defaultConfig = (new \Mpdf\Config\ConfigVariables())->getDefaults();
        $fontDirs = $defaultConfig['fontDir'];

        $defaultFontConfig = (new \Mpdf\Config\FontVariables())->getDefaults();
        $fontData = $defaultFontConfig['fontdata'];

        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'directionality' => app()->getLocale() == 'ar' ? 'rtl' : 'ltr',
            'fontDir' => array_merge($fontDirs, [resource_path('fonts')]),
            'fontdata' => $fontData + [
                'amiri' => [
                    'R' => 'Amiri-Regular.ttf',
                ],
                'cairo' => [
                    'R' => 'Cairo-Regular.ttf',
                ],
            ],
            'default_font' => 'xbriyaz',
        ]);
        
        $mpdf->autoArabic = true;
        $mpdf->autoScriptToLang = true;
        $mpdf->autoLangToFont = true;

        $agendas = collect([$agenda]);
        $html = view('service-body-agendas.pdf', compact('agendas'))->render();
        $mpdf->WriteHTML($html);

        $year = $agenda->meeting_date->format('Y');
        $monthNum = (int)$agenda->meeting_date->format('m');
        $monthStr = $agenda->meeting_date->format('m');
        $monthArabicName = $this->arabicMonths[$monthNum] ?? $monthStr;

        $sbName = $agenda->serviceBody ? $agenda->serviceBody->ar_name : 'خدمة';
        $sbName = str_replace(['/', '\\', "\0"], '', $sbName);
        $cleanedSbName = str_replace(' ', '_', $sbName);

        $suffix = $agenda->is_exceptional ? '_EX' : '';
        $filename = sprintf('%s_%s_%s%s.pdf', $cleanedSbName, $monthArabicName, $year, $suffix);

        return response($mpdf->Output($filename, 'S'), 200)
               ->header('Content-Type', 'application/pdf')
               ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    public function approve($id)
    {
        if (!$this->isRsc()) {
            abort(403, 'Unauthorized');
        }

        $agenda = ServiceBodyAgenda::findOrFail($id);
        
        if ($agenda->status !== 'submitted') {
            return redirect()->back()->with('error', 'Only submitted agendas can be approved.');
        }

        $agenda->update(['status' => 'approved']);

        app(\App\Services\ServiceBodyAgendaArchiver::class)->archive($agenda);

        return redirect()->route('service-body-agendas.index')->with('success', 'Agenda approved and archived successfully.');
    }

    public function returnToDraft(Request $request, $id)
    {
        if (!$this->isRsc()) {
            abort(403, 'Unauthorized');
        }

        $agenda = ServiceBodyAgenda::findOrFail($id);

        if ($agenda->status !== 'submitted') {
            return redirect()->back()->with('error', 'Only submitted agendas can be returned to draft.');
        }

        $agenda->update(['status' => 'draft']);

        return redirect()->route('service-body-agendas.index')->with('success', 'Agenda returned to draft.');
    }

    public function archive(Request $request)
    {
        $user = auth()->user();
        $query = ServiceBodyAgenda::with('serviceBody');

        if ($this->isRestrictedConsumer($user)) {
            $query->where('status', 'approved');
            $query->where(function($q) {
                $q->where('is_exceptional', true)
                  ->orWhere(function($subQ) {
                      $now = now();
                      $subQ->where(function($inner) use ($now) {
                          $inner->whereRaw("DAY(meeting_date) <= 10")
                                ->where(function($dateQ) use ($now) {
                                    $dateQ->whereYear('meeting_date', '<', $now->year)
                                          ->orWhere(function($yQ) use ($now) {
                                              $yQ->whereYear('meeting_date', $now->year)
                                                 ->where(function($mQ) use ($now) {
                                                     $mQ->whereMonth('meeting_date', '<', $now->month)
                                                        ->orWhere(function($dQ) use ($now) {
                                                            $dQ->whereMonth('meeting_date', $now->month)
                                                               ->whereDay('meeting_date', '<=', $now->day);
                                                        });
                                                 });
                                          });
                                });
                      })->orWhere(function($inner) use ($now) {
                          $inner->whereRaw("DAY(meeting_date) > 10")
                                ->where(function($dateQ) use ($now) {
                                    $dateQ->whereYear('meeting_date', '<', $now->year)
                                          ->orWhere(function($yQ) use ($now) {
                                              $yQ->whereYear('meeting_date', $now->year)
                                                 ->where(function($mQ) use ($now) {
                                                     $mQ->where('meeting_date', '<', Carbon::now()->startOfMonth()->subMonth()->day(10));
                                                 });
                                          });
                                });
                      });
                  });
            });
        } else {
            // RSC or RCM can see approved ones in Archive page
            $query->where('status', 'approved');
            if (!$this->isRsc()) {
                $sb = $this->getServiceBody();
                if ($sb) {
                    $query->where('service_body_id', $sb->id);
                }
            }
        }

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('body', 'like', "%$search%")
                  ->orWhereHas('serviceBody', function ($sbQ) use ($search) {
                      $sbQ->where('ar_name', 'like', "%$search%")
                          ->orWhere('en_name', 'like', "%$search%");
                  });
            });
        }

        if ($request->has('service_body_id') && $request->service_body_id != '') {
            $query->where('service_body_id', $request->service_body_id);
        }

        if ($request->has('start_date') && $request->start_date != '') {
            $query->where('meeting_date', '>=', $request->start_date);
        }

        if ($request->has('end_date') && $request->end_date != '') {
            $query->where('meeting_date', '<=', $request->end_date);
        }

        $dbAgendas = $query->orderBy('meeting_date', 'desc')->get();

        // Build storage box files list under service_body_agendas/
        $cacheKey = 'storagebox_agendas_files_list';
        if ($request->query('refresh') == '1') {
            Cache::forget($cacheKey);
        }

        $allStorageboxFiles = Cache::remember($cacheKey, 43200, function () {
            $list = [];
            try {
                if (Storage::disk('storagebox')->exists('service_body_agendas')) {
                    $allFiles = Storage::disk('storagebox')->allFiles('service_body_agendas');
                    foreach ($allFiles as $filePath) {
                        if (str_starts_with(basename($filePath), '.') || str_contains($filePath, '/.')) {
                            continue;
                        }
                        try {
                            $size = Storage::disk('storagebox')->size($filePath);
                        } catch (Exception $e) {
                            $size = 0;
                        }

                        $list[] = [
                            'name' => basename($filePath),
                            'path' => $filePath,
                            'size' => $size,
                        ];
                    }
                }
            } catch (Exception $e) {
                Log::error("Failed to list files from storagebox service_body_agendas: " . $e->getMessage());
            }
            return $list;
        });

        $serviceBodies = ServiceBody::all();
        $filesAndDirs = [];

        foreach ($allStorageboxFiles as $fileInfo) {
            $filePath = $fileInfo['path'];

            // ServiceBody filter for storagebox files
            if ($request->has('service_body_id') && $request->service_body_id != '') {
                $selectedSb = $serviceBodies->firstWhere('id', $request->service_body_id);
                if ($selectedSb) {
                    $arName = $selectedSb->ar_name ? mb_strtolower($selectedSb->ar_name, 'UTF-8') : null;
                    $enName = $selectedSb->en_name ? mb_strtolower($selectedSb->en_name, 'UTF-8') : null;
                    $filePathLower = mb_strtolower($filePath, 'UTF-8');
                    
                    $match = false;
                    if ($arName && str_contains($filePathLower, str_replace(' ', '_', $arName))) {
                        $match = true;
                    }
                    if ($enName && str_contains($filePathLower, str_replace(' ', '_', $enName))) {
                        $match = true;
                    }
                    if (!$match) {
                        continue;
                    }
                }
            }

            // Search filter
            if ($request->has('search') && $request->search != '') {
                $search = mb_strtolower($request->search, 'UTF-8');
                $filename = mb_strtolower($fileInfo['name'], 'UTF-8');
                if (!str_contains($filename, $search)) {
                    continue;
                }
            }

            $prefixedPath = str_starts_with($filePath, 'Archives/') ? $filePath : 'Archives/' . $filePath;

            $filesAndDirs[] = [
                'is_dir' => false,
                'name' => $fileInfo['name'],
                'path' => $prefixedPath,
                'encrypted_path' => Crypt::encryptString($filePath),
                'size' => $fileInfo['size'],
            ];
        }

        // Add database virtual files
        foreach ($dbAgendas as $agenda) {
            $year = $agenda->meeting_date->format('Y');
            $monthNum = (int)$agenda->meeting_date->format('m');
            $monthStr = $agenda->meeting_date->format('m');
            $monthArabicName = $this->arabicMonths[$monthNum] ?? $monthStr;

            $sbName = $agenda->serviceBody ? $agenda->serviceBody->ar_name : 'خدمة';
            $sbName = str_replace(['/', '\\', "\0"], '', $sbName);
            $cleanedSbName = str_replace(' ', '_', $sbName);

            $suffix = $agenda->is_exceptional ? '_EX' : '';
            $filename = sprintf('%s_%s_%s%s.pdf', $cleanedSbName, $monthArabicName, $year, $suffix);
            $virtualPath = "Archives/service_body_agendas/{$year}/{$monthStr}/{$filename}";

            $filesAndDirs[] = [
                'is_dir' => false,
                'name' => ($agenda->serviceBody->ar_name ?? 'Service Body Agenda') . ' - ' . $agenda->meeting_date->format('Y-m-d') . '.pdf',
                'path' => $virtualPath,
                'db_agenda_id' => $agenda->id,
                'size' => 0,
            ];
        }

        // Build a structured tree directory matching Archives structure
        $tree = [];
        foreach ($filesAndDirs as $file) {
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
                $accumulatedPath .= '/' . $dirName;
                if (!isset($currentLevel[$dirName])) {
                    $currentLevel[$dirName] = [
                        'is_dir' => true,
                        'name' => $dirName,
                        'path' => $accumulatedPath,
                        'children' => [],
                    ];
                }
                $currentLevel = &$currentLevel[$dirName]['children'];
            }

            $fileName = end($parts);
            $currentLevel[$fileName] = [
                'is_dir' => false,
                'name' => $file['name'],
                'path' => $file['path'],
                'encrypted_path' => $file['encrypted_path'] ?? null,
                'db_agenda_id' => $file['db_agenda_id'] ?? null,
                'size' => $file['size'],
            ];
        }

        $sanitizeTree = function ($tree) use (&$sanitizeTree) {
            $result = [];
            foreach ($tree as $key => $node) {
                if ($node['is_dir']) {
                    $node['children'] = $sanitizeTree($node['children']);
                }
                $result[] = $node;
            }

            usort($result, function ($a, $b) {
                if ($a['is_dir'] && !$b['is_dir']) return -1;
                if (!$a['is_dir'] && $b['is_dir']) return 1;
                return strcasecmp($a['name'], $b['name']);
            });

            return $result;
        };

        $archiveTree = $sanitizeTree($tree);

        return view('service-body-agendas.archive', compact('archiveTree', 'serviceBodies'));
    }

    protected function sendNotificationEmail(ServiceBodyAgenda $agenda)
    {
        try {
            $sbName = $agenda->serviceBody->ar_name ?? $agenda->serviceBody->en_name ?? 'Unknown Service Body';
            $meetingDate = $agenda->meeting_date->format('Y-m-d');
            
            Mail::send([], [], function ($message) use ($sbName, $meetingDate) {
                $message->to(['rsc@naegypt.org', 'arsc@naegypt.org'])
                        ->subject("New Service Body Agenda: {$sbName} ({$meetingDate})")
                        ->html("
                            <p>Dear RSC,</p>
                            <p>A new Service Body Agenda has been submitted by the service body <strong>{$sbName}</strong> for the meeting date <strong>{$meetingDate}</strong>.</p>
                            <p>You can view and review this agenda in the dashboard or archive.</p>
                            <p>Regards,<br>NA Egypt System</p>
                        ");
            });
        } catch (Exception $e) {
            Log::error("Failed to send service body agenda email alert: " . $e->getMessage());
        }
    }
}
