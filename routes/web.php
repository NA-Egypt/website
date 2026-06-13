<?php

use App\Http\Controllers\CityController;
use App\Http\Controllers\GreatingPagesController;
use App\Http\Controllers\CustomFormController;
use App\Http\Controllers\PublicFormController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\MeetingController;
use App\Http\Controllers\NeighborhoodController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\ServiceBodyController;
use App\Http\Controllers\ServiceCommitteeController;
use App\Http\Controllers\TopicController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MeetingFilterController;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use App\Http\Controllers\AzureAuthController;
use App\Http\Controllers\ContactUsController;
use App\Http\Controllers\ForPublicController;
use Illuminate\Support\Facades\App;
use App\Models\City;
use App\Models\Group;
use App\Models\ServiceCommittee;
use App\Models\ServiceBody;
use App\Models\Meeting;

// Localization Routes:
Route::group(
    [
        
        'prefix' => LaravelLocalization::setLocale(),
        'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'localeViewPath', 'setLanguageDirection']
    ],
    function () {

        Route::middleware(['auth', 'role.redirect'])->group(function () {

            // Dashboard:
            Route::get('/dashboard', [GreatingPagesController::class, 'dashboard'])
                ->name('dashboard');

//            Route::get('/group/show/{group}', [GroupController::class, 'show'])
//                ->name('group.show');

            Route::middleware(['role:super admin'])->group(function () {
                // Transactions:
                Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions.index');

                // ServiceBody Routes:
                Route::get('/serviceBody',[ServiceBodyController::class,'index'])->name('serviceBody.index');
                Route::get('/serviceBody/create',[ServiceBodyController::class,'create'])->name('serviceBody.create');
                Route::post('/serviceBody',[ServiceBodyController::class,'store'])->name('serviceBody.store');
                Route::get('/serviceBody/{serviceBody}',[ServiceBodyController::class,'edit'])->name('serviceBody.edit');
                Route::put('/serviceBody/{serviceBody}',[ServiceBodyController::class,'update'])->name('serviceBody.update');
                Route::delete('/serviceBody/{serviceBody}',[ServiceBodyController::class,'destroy'])->name('serviceBody.destroy');

                // City Routes:
                Route::get('/city',[CityController::class,'index'])->name('city.index');
                Route::get('/city/create',[CityController::class,'create'])->name('city.create');
                Route::post('/city',[CityController::class,'store'])->name('city.store');
                Route::get('/city/{city}',[CityController::class,'edit'])->name('city.edit');
                Route::put('/city/{city}',[CityController::class,'update'])->name('city.update');
                Route::delete('/city/{city}',[CityController::class,'destroy'])->name('city.destroy');

                // Neighborhood Routes:
                Route::get('/neighborhood',[NeighborhoodController::class,'index'])->name('neighborhood.index');
                Route::get('/neighborhood/create',[NeighborhoodController::class,'create'])->name('neighborhood.create');
                Route::post('/neighborhood',[NeighborhoodController::class,'store'])->name('neighborhood.store');
                Route::get('/neighborhood/{neighborhood}',[NeighborhoodController::class,'edit'])->name('neighborhood.edit');
                Route::put('/neighborhood/{neighborhood}',[NeighborhoodController::class,'update'])->name('neighborhood.update');
                Route::delete('/neighborhood/{neighborhood}',[NeighborhoodController::class,'destroy'])->name('neighborhood.destroy');

                // Topic Routes:
                Route::get('/topic',[TopicController::class,'index'])->name('topic.index');
                Route::get('/topic/create',[TopicController::class,'create'])->name('topic.create');
                Route::post('/topic',[TopicController::class,'store'])->name('topic.store');
                Route::get('/topic/{topic}',[TopicController::class,'edit'])->name('topic.edit');
                Route::put('/topic/{topic}',[TopicController::class,'update'])->name('topic.update');
                Route::delete('/topic/{topic}',[TopicController::class,'destroy'])->name('topic.destroy');
            });

            // Group Routes:
            Route::get('/group',[GroupController::class,'index'])->name('group.index');
            Route::get('/group/create',[GroupController::class,'create'])->name('group.create');
            Route::post('/group',[GroupController::class,'store'])->name('group.store');
            Route::get('/group/show/{group}',[GroupController::class,'show'])->name('group.show');
            Route::get('/group/{group}',[GroupController::class,'edit'])->name('group.edit');
            Route::put('/group/{group}',[GroupController::class,'update'])->name('group.update');
            Route::delete('/group/{group}',[GroupController::class,'destroy'])->name('group.destroy');

            // Service Committees Routes:
            Route::get('/serviceCommittee',[serviceCommitteeController::class,'index'])->name('serviceCommittee.index');
            Route::get('/serviceCommittee/create',[serviceCommitteeController::class,'create'])->name('serviceCommittee.create');
            Route::post('/serviceCommittee',[serviceCommitteeController::class,'store'])->name('serviceCommittee.store');
            Route::get('/serviceCommittee/{serviceCommittee}',[serviceCommitteeController::class,'edit'])->name('serviceCommittee.edit');
            Route::put('/serviceCommittee/{serviceCommittee}',[serviceCommitteeController::class,'update'])->name('serviceCommittee.update');
            Route::get('/serviceCommittee/show/{serviceCommittee}',[serviceCommitteeController::class,'show'])->name('serviceCommittee.show');
            Route::delete('/serviceCommittee/{serviceCommittee}',[serviceCommitteeController::class,'destroy'])->name('serviceCommittee.destroy');

            // Meeting Routes:
            Route::get('/meeting',[MeetingController::class,'index'])->name('meeting.index');
            Route::get('/meeting/create',[MeetingController::class,'create'])->name('meeting.create');
            Route::post('/meeting',[MeetingController::class,'store'])->name('meeting.store');
            Route::get('/meeting/{meeting}',[MeetingController::class,'edit'])->name('meeting.edit');
            Route::put('/meeting/{meeting}',[MeetingController::class,'update'])->name('meeting.update');
            Route::delete('/meeting/{meeting}',[MeetingController::class,'destroy'])->name('meeting.destroy');

            // Agenda Routes:
            Route::get('/agenda/create', [\App\Http\Controllers\AgendaController::class, 'create'])->name('agenda.create');
            Route::post('/agenda', [\App\Http\Controllers\AgendaController::class, 'store'])->name('agenda.store');
            Route::get('/groups-agendas/archive', [\App\Http\Controllers\AgendaController::class, 'archive'])->name('groups-agendas.archive');
            Route::post('/groups-agendas/export', [\App\Http\Controllers\AgendaController::class, 'exportMultipleAgendasPdf'])->name('groups-agendas.exportPdf');
            Route::get('/agenda/{agenda}', [\App\Http\Controllers\AgendaController::class, 'show'])->name('agenda.show');
            Route::get('/agenda/{agenda}/export', [\App\Http\Controllers\AgendaController::class, 'exportPdf'])->name('agenda.exportPdf');
            Route::get('/serviceBody/{serviceBody}/agendas',[ServiceBodyController::class,'agendas'])->name('serviceBody.agendas');
            Route::post('/serviceBody/{serviceBody}/agendas/export',[ServiceBodyController::class,'exportAgendasPdf'])->name('serviceBody.agendas.exportPdf');

            Route::middleware(['role:super admin'])->group(function () {
                // Permissions:
                Route::get('/permissions', [PermissionController::class, 'index'])
                    ->name('permissions.index');
                Route::get('/permissions/create', [PermissionController::class, 'create'])
                    ->name('permissions.create');
                Route::post('/permissions', [PermissionController::class, 'store'])
                    ->name('permissions.store');
                Route::get('/permissions/{permission}',
                    [PermissionController::class, 'edit'])
                    ->name('permissions.edit');
                Route::put('/permissions/{permission}',
                    [PermissionController::class, 'update'])
                    ->name('permissions.update');
                Route::delete('/permissions/{permission}',
                    [PermissionController::class, 'destroy'])
                    ->name('permissions.destroy');

                // Roles:
                Route::resource('roles', RoleController::class)->only(['index', 'create', 'store']);

                // users:
                Route::post('users/bulk-action', [UserController::class, 'bulkAction'])->name('users.bulk_action');
                Route::resource('users', UserController::class)->only(['index', 'edit', 'update', 'create', 'store']);
                Route::get('/roles/{role}/assign-permissions',
                    [RoleController::class, 'assignPermissions'])
                    ->name('roles.assign-permissions');
                Route::post('/roles/{role}/update-permissions',
                    [RoleController::class, 'updatePermissions'])
                    ->name('roles.update-permissions');
                Route::delete('/roles/{role}',
                    [RoleController::class, 'destroy'])->name('roles.destroy');
                Route::delete('/users/{user}',
                    [UserController::class, 'destroy'])->name('users.destroy');
            });

            // Custom Form Builder:
            Route::post('forms/{form}/toggle-status', [CustomFormController::class, 'toggleStatus'])->name('forms.toggleStatus');
            Route::post('forms/{form}/duplicate', [CustomFormController::class, 'duplicate'])->name('forms.duplicate');
            Route::post('forms/{form}/reset', [CustomFormController::class, 'resetSubmissions'])->name('forms.reset');
            Route::get('forms/{form}/report', [CustomFormController::class, 'showReport'])->name('forms.report');
            Route::get('forms/{form}/report/pdf', [CustomFormController::class, 'exportPdf'])->name('forms.reportPdf');
            Route::get('forms/{form}/submissions/{submission}/pdf', [CustomFormController::class, 'exportSubmissionPdf'])->name('forms.submissionPdf');
            Route::get('forms/{form}/report/csv', [CustomFormController::class, 'exportCsv'])->name('forms.csv');
            Route::resource('forms', CustomFormController::class);

            // Committee Reports:
            Route::get('committee-reports/archive', [\App\Http\Controllers\CommitteeReportController::class, 'archive'])->name('committee-reports.archive');
            Route::get('committee-reports/storagebox/download', [\App\Http\Controllers\CommitteeReportController::class, 'downloadStorageboxFile'])->name('committee-reports.downloadStorageboxFile');
            Route::get('committee-reports/attachments/{id}', [\App\Http\Controllers\CommitteeReportController::class, 'downloadAttachment'])->name('committee-reports.downloadAttachment');
            Route::delete('committee-reports/attachments/{id}', [\App\Http\Controllers\CommitteeReportController::class, 'deleteAttachment'])->name('committee-reports.deleteAttachment');
            Route::resource('committee-reports', \App\Http\Controllers\CommitteeReportController::class);
            Route::get('committee-reports/{id}/pdf', [\App\Http\Controllers\CommitteeReportController::class, 'pdf'])->name('committee-reports.pdf');
            Route::post('committee-reports/export', [\App\Http\Controllers\CommitteeReportController::class, 'exportReportsPdf'])->name('committee-reports.exportPdf');
            Route::post('committee-reports/{id}/send', [\App\Http\Controllers\CommitteeReportController::class, 'send'])->name('committee-reports.send');
            Route::post('committee-reports/{id}/approve-and-send', [\App\Http\Controllers\CommitteeReportController::class, 'approveAndSend'])->name('committee-reports.approveAndSend');
            Route::post('committee-reports/{id}/return-to-draft', [\App\Http\Controllers\CommitteeReportController::class, 'returnToDraft'])->name('committee-reports.returnToDraft');

            // Calendar
            Route::get('/calendar', \App\Livewire\YearlyCalendar::class)->name('calendar.index');

            // Change Requests:
            Route::get('change-requests', [\App\Http\Controllers\ChangeRequestController::class, 'index'])->name('change-requests.index');
            Route::get('change-requests/create', [\App\Http\Controllers\ChangeRequestController::class, 'create'])->name('change-requests.create');
            Route::post('change-requests', [\App\Http\Controllers\ChangeRequestController::class, 'store'])->name('change-requests.store');
            Route::get('change-requests/{id}', [\App\Http\Controllers\ChangeRequestController::class, 'show'])->name('change-requests.show');
            Route::patch('change-requests/{id}/status', [\App\Http\Controllers\ChangeRequestController::class, 'updateStatus'])->name('change-requests.update-status');
            Route::get('change-requests/{id}/download', [\App\Http\Controllers\ChangeRequestController::class, 'downloadAttachment'])->name('change-requests.download-attachment');
        });

        // Auth:
        Route::get('/login/microsoft', [AzureAuthController::class, 'redirectToAzure']);
        Route::get('/login/microsoft/callback', [AzureAuthController::class, 'handleAzureCallback']);

        // Logout:
        Route::post('/logout', [AzureAuthController::class, 'logout'])->name('logout');

        Route::get('/meetings', [MeetingFilterController::class, 'filterMeetings'])->name('frontend.meetings');
        Route::get('/export-meetings-pdf', [MeetingFilterController::class, 'exportMeetingsToPDF'])->name('exportMeetingsToPDF');
        Route::get('/export-meetings-csv', [MeetingFilterController::class, 'exportMeetingsToCSV'])->name('exportMeetingsToCSV');


        // Searches:
        Route::get('/searches/city/{id}/groups', [SearchController::class, 'city'])->name('searches.city');
        Route::get('/group/{id}/meetings', [SearchController::class, 'groupMeetings'])->name('searches.meeting');

        // Frontend:
        Route::get('/', function(){
            $homeStats = [
                'weekly_meetings' => Meeting::notMonthlyRecurrent()->count(),
                'groups' => Group::count(),
                'governorates' => City::count(),
            ];

            $jftFileName = date('j') . '_' . strtolower(date('M')) . '_.html';
            $jftFilePath = public_path('literature/jft/' . $jftFileName);
            $jftContent = '';
            if (file_exists($jftFilePath)) {
                $html = file_get_contents($jftFilePath);
                if (preg_match('/<body>(.*?)<\/body>/is', $html, $matches)) {
                    $jftContent = $matches[1];
                } else {
                    $jftContent = $html;
                }
            }

            return view('frontend.home', compact('homeStats', 'jftContent'));
        })->name('frontend.home');

        Route::get('/literature', function(){
            return view('frontend.literature');
        })->name('frontend.literature');

        Route::get('/forpublic', function(){
            return view('frontend.forpublic');
        })->name('frontend.forpublic');

        Route::get('/questions', function(){
            return view('frontend.questions');
        })->name('frontend.questions');

        Route::get('/events', [\App\Http\Controllers\FrontendEventController::class, 'index'])->name('frontend.events');

        Route::get('/test', [ForPublicController::class, 'index'])->name('frontend.test');

        Route::get('/test', function(){
            return view('frontend.test');
        })->name('frontend.test');

        Route::get('/speakers', function(){
            return view('frontend.speakers');
        })->name('frontend.speakers');
        
        Route::get('/fdsurvey', function () {
            $groups = Group::all();
            $serviceBody = ServiceBody::all();
            $serviceCommittee = ServiceCommittee::all();
            return view('frontend.fdsurvey', compact('groups', 'serviceBody', 'serviceCommittee'));
        })->name('frontend.fdsurvey');

        Route::get('/committees', [ServiceCommitteeController::class, '__invoke'])->name('frontend.comms');

        Route::get('/contactus', [ContactUsController::class, 'create'])->name('contactus.create');
        Route::post('/contactus', [ContactUsController::class, 'store'])->name('contactus.store');

        // Public Custom Forms
        Route::get('/f/{slug}', [PublicFormController::class, 'show'])->name('forms.show.public');
        Route::post('/f/{slug}', [PublicFormController::class, 'submit'])->name('forms.submit.public');

    }
);
