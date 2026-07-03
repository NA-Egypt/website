<?php

namespace App\Http\Controllers;

use App\Models\ServiceBodyAgenda;
use App\Models\ServiceBody;
use App\Models\ServiceBodyAgendaAttachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use App\Services\MpdfService;
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
        
        // Super Admin and RSC are never restricted
        if ($user->hasRole('super admin') || $user->hasRole('rsc')) {
            return false;
        }

        // ServiceBody role (RCM) is not restricted
        if ($user->hasRole('ServiceBody')) {
            return false;
        }

        // Everyone else is restricted
        return true;
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
            
            $daySql = \Illuminate\Support\Facades\DB::getDriverName() === 'sqlite'
                ? "cast(strftime('%d', meeting_date) as integer)"
                : "DAY(meeting_date)";

            $query->where(function($q) use ($daySql) {
                // Exceptional are visible immediately
                $q->where('is_exceptional', true)
                  ->orWhere(function($subQ) use ($daySql) {
                      $now = now();
                      $subQ->where(function($inner) use ($now, $daySql) {
                          $inner->whereRaw("{$daySql} <= 10")
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
                      })->orWhere(function($inner) use ($now, $daySql) {
                          $inner->whereRaw("{$daySql} > 10")
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
        } elseif (!$isRsc) {
            $sb = $this->getServiceBody();
            if (!$sb) {
                abort(403, 'You are not assigned to any Service Body.');
            }
            $query->where('service_body_id', $sb->id);
        } else {
            // RSC and Super Admin see all states (draft, submitted, approved)
            $query->whereIn('status', ['draft', 'submitted', 'approved']);
            
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
        $user = Auth::user();
        $isRsc = $this->isRsc();
        $sb = $this->getServiceBody();
        $serviceBodies = [];

        if ($isRsc) {
            $serviceBodies = ServiceBody::with('groups')->get();
        } else {
            if (!$user || !$user->hasPermissionTo('create sb agenda') || !$sb) {
                abort(403, 'Only users with create sb agenda permission can create agendas.');
            }
            $sb->load('groups');
        }

        return view('service-body-agendas.create', compact('sb', 'serviceBodies', 'isRsc'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $isRsc = $this->isRsc();
        $sb = $this->getServiceBody();

        if (!$isRsc) {
            if (!$user || !$user->hasPermissionTo('create sb agenda') || !$sb) {
                abort(403, 'Unauthorized');
            }
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
            'attachments' => 'nullable|array|max:5',
            'attachments.*' => 'file|mimes:pdf,png,jpg,jpeg,docx,xlsx|max:5120',
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

        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('agenda_attachments', 'storagebox');
                ServiceBodyAgendaAttachment::create([
                    'service_body_agenda_id' => $agenda->id,
                    'file_path' => $path,
                    'original_name' => $file->getClientOriginalName(),
                    'mime_type' => $file->getClientMimeType(),
                    'file_size' => $file->getSize(),
                ]);
            }
        }

        if ($agenda->status === 'submitted') {
            $this->sendNotificationEmail($agenda);
        }

        if ($agenda->status === 'approved') {
            app(\App\Services\ServiceBodyAgendaArchiver::class)->archive($agenda);
        }

        return redirect()->route('service-body-agendas.index')->with('success', __('messages.agenda_created_success'));
    }

    public function show($id)
    {
        $agenda = ServiceBodyAgenda::with(['serviceBody', 'attachments'])->findOrFail($id);
        
        if (!$this->isAgendaVisibleToUser($agenda)) {
            abort(403, 'Unauthorized');
        }

        return view('service-body-agendas.show', compact('agenda'));
    }

    public function edit($id)
    {
        $user = Auth::user();
        $isRsc = $this->isRsc();
        $agenda = ServiceBodyAgenda::with(['serviceBody', 'attachments'])->findOrFail($id);

        if (!$isRsc) {
            $sb = $this->getServiceBody();
            if (!$sb || $sb->id !== $agenda->service_body_id || !$user || !$user->hasPermissionTo('edit sb agenda')) {
                abort(403, 'Unauthorized');
            }
            if ($agenda->status !== 'draft') {
                return redirect()->route('service-body-agendas.show', $agenda->id)
                    ->with('error', __('messages.submitted_agendas_cannot_be_edited'));
            }
        }

        $serviceBodies = $isRsc ? ServiceBody::with('groups')->get() : [];
        $sb = !$isRsc ? $this->getServiceBody()->load('groups') : null;

        return view('service-body-agendas.edit', compact('agenda', 'sb', 'serviceBodies', 'isRsc'));
    }

    public function update(Request $request, $id)
    {
        $agenda = ServiceBodyAgenda::findOrFail($id);
        $user = Auth::user();

        if (!$this->isRsc()) {
            $sb = $this->getServiceBody();
            if (!$sb || $sb->id !== $agenda->service_body_id || !$user || !$user->hasPermissionTo('edit sb agenda')) {
                abort(403, 'Unauthorized');
            }
            if ($agenda->status !== 'draft') {
                return redirect()->route('service-body-agendas.show', $agenda->id)
                    ->with('error', __('messages.submitted_agendas_cannot_be_edited'));
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
            'attachments' => 'nullable|array|max:5',
            'attachments.*' => 'file|mimes:pdf,png,jpg,jpeg,docx,xlsx|max:5120',
        ]);

        if ($request->hasFile('attachments')) {
            $currentCount = $agenda->attachments()->count();
            $newCount = count($request->file('attachments'));
            if ($currentCount + $newCount > 5) {
                return redirect()->back()->withErrors(['attachments' => 'An agenda can have a maximum of 5 attachments.'])->withInput();
            }

            foreach ($request->file('attachments') as $file) {
                $path = $file->store('agenda_attachments', 'storagebox');
                ServiceBodyAgendaAttachment::create([
                    'service_body_agenda_id' => $agenda->id,
                    'file_path' => $path,
                    'original_name' => $file->getClientOriginalName(),
                    'mime_type' => $file->getClientMimeType(),
                    'file_size' => $file->getSize(),
                ]);
            }
        }

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

        return redirect()->route('service-body-agendas.index')->with('success', __('messages.agenda_updated_success'));
    }

    public function destroy($id)
    {
        $agenda = ServiceBodyAgenda::findOrFail($id);
        $user = Auth::user();

        if (!$this->isRsc()) {
            $sb = $this->getServiceBody();
            if (!$sb || $sb->id !== $agenda->service_body_id || !$user || !$user->hasPermissionTo('delete sb agenda')) {
                abort(403, 'Unauthorized');
            }
            if ($agenda->status !== 'draft') {
                abort(403, 'Only drafts can be deleted.');
            }
        }

        // Delete physical files
        foreach ($agenda->attachments as $attachment) {
            if (Storage::disk('storagebox')->exists($attachment->file_path)) {
                Storage::disk('storagebox')->delete($attachment->file_path);
            }
            $attachment->delete();
        }

        $agenda->delete();

        return redirect()->route('service-body-agendas.index')->with('success', __('messages.agenda_deleted_success'));
    }

    public function pdf($id)
    {
        $agenda = ServiceBodyAgenda::with('serviceBody')->findOrFail($id);

        if (!$this->isAgendaVisibleToUser($agenda)) {
            abort(403, 'Unauthorized');
        }

        $mpdf = MpdfService::create();

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

        $disposition = request()->query('disposition', 'attachment');
        if (!in_array($disposition, ['attachment', 'inline'])) {
            $disposition = 'attachment';
        }

        return response($mpdf->Output($filename, 'S'), 200)
               ->header('Content-Type', 'application/pdf')
               ->header('Content-Disposition', $disposition . '; filename="' . $filename . '"');
    }

    public function approve($id)
    {
        $agenda = ServiceBodyAgenda::findOrFail($id);
        $user = Auth::user();
        $isRsc = $this->isRsc();

        if (!$isRsc) {
            $sb = $this->getServiceBody();
            if (!$sb || $sb->id !== $agenda->service_body_id || !$user || !$user->hasPermissionTo('approve sb agenda')) {
                abort(403, 'Unauthorized');
            }
        }
        
        if ($agenda->status !== 'submitted') {
            return redirect()->back()->with('error', __('messages.only_submitted_agendas_approved'));
        }

        $agenda->update(['status' => 'approved']);

        app(\App\Services\ServiceBodyAgendaArchiver::class)->archive($agenda);

        return redirect()->route('service-body-agendas.index')->with('success', __('messages.agenda_approved_archived'));
    }

    public function returnToDraft(Request $request, $id)
    {
        $agenda = ServiceBodyAgenda::findOrFail($id);
        $user = Auth::user();
        $isRsc = $this->isRsc();

        if (!$isRsc) {
            $sb = $this->getServiceBody();
            if (!$sb || $sb->id !== $agenda->service_body_id || !$user || !$user->hasPermissionTo('approve sb agenda')) {
                abort(403, 'Unauthorized');
            }
        }

        if ($agenda->status !== 'submitted') {
            return redirect()->back()->with('error', __('messages.only_submitted_agendas_returned_draft'));
        }

        $agenda->update(['status' => 'draft']);
        return redirect()->route('service-body-agendas.index')->with('success', __('messages.agenda_returned_draft'));
    }

    public function archive(Request $request)
    {
        $user = auth()->user();
        $query = ServiceBodyAgenda::with('serviceBody');

        $daySql = \Illuminate\Support\Facades\DB::getDriverName() === 'sqlite'
            ? "cast(strftime('%d', meeting_date) as integer)"
            : "DAY(meeting_date)";

        if ($this->isRestrictedConsumer($user)) {
            $query->where('status', 'approved');
            $query->where(function($q) use ($daySql) {
                $q->where('is_exceptional', true)
                  ->orWhere(function($subQ) use ($daySql) {
                      $now = now();
                      $subQ->where(function($inner) use ($now, $daySql) {
                          $inner->whereRaw("{$daySql} <= 10")
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
                      })->orWhere(function($inner) use ($now, $daySql) {
                          $inner->whereRaw("{$daySql} > 10")
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
            // RSC / Super Admin / RCM
            if (!$this->isRsc()) {
                $sb = $this->getServiceBody();
                $sbId = $sb ? $sb->id : 0;
                $query->where(function($q) use ($sbId, $daySql) {
                    if ($sbId) {
                        $q->where('service_body_id', $sbId);
                    }
                    $q->orWhere(function($otherQ) use ($sbId, $daySql) {
                        if ($sbId) {
                            $otherQ->where('service_body_id', '!=', $sbId);
                        }
                        $otherQ->where('status', 'approved');
                        $otherQ->where(function($releaseQ) use ($daySql) {
                            $releaseQ->where('is_exceptional', true)
                                     ->orWhere(function($subQ) use ($daySql) {
                                         $now = now();
                                         $subQ->where(function($inner) use ($now, $daySql) {
                                             $inner->whereRaw("{$daySql} <= 10")
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
                                         })->orWhere(function($inner) use ($now, $daySql) {
                                             $inner->whereRaw("{$daySql} > 10")
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
                    });
                });
            } else {
                // Super Admin & RSC see all agendas (draft, submitted, approved)
                // No status restriction
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

        // Build list of allowed filenames for physical files based on user access
        $allowedFilenames = [];
        $userSb = null;
        if (!$this->isRsc() && !$this->isRestrictedConsumer($user)) {
            $userSb = $this->getServiceBody();
        }

        // Build storage box files list under Archives/service_body_agendas/
        $cacheKey = 'storagebox_agendas_files_list';
        if ($request->query('refresh') == '1') {
            Cache::forget($cacheKey);
        }

        $allStorageboxFiles = Cache::remember($cacheKey, 43200, function () {
            $list = [];
            try {
                if (Storage::disk('storagebox')->exists('Archives/service_body_agendas')) {
                    $allFiles = Storage::disk('storagebox')->allFiles('Archives/service_body_agendas');
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
                Log::error("Failed to list files from storagebox Archives/service_body_agendas: " . $e->getMessage());
            }
            return $list;
        });

        $serviceBodies = ServiceBody::all();
        $filesAndDirs = [];

        // Build mapping of allowed filenames for restricted consumers / other Service Bodies' agendas from the dbAgendas (which only has approved, released agendas for others/restricted consumers)
        if ($this->isRestrictedConsumer($user) || (!$this->isRsc() && $userSb)) {
            foreach ($dbAgendas as $agenda) {
                // If user is RCM, only add it to allowedFilenames if it's NOT their own (own is already allowed unconditionally by folder name matching)
                if (!$this->isRestrictedConsumer($user) && $agenda->service_body_id === $userSb->id) {
                    continue;
                }
                
                if ($agenda->status === 'approved') {
                    $year = $agenda->meeting_date->format('Y');
                    $monthNum = (int)$agenda->meeting_date->format('m');
                    $monthStr = $agenda->meeting_date->format('m');
                    $monthArabicName = $this->arabicMonths[$monthNum] ?? $monthStr;
                    $sbName = $agenda->serviceBody ? $agenda->serviceBody->ar_name : 'خدمة';
                    $sbName = str_replace(['/', '\\', "\0"], '', $sbName);
                    $cleanedSbName = str_replace(' ', '_', $sbName);
                    $suffix = $agenda->is_exceptional ? '_EX' : '';
                    $filename = sprintf('%s_%s_%s%s.pdf', $cleanedSbName, $monthArabicName, $year, $suffix);
                    $allowedFilenames[] = strtolower($filename);
                }
            }
        }

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

            // If user is RCM (not RSC/SuperAdmin), enforce own service body filter OR approved/released filter on physical files
            if (!$this->isRsc() && !$this->isRestrictedConsumer($user) && $userSb) {
                $arName = $userSb->ar_name ? mb_strtolower($userSb->ar_name, 'UTF-8') : null;
                $enName = $userSb->en_name ? mb_strtolower($userSb->en_name, 'UTF-8') : null;
                $filePathLower = mb_strtolower($filePath, 'UTF-8');
                
                $match = false;
                if ($arName && str_contains($filePathLower, str_replace(' ', '_', $arName))) {
                    $match = true;
                }
                if ($enName && str_contains($filePathLower, str_replace(' ', '_', $enName))) {
                    $match = true;
                }
                if (!$match) {
                    if (!in_array(strtolower($fileInfo['name']), $allowedFilenames)) {
                        continue;
                    }
                }
            }

            // If user is restricted consumer, only show files that match released database agendas
            if ($this->isRestrictedConsumer($user)) {
                if (!in_array(strtolower($fileInfo['name']), $allowedFilenames)) {
                    continue;
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
        $archiver = app(\App\Services\ServiceBodyAgendaArchiver::class);
        foreach ($dbAgendas as $agenda) {
            $period = $archiver->getTargetMeetingPeriod($agenda->meeting_date);
            $targetYear = $period['year'];
            $arabicMonth = $period['arabic_month'];

            $year = $agenda->meeting_date->format('Y');
            $monthNum = (int)$agenda->meeting_date->format('m');
            $monthStr = $agenda->meeting_date->format('m');
            $monthArabicName = $this->arabicMonths[$monthNum] ?? $monthStr;

            $sbName = $agenda->serviceBody ? $agenda->serviceBody->ar_name : 'خدمة';
            $sbName = str_replace(['/', '\\', "\0"], '', $sbName);
            $cleanedSbName = str_replace(' ', '_', $sbName);

            $suffix = $agenda->is_exceptional ? '_EX' : '';
            $filename = sprintf('%s_%s_%s%s.pdf', $cleanedSbName, $monthArabicName, $year, $suffix);
            $virtualPath = "Archives/أجندة إجتماع لجنة خدمة الاقليم/{$targetYear}/أجندة {$arabicMonth} {$targetYear}/التقارير الشهرية حتى 10 {$arabicMonth} {$targetYear}/أجندات المناطق و المنتديات/{$sbName}/{$filename}";

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

    public function downloadAttachment($id)
    {
        $attachment = ServiceBodyAgendaAttachment::findOrFail($id);
        $agenda = $attachment->serviceBodyAgenda;

        if (!$this->isAgendaVisibleToUser($agenda)) {
            abort(403, 'Unauthorized');
        }

        if (!Storage::disk('storagebox')->exists($attachment->file_path)) {
            abort(404, 'File not found');
        }

        $disposition = request()->query('disposition', 'attachment');
        if (!in_array($disposition, ['attachment', 'inline'])) {
            $disposition = 'attachment';
        }

        if ($disposition === 'inline') {
            return response()->file(Storage::disk('storagebox')->path($attachment->file_path), [
                'Content-Type' => $attachment->mime_type,
                'Content-Disposition' => 'inline; filename="' . $attachment->original_name . '"',
                'X-Content-Type-Options' => 'nosniff',
            ]);
        }

        return Storage::disk('storagebox')->download($attachment->file_path, $attachment->original_name, [
            'Content-Type' => $attachment->mime_type,
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    public function deleteAttachment($id)
    {
        $attachment = ServiceBodyAgendaAttachment::findOrFail($id);
        $agenda = $attachment->serviceBodyAgenda;

        // Only owner or admin can delete draft attachments
        if (!$this->isRsc()) {
            $sb = $this->getServiceBody();
            if (!$sb || $sb->id !== $agenda->service_body_id) {
                abort(403, 'Unauthorized');
            }
        }

        if ($agenda->status !== 'draft') {
            abort(403, 'Cannot delete attachments from a submitted agenda.');
        }

        if (Storage::disk('storagebox')->exists($attachment->file_path)) {
            Storage::disk('storagebox')->delete($attachment->file_path);
        }

        $attachment->delete();

        return redirect()->back()->with('success', __('messages.attachment_deleted_success'));
    }
}
