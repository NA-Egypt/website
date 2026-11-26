<?php

use App\Http\Controllers\Admin\ApkRequestController;
use App\Http\Controllers\Admin\WhatsAppDeviceController;
use App\Http\Controllers\Admin\WhatsAppDocsController;
use App\Http\Controllers\Admin\WhatsAppInboxController;
use App\Http\Controllers\Admin\WhatsAppReportController;
use App\Http\Controllers\Admin\WhatsAppSubscriberController;
use App\Http\Controllers\AgendaController;
use App\Http\Controllers\Api\CalendarEventController;
use App\Http\Controllers\ApiUsageAnalyticsController;
use App\Http\Controllers\ApkDownloadController;
use App\Http\Controllers\AzureAuthController;
use App\Http\Controllers\ChangeRequestController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\CommitteeLiteratureController;
use App\Http\Controllers\CommitteeReportArchiveController;
use App\Http\Controllers\CommitteeReportController;
use App\Http\Controllers\CommitteeReportsGuideController;
use App\Http\Controllers\ContactUsController;
use App\Http\Controllers\CustomFormController;
use App\Http\Controllers\DirectOnlineGroupController;
use App\Http\Controllers\FacebookTargetingController;
use App\Http\Controllers\ForPublicController;
use App\Http\Controllers\FrontendEventController;
use App\Http\Controllers\GreatingPagesController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\GsrGuideController;
use App\Http\Controllers\HelplineDashboardController;
use App\Http\Controllers\HelplinePublicFormController;
use App\Http\Controllers\InventorySlipController;
use App\Http\Controllers\LitController;
use App\Http\Controllers\LiteratureRequestController;
use App\Http\Controllers\LitLedgerController;
use App\Http\Controllers\LitReconciliationController;
use App\Http\Controllers\LookupController;
use App\Http\Controllers\MeetingController;
use App\Http\Controllers\MeetingExportController;
use App\Http\Controllers\MeetingFilterController;
use App\Http\Controllers\NeighborhoodController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\PublicFormController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\ServiceBodyAgendaController;
use App\Http\Controllers\ServiceBodyController;
use App\Http\Controllers\ServiceBodyMapController;
use App\Http\Controllers\ServiceCommitteeController;
use App\Http\Controllers\StocktakingController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\SubscriberController;
use App\Http\Controllers\TopicController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WorkgroupController;
use App\Http\Middleware\CheckStoreLocked;
use App\Livewire\YearlyCalendar;
use App\Models\CalendarEvent;
use App\Models\City;
use App\Models\Day;
use App\Models\Group;
use App\Models\Meeting;
use App\Models\Neighborhood;
use App\Models\ServiceBody;
use App\Models\ServiceCommittee;
use App\Services\JftService;
use Carbon\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

// Azure Auth Routes (Web & Mobile):
Route::get('/login/microsoft', [AzureAuthController::class, 'redirectToAzure'])->name('login.azure');
Route::get('/login/microsoft/callback', [AzureAuthController::class, 'handleAzureCallback'])->name('login.azure.callback');
Route::get('/auth/azure/redirect', [\App\Http\Controllers\Auth\AzureAuthController::class, 'redirectForMobile']);
Route::get('/auth/azure/callback', [\App\Http\Controllers\Auth\AzureAuthController::class, 'handleCallbackForMobile']);

// Committee Reports & Workgroups Documentation (External Public & Standard Routes):
Route::get('/reports-guide', [CommitteeReportsGuideController::class, 'publicIndex'])->name('reports.guide.public');
Route::get('/committee-reports-guide', [CommitteeReportsGuideController::class, 'index'])->name('reports.guide');

// Public Entity Lookup (Normalized Search):
Route::get('/lookup/{entity}', [LookupController::class, 'search'])->name('lookup.search');

// Notifications (any authenticated user):
Route::middleware(['auth'])->group(function () {
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read_all');
});

// Localization Routes:
Route::group(
    [

        'prefix' => LaravelLocalization::setLocale(),
        'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'localeViewPath', 'setLanguageDirection'],
    ],
    function () {

        Route::middleware(['auth', 'role.redirect'])->group(function () {

            // Dashboard:
            Route::get('/dashboard', [GreatingPagesController::class, 'dashboard'])
                ->name('dashboard');

            // GSR Meetings Guide:
            Route::get('/gsr-guide', [GsrGuideController::class, 'index'])->name('gsr.guide');

            // Stocktaking Routes:
            Route::get('/store/stocktaking', [StocktakingController::class, 'index'])->name('store.stocktaking.index');
            Route::post('/store/stocktaking/start', [StocktakingController::class, 'start'])->name('store.stocktaking.start');
            Route::get('/store/stocktaking/{session}/count', [StocktakingController::class, 'count'])->name('store.stocktaking.count');
            Route::post('/store/stocktaking/{session}/count', [StocktakingController::class, 'updateCount'])->name('store.stocktaking.update_count');
            Route::get('/store/stocktaking/{session}', [StocktakingController::class, 'show'])->name('store.stocktaking.show');
            Route::post('/store/stocktaking/{session}/adjust', [StocktakingController::class, 'applyAdjustments'])->name('store.stocktaking.adjust');
            Route::get('/store/stocktaking/{session}/pdf', [StocktakingController::class, 'exportPdf'])->name('store.stocktaking.pdf');
            Route::get('/store/stocktaking/{session}/csv', [StocktakingController::class, 'exportCsv'])->name('store.stocktaking.csv');

            // Store Inventory Read Routes:
            Route::get('/store', [StoreController::class, 'index'])->name('store.index');
            Route::get('/store/live-state', [StoreController::class, 'liveState'])->name('store.live_state');
            Route::get('/store/reports', [StoreController::class, 'reports'])->name('store.reports');
            Route::get('/store/reports/pdf', [StoreController::class, 'exportPdf'])->name('store.reports.pdf');
            Route::get('/store/reports/csv', [StoreController::class, 'exportCsv'])->name('store.reports.csv');

            // Store Inventory Mutation Routes (Locked during active stocktaking):
            Route::middleware([CheckStoreLocked::class])->group(function () {
                Route::post('/store', [StoreController::class, 'store'])->name('store.store');
                Route::post('/store/bulk-receive', [StoreController::class, 'bulkReceive'])->name('store.bulk_receive');
                Route::post('/store/bulk-transfer', [StoreController::class, 'bulkTransfer'])->name('store.bulk_transfer');
                Route::post('/store/bulk-return', [StoreController::class, 'bulkReturn'])->name('store.bulk_return');
                Route::post('/store/bulk-delete', [StoreController::class, 'bulkDestroy'])->name('store.bulk_delete');
                Route::post('/store/bulk-inline-update', [StoreController::class, 'bulkInlineUpdate'])->name('store.bulk_inline_update');
                Route::post('/store/batch-movement', [StoreController::class, 'batchMovement'])->name('store.batch_movement');
                Route::post('/store/{item}/inline-update', [StoreController::class, 'inlineUpdate'])->name('store.inline_update');
                Route::put('/store/{item}', [StoreController::class, 'update'])->name('store.update');
                Route::delete('/store/{item}', [StoreController::class, 'destroy'])->name('store.destroy');
                Route::post('/store/{id}/restore', [StoreController::class, 'restore'])->name('store.restore');
                Route::post('/store/{item}/receive', [StoreController::class, 'receive'])->name('store.receive');
                Route::post('/store/{item}/transfer', [StoreController::class, 'transfer'])->name('store.transfer');
                Route::post('/store/{item}/return', [StoreController::class, 'returnStock'])->name('store.return');
                Route::post('/store/{item}/damage', [StoreController::class, 'markDamaged'])->name('store.damage');
                Route::post('/store/{item}/cancel-wrong-receive', [StoreController::class, 'cancelWrongReceive'])->name('store.cancel_wrong_receive');
            });

            // Lit Inventory Routes:
            Route::get('/inventory/live-state', [LitLedgerController::class, 'liveState'])->name('inventory.live_state');
            Route::get('/lit', [LitController::class, 'index'])->name('lit.index');
            Route::post('/lit/{item}/damage', [LitController::class, 'markDamaged'])->name('lit.damage');
            Route::get('/lit/reconciliation', [LitReconciliationController::class, 'index'])->name('lit.reconciliation');
            Route::post('/lit/reconciliation/return', [LitReconciliationController::class, 'processReturn'])->name('lit.reconciliation.return');
            Route::get('/lit/ledger', [LitLedgerController::class, 'index'])->name('lit.ledger');
            Route::get('/lit/ledger/pdf', [LitLedgerController::class, 'exportPdf'])->name('lit.ledger.pdf');
            Route::get('/lit/ledger/csv', [LitLedgerController::class, 'exportCsv'])->name('lit.ledger.csv');

            // Inventory Slips Routes:
            Route::get('/slips', [InventorySlipController::class, 'index'])->name('slips.index');
            Route::get('/slips/{slip}', [InventorySlipController::class, 'show'])->name('slips.show');
            Route::post('/slips/{slip}/acknowledge', [InventorySlipController::class, 'acknowledgeReceipt'])->name('slips.acknowledge');
            Route::get('/slips/{slip}/pdf', [InventorySlipController::class, 'exportPdf'])->name('slips.pdf');

            // Literature Request Routes:
            Route::get('/literature-requests/cart', [LiteratureRequestController::class, 'cart'])->name('literature-requests.cart');
            Route::post('/literature-requests/cart/update', [LiteratureRequestController::class, 'updateCart'])->name('literature-requests.cart.update');
            Route::post('/literature-requests/submit', [LiteratureRequestController::class, 'submitRequest'])->name('literature-requests.submit');
            Route::get('/literature-requests/treasurer', [LiteratureRequestController::class, 'treasurerDashboard'])->name('literature-requests.treasurer');
            Route::post('/literature-requests/{id}/approve-send', [LiteratureRequestController::class, 'approveAndSend'])->name('literature-requests.approve-send');
            Route::post('/literature-requests/group/{id}/toggle-status', [LiteratureRequestController::class, 'toggleGroupRequestStatus'])->name('literature-requests.group.toggle-status');
            Route::get('/literature-requests/committee', [LiteratureRequestController::class, 'committeeDashboard'])->name('literature-requests.committee');
            Route::get('/literature-requests/committee/{id}/edit', [LiteratureRequestController::class, 'committeeEdit'])->name('literature-requests.committee.edit');
            Route::post('/literature-requests/committee/{id}/update', [LiteratureRequestController::class, 'committeeUpdate'])->name('literature-requests.committee.update');
            Route::get('/literature-requests/archive', [LiteratureRequestController::class, 'archive'])->name('literature-requests.archive');
            Route::get('/literature-requests/{id}/pdf', [LiteratureRequestController::class, 'exportPdf'])->name('literature-requests.pdf');

            // Committee Literature Requests & Non-Invoice Slips Routes:
            Route::get('/committee-literature', [CommitteeLiteratureController::class, 'index'])->name('committee-literature.index');
            Route::get('/committee-literature/create', [CommitteeLiteratureController::class, 'create'])->name('committee-literature.create');
            Route::post('/committee-literature', [CommitteeLiteratureController::class, 'store'])->name('committee-literature.store');
            Route::get('/committee-literature/{id}', [CommitteeLiteratureController::class, 'show'])->name('committee-literature.show');
            Route::post('/committee-literature/{id}/issue-slip', [CommitteeLiteratureController::class, 'issueSlip'])->name('committee-literature.issue-slip');
            Route::post('/committee-literature/slips/{slipId}/acknowledge', [CommitteeLiteratureController::class, 'acknowledgeSlip'])->name('committee-literature.acknowledge');
            Route::get('/committee-literature/{id}/return', [CommitteeLiteratureController::class, 'returnForm'])->name('committee-literature.return');
            Route::post('/committee-literature/{id}/process-return', [CommitteeLiteratureController::class, 'processReturn'])->name('committee-literature.process-return');
            Route::get('/committee-literature/slips/{slipId}/pdf', [CommitteeLiteratureController::class, 'exportSlipPdf'])->name('committee-literature.slip-pdf');

            //            Route::get('/group/show/{group}', [GroupController::class, 'show'])
            //                ->name('group.show');

            // Read-Only Fellowship Structure & Maps (Super Admin & RSC)
            Route::middleware(['role:super admin|rsc'])->group(function () {
                Route::get('/serviceBody', [ServiceBodyController::class, 'index'])->name('serviceBody.index');
                Route::get('/serviceBody/map', [ServiceBodyMapController::class, 'dashboardMap'])->name('serviceBody.map');
                Route::get('/city', [CityController::class, 'index'])->name('city.index');
                Route::get('/neighborhood', [NeighborhoodController::class, 'index'])->name('neighborhood.index');
                Route::get('/topic', [TopicController::class, 'index'])->name('topic.index');
            });

            Route::middleware(['role:super admin'])->group(function () {
                // Transactions:
                Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions.index');

                // ServiceBody Mutations:
                Route::get('/serviceBody/create', [ServiceBodyController::class, 'create'])->name('serviceBody.create');
                Route::post('/serviceBody', [ServiceBodyController::class, 'store'])->name('serviceBody.store');
                Route::get('/serviceBody/{serviceBody}', [ServiceBodyController::class, 'edit'])->name('serviceBody.edit');
                Route::put('/serviceBody/{serviceBody}', [ServiceBodyController::class, 'update'])->name('serviceBody.update');
                Route::delete('/serviceBody/{serviceBody}', [ServiceBodyController::class, 'destroy'])->name('serviceBody.destroy');

                // City Mutations:
                Route::get('/city/create', [CityController::class, 'create'])->name('city.create');
                Route::post('/city', [CityController::class, 'store'])->name('city.store');
                Route::get('/city/{city}', [CityController::class, 'edit'])->name('city.edit');
                Route::put('/city/{city}', [CityController::class, 'update'])->name('city.update');
                Route::delete('/city/{city}', [CityController::class, 'destroy'])->name('city.destroy');

                // Neighborhood Mutations:
                Route::get('/neighborhood/create', [NeighborhoodController::class, 'create'])->name('neighborhood.create');
                Route::post('/neighborhood', [NeighborhoodController::class, 'store'])->name('neighborhood.store');
                Route::get('/neighborhood/{neighborhood}', [NeighborhoodController::class, 'edit'])->name('neighborhood.edit');
                Route::put('/neighborhood/{neighborhood}', [NeighborhoodController::class, 'update'])->name('neighborhood.update');
                Route::delete('/neighborhood/{neighborhood}', [NeighborhoodController::class, 'destroy'])->name('neighborhood.destroy');

                // Topic Mutations:
                Route::get('/topic/create', [TopicController::class, 'create'])->name('topic.create');
                Route::post('/topic', [TopicController::class, 'store'])->name('topic.store');
                Route::get('/topic/{topic}', [TopicController::class, 'edit'])->name('topic.edit');
                Route::put('/topic/{topic}', [TopicController::class, 'update'])->name('topic.update');
                Route::delete('/topic/{topic}', [TopicController::class, 'destroy'])->name('topic.destroy');

                // Facebook Targeting Routes (Sync is Super Admin only)
                Route::post('/facebook-targeting/sync', [FacebookTargetingController::class, 'sync'])->name('facebook-targeting.sync');
            });

            Route::middleware(['role:super admin|Committees'])->group(function () {
                Route::get('/facebook-targeting', [FacebookTargetingController::class, 'index'])->name('facebook-targeting.index');
                Route::post('/facebook-targeting/download', [FacebookTargetingController::class, 'download'])->name('facebook-targeting.download');
                Route::post('/facebook-targeting/static-map', [FacebookTargetingController::class, 'generateStaticMapUrl'])->name('facebook-targeting.static-map');
            });

            // Group Routes:
            Route::get('/group', [GroupController::class, 'index'])->name('group.index');
            Route::get('/group/create', [GroupController::class, 'create'])->name('group.create');
            Route::post('/group', [GroupController::class, 'store'])->name('group.store');
            Route::get('/group/show/{group}', [GroupController::class, 'show'])->name('group.show');
            Route::get('/group/{group}', [GroupController::class, 'edit'])->name('group.edit');
            Route::put('/group/{group}', [GroupController::class, 'update'])->name('group.update');
            Route::delete('/group/{group}', [GroupController::class, 'destroy'])->name('group.destroy');
            Route::post('/group/{id}/restore', [GroupController::class, 'restore'])->name('group.restore');

            // Direct Online Group Routes:
            Route::get('/direct-online-group', [DirectOnlineGroupController::class, 'index'])->name('direct-online-group.index');
            Route::get('/direct-online-group/create', [DirectOnlineGroupController::class, 'create'])->name('direct-online-group.create');
            Route::post('/direct-online-group', [DirectOnlineGroupController::class, 'store'])->name('direct-online-group.store');
            Route::get('/direct-online-group/show/{directOnlineGroup}', [DirectOnlineGroupController::class, 'show'])->name('direct-online-group.show');
            Route::get('/direct-online-group/{directOnlineGroup}', [DirectOnlineGroupController::class, 'edit'])->name('direct-online-group.edit');
            Route::put('/direct-online-group/{directOnlineGroup}', [DirectOnlineGroupController::class, 'update'])->name('direct-online-group.update');
            Route::delete('/direct-online-group/{directOnlineGroup}', [DirectOnlineGroupController::class, 'destroy'])->name('direct-online-group.destroy');
            Route::post('/direct-online-group/{id}/restore', [DirectOnlineGroupController::class, 'restore'])->name('direct-online-group.restore');

            // Service Committees Routes:
            Route::get('/serviceCommittee', [ServiceCommitteeController::class, 'index'])->name('serviceCommittee.index');
            Route::get('/serviceCommittee/create', [ServiceCommitteeController::class, 'create'])->name('serviceCommittee.create');
            Route::post('/serviceCommittee', [ServiceCommitteeController::class, 'store'])->name('serviceCommittee.store');
            Route::get('/serviceCommittee/{serviceCommittee}', [ServiceCommitteeController::class, 'edit'])->name('serviceCommittee.edit');
            Route::put('/serviceCommittee/{serviceCommittee}', [ServiceCommitteeController::class, 'update'])->name('serviceCommittee.update');
            Route::get('/serviceCommittee/show/{serviceCommittee}', [ServiceCommitteeController::class, 'show'])->name('serviceCommittee.show');
            Route::delete('/serviceCommittee/{serviceCommittee}', [ServiceCommitteeController::class, 'destroy'])->name('serviceCommittee.destroy');

            // Workgroups Routes:
            Route::get('/workgroup', [WorkgroupController::class, 'index'])->name('workgroup.index');
            Route::get('/workgroup/create', [WorkgroupController::class, 'create'])->name('workgroup.create');
            Route::post('/workgroup', [WorkgroupController::class, 'store'])->name('workgroup.store');
            Route::get('/workgroup/{workgroup}', [WorkgroupController::class, 'edit'])->name('workgroup.edit');
            Route::put('/workgroup/{workgroup}', [WorkgroupController::class, 'update'])->name('workgroup.update');
            Route::get('/workgroup/show/{workgroup}', [WorkgroupController::class, 'show'])->name('workgroup.show');
            Route::delete('/workgroup/{workgroup}', [WorkgroupController::class, 'destroy'])->name('workgroup.destroy');

            // Meeting Routes:
            Route::get('/meeting', [MeetingController::class, 'index'])->name('meeting.index');
            Route::get('/meeting/create', [MeetingController::class, 'create'])->name('meeting.create');
            Route::post('/meeting', [MeetingController::class, 'store'])->name('meeting.store');
            Route::get('/meeting/{meeting}', [MeetingController::class, 'edit'])->name('meeting.edit');
            Route::put('/meeting/{meeting}', [MeetingController::class, 'update'])->name('meeting.update');
            Route::delete('/meeting/{meeting}', [MeetingController::class, 'destroy'])->name('meeting.destroy');
            Route::post('/meeting/{id}/restore', [MeetingController::class, 'restore'])->name('meeting.restore');

            // Agenda Routes:
            Route::get('/agenda/create', [AgendaController::class, 'create'])->name('agenda.create');
            Route::post('/agenda', [AgendaController::class, 'store'])->name('agenda.store');
            Route::get('/groups-agendas/archive', [AgendaController::class, 'archive'])->name('groups-agendas.archive');
            Route::post('/groups-agendas/export', [AgendaController::class, 'exportMultipleAgendasPdf'])->name('groups-agendas.exportPdf');
            Route::get('/agenda/{agenda}', [AgendaController::class, 'show'])->name('agenda.show');
            Route::get('/agenda/{agenda}/export', [AgendaController::class, 'exportPdf'])->name('agenda.exportPdf');
            Route::get('/serviceBody/{serviceBody}/agendas', [ServiceBodyController::class, 'agendas'])->name('serviceBody.agendas');
            Route::post('/serviceBody/{serviceBody}/agendas/export', [ServiceBodyController::class, 'exportAgendasPdf'])->name('serviceBody.agendas.exportPdf');

            Route::middleware(['role:super admin'])->group(function () {
                // Agenda Delete Routes:
                Route::delete('/agenda/{agenda}', [AgendaController::class, 'destroy'])->name('agenda.destroy');
                Route::post('/groups-agendas/bulk-delete', [AgendaController::class, 'bulkDestroy'])->name('groups-agendas.bulk_delete');

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
                Route::post('users/{user}/impersonate', [UserController::class, 'impersonate'])->name('users.impersonate');
                Route::resource('users', UserController::class)->only(['index', 'edit', 'update', 'create', 'store']);
                Route::get('/roles/{role}/assign-permissions',
                    [RoleController::class, 'assignPermissions'])
                    ->name('roles.assign-permissions');
                Route::get('/roles/{role}/details',
                    [RoleController::class, 'details'])
                    ->name('roles.details');
                Route::put('/roles/{role}',
                    [RoleController::class, 'update'])
                    ->name('roles.update');
                Route::post('/roles/{role}/update-permissions',
                    [RoleController::class, 'updatePermissions'])
                    ->name('roles.update-permissions');
                Route::delete('/roles/{role}',
                    [RoleController::class, 'destroy'])->name('roles.destroy');
                Route::delete('/users/{user}',
                    [UserController::class, 'destroy'])->name('users.destroy');

                // Subscribers:
                Route::get('/subscribers', [SubscriberController::class, 'index'])->name('subscribers.index');
                Route::get('/subscribers/create', [SubscriberController::class, 'create'])->name('subscribers.create');
                Route::post('/subscribers', [SubscriberController::class, 'store'])->name('subscribers.store_admin');
                Route::post('/subscribers/bulk-action', [SubscriberController::class, 'bulkAction'])->name('subscribers.bulk_action');
                Route::get('/subscribers/ids', [SubscriberController::class, 'getSubscriberIds'])->name('subscribers.ids');
                Route::post('/subscribers/verify-batch', [SubscriberController::class, 'verifyBatch'])->name('subscribers.verify_batch');
                Route::post('/subscribers/{subscriber}/toggle-verification', [SubscriberController::class, 'toggleVerification'])->name('subscribers.toggle-verification');
                Route::delete('/subscribers/{subscriber}', [SubscriberController::class, 'destroy'])->name('subscribers.destroy_admin');
                Route::get('/subscribers-export', [SubscriberController::class, 'export'])->name('subscribers.export');

                // API & Mobile App Analytics:
                Route::get('/admin/api-usage', [ApiUsageAnalyticsController::class, 'index'])->name('admin.api_usage.index');
                Route::get('/admin/api-usage/export', [ApiUsageAnalyticsController::class, 'exportCsv'])->name('admin.api_usage.export');

                // APK Download Requests:
                Route::get('/admin/apk-requests', [ApkRequestController::class, 'index'])->name('admin.apk_requests.index');
                Route::get('/admin/apk-requests/export', [ApkRequestController::class, 'exportCsv'])->name('admin.apk_requests.export');
                Route::post('/admin/apk-requests/{apkRequest}/resend', [ApkRequestController::class, 'resend'])->name('admin.apk_requests.resend');
                Route::delete('/admin/apk-requests/{apkRequest}', [ApkRequestController::class, 'destroy'])->name('admin.apk_requests.destroy');
                Route::post('/admin/apk-requests/bulk-delete', [ApkRequestController::class, 'bulkDestroy'])->name('admin.apk_requests.bulk_destroy');
            });

            // Stop Impersonating Route (Accessible to any authenticated session currently impersonating):
            Route::post('users/stop-impersonation', [UserController::class, 'stopImpersonating'])->name('users.stop_impersonating');

            // Custom Form Builder:
            Route::middleware(['permission:manage own forms'])->group(function () {
                Route::post('forms/{form}/toggle-status', [CustomFormController::class, 'toggleStatus'])->name('forms.toggleStatus')->where('form', '[0-9]+');
                Route::post('forms/{form}/archive', [CustomFormController::class, 'archive'])->name('forms.archive')->where('form', '[0-9]+');
                Route::post('forms/{form}/unarchive', [CustomFormController::class, 'unarchive'])->name('forms.unarchive')->where('form', '[0-9]+');
                Route::post('forms/{form}/duplicate', [CustomFormController::class, 'duplicate'])->name('forms.duplicate')->where('form', '[0-9]+');
                Route::post('forms/{form}/reset', [CustomFormController::class, 'resetSubmissions'])->name('forms.reset')->where('form', '[0-9]+');
                Route::get('forms/{form}/report', [CustomFormController::class, 'showReport'])->name('forms.report')->where('form', '[0-9]+');
                Route::get('forms/{form}/report/pdf', [CustomFormController::class, 'exportPdf'])->name('forms.reportPdf')->where('form', '[0-9]+');
                Route::get('forms/{form}/submissions/{submission}/pdf', [CustomFormController::class, 'exportSubmissionPdf'])->name('forms.submissionPdf')->where('form', '[0-9]+');
                Route::get('forms/{form}/submissions/{submission}/csv', [CustomFormController::class, 'exportSubmissionCsv'])->name('forms.submissionCsv')->where('form', '[0-9]+');
                Route::get('forms/{form}/report/csv', [CustomFormController::class, 'exportCsv'])->name('forms.csv')->where('form', '[0-9]+');
                Route::post('forms/{id}/restore', [CustomFormController::class, 'restore'])->name('forms.restore')->where('id', '[0-9]+');
                Route::resource('forms', CustomFormController::class)->where(['form' => '[0-9]+']);
            });

            // Helpline Calls & Volunteers Management
            Route::prefix('helpline')->name('helpline.')->group(function () {
                Route::get('/', [HelplineDashboardController::class, 'index'])->name('index');
                Route::get('/data', [HelplineDashboardController::class, 'data'])->name('data');
                Route::get('/export/excel', [HelplineDashboardController::class, 'exportExcel'])->name('export.excel');
                Route::get('/export/pdf', [HelplineDashboardController::class, 'exportPdf'])->name('export.pdf');
                Route::post('/sync-workgroup', [HelplineDashboardController::class, 'syncWorkgroupReport'])->name('sync.workgroup');

                // Call Actions
                Route::post('/calls/{call}/toggle-discuss', [HelplineDashboardController::class, 'toggleDiscuss'])->name('calls.toggle-discuss');
                Route::delete('/calls/{call}', [HelplineDashboardController::class, 'destroyCall'])->name('calls.destroy');

                // Volunteers Management
                Route::get('/volunteers', [HelplineDashboardController::class, 'volunteers'])->name('volunteers');
                Route::post('/volunteers', [HelplineDashboardController::class, 'storeVolunteer'])->name('volunteers.store');
                Route::put('/volunteers/{id}', [HelplineDashboardController::class, 'updateVolunteer'])->name('volunteers.update');
                Route::post('/volunteers/{id}/toggle', [HelplineDashboardController::class, 'toggleVolunteerStatus'])->name('volunteers.toggle');
                Route::delete('/volunteers/{id}', [HelplineDashboardController::class, 'deleteVolunteer'])->name('volunteers.destroy');
            });

            // WhatsApp Automation & Communication Module
            Route::prefix('whatsapp')->name('whatsapp.')->group(function () {
                // Device & Connection Status + Broadcasts
                Route::group(['middleware' => ['permission:manage whatsapp']], function () {
                    Route::get('/device', [WhatsAppDeviceController::class, 'status'])->name('device.status');
                    Route::post('/device', [WhatsAppDeviceController::class, 'store'])->name('device.store');
                    Route::delete('/device/{deviceId}', [WhatsAppDeviceController::class, 'destroy'])->name('device.destroy');
                    Route::get('/device/qr', [WhatsAppDeviceController::class, 'qr'])->name('device.qr');
                    Route::get('/device/check', [WhatsAppDeviceController::class, 'check'])->name('device.check');
                    Route::post('/device/reconnect', [WhatsAppDeviceController::class, 'reconnect'])->name('device.reconnect');
                    Route::post('/device/logout', [WhatsAppDeviceController::class, 'logout'])->name('device.logout');
                    Route::post('/device/test', [WhatsAppDeviceController::class, 'testMessage'])->name('device.test');

                    Route::get('/subscribers', [WhatsAppSubscriberController::class, 'index'])->name('subscribers.index');
                    Route::get('/subscribers/sample-csv', [WhatsAppSubscriberController::class, 'sampleCsv'])->name('subscribers.sample-csv');
                    Route::post('/subscribers/bulk-csv', [WhatsAppSubscriberController::class, 'bulkCsv'])->name('subscribers.bulk-csv');
                    Route::get('/broadcasts/{broadcast}/progress', [WhatsAppSubscriberController::class, 'broadcastProgress'])->name('broadcasts.progress');
                    Route::post('/broadcasts/{broadcast}/cancel', [WhatsAppSubscriberController::class, 'cancelBroadcast'])->name('broadcasts.cancel');
                    Route::post('/broadcasts/{broadcast}/resume', [WhatsAppSubscriberController::class, 'resumeBroadcast'])->name('broadcasts.resume');
                    Route::post('/convention-attendees/upload', [WhatsAppSubscriberController::class, 'uploadConventionAttendeesCsv'])->name('convention-attendees.upload');
                    Route::post('/subscribers/broadcast', [WhatsAppSubscriberController::class, 'triggerBroadcast'])->name('subscribers.broadcast');
                });

                // Live Chat Inbox & Reports
                Route::group(['middleware' => ['permission:view whatsapp chats']], function () {
                    Route::get('/inbox', [WhatsAppInboxController::class, 'index'])->name('inbox');
                    Route::get('/inbox/conversations', [WhatsAppInboxController::class, 'conversations'])->name('inbox.conversations');
                    Route::get('/inbox/{conversation}/messages', [WhatsAppInboxController::class, 'messages'])->name('inbox.messages');
                    Route::post('/inbox/{conversation}/send', [WhatsAppInboxController::class, 'send'])->name('inbox.send');
                    Route::post('/inbox/{conversation}/toggle-live-agent', [WhatsAppInboxController::class, 'toggleLiveAgent'])->name('inbox.toggle-live-agent');
                    Route::post('/inbox/{conversation}/read', [WhatsAppInboxController::class, 'markRead'])->name('inbox.read');
                    Route::post('/inbox/{conversation}/notes', [WhatsAppInboxController::class, 'updateNotes'])->name('inbox.notes');
                    Route::post('/inbox/{conversation}/toggle-subscription', [WhatsAppInboxController::class, 'toggleSubscription'])->name('inbox.toggle-subscription');

                    Route::get('/reports', [WhatsAppReportController::class, 'index'])->name('reports.index');
                    Route::get('/reports/export', [WhatsAppReportController::class, 'export'])->name('reports.export');
                });

                // User Manual & Technical Documentation
                Route::get('/docs', [WhatsAppDocsController::class, 'index'])->name('docs');
            });

            // Committee Reports:
            Route::get('committee-reports/logs', [CommitteeReportController::class, 'logs'])->name('committee-reports.logs');
            Route::get('committee-reports/archive', [CommitteeReportArchiveController::class, 'archive'])->name('committee-reports.archive');
            Route::get('committee-reports/archive/sync-status', [CommitteeReportArchiveController::class, 'archiveSyncStatus'])->name('committee-reports.archive.syncStatus');
            Route::post('committee-reports/archive/sync', [CommitteeReportArchiveController::class, 'triggerArchiveSync'])->name('committee-reports.archive.sync');
            Route::get('committee-reports/archive/onedrive-connect', [CommitteeReportArchiveController::class, 'connectOneDrive'])->name('committee-reports.archive.onedriveConnect');
            Route::post('committee-reports/archive/onedrive-token', [CommitteeReportArchiveController::class, 'saveOneDriveToken'])->name('committee-reports.archive.onedriveToken');
            Route::post('committee-reports/archive/download-zip', [CommitteeReportArchiveController::class, 'downloadArchiveZip'])->name('committee-reports.archive.downloadZip');
            Route::post('committee-reports/archive/upload', [CommitteeReportArchiveController::class, 'uploadArchiveFiles'])->name('committee-reports.archive.upload');
            Route::post('committee-reports/archive/folder', [CommitteeReportArchiveController::class, 'createArchiveFolder'])->name('committee-reports.archive.createFolder');
            Route::post('committee-reports/archive/rename', [CommitteeReportArchiveController::class, 'renameArchiveItem'])->name('committee-reports.archive.rename');
            Route::delete('committee-reports/archive/item', [CommitteeReportArchiveController::class, 'deleteArchiveItem'])->name('committee-reports.archive.delete');
            Route::get('committee-reports/storagebox/download', [CommitteeReportArchiveController::class, 'downloadStorageboxFile'])->name('committee-reports.downloadStorageboxFile');
            Route::get('committee-reports/attachments/{id}', [CommitteeReportController::class, 'downloadAttachment'])->name('committee-reports.downloadAttachment');
            Route::delete('committee-reports/attachments/{id}', [CommitteeReportController::class, 'deleteAttachment'])->name('committee-reports.deleteAttachment');
            Route::resource('committee-reports', CommitteeReportController::class);
            Route::post('committee-reports/{id}/restore', [CommitteeReportController::class, 'restore'])->name('committee-reports.restore');
            Route::get('committee-reports/{id}/pdf', [CommitteeReportController::class, 'pdf'])->name('committee-reports.pdf');
            Route::post('committee-reports/export', [CommitteeReportController::class, 'exportReportsPdf'])->name('committee-reports.exportPdf');
            Route::post('committee-reports/{id}/send', [CommitteeReportController::class, 'send'])->name('committee-reports.send');
            Route::post('committee-reports/{id}/approve-and-send', [CommitteeReportController::class, 'approveAndSend'])->name('committee-reports.approveAndSend');
            Route::post('committee-reports/{id}/return-to-draft', [CommitteeReportController::class, 'returnToDraft'])->name('committee-reports.returnToDraft');

            // Service Body Agendas:
            Route::get('service-body-agendas/archive', [ServiceBodyAgendaController::class, 'archive'])->name('service-body-agendas.archive');
            Route::get('service-body-agendas/attachments/{id}', [ServiceBodyAgendaController::class, 'downloadAttachment'])->name('service-body-agendas.downloadAttachment');
            Route::delete('service-body-agendas/attachments/{id}', [ServiceBodyAgendaController::class, 'deleteAttachment'])->name('service-body-agendas.deleteAttachment');
            Route::resource('service-body-agendas', ServiceBodyAgendaController::class);
            Route::post('service-body-agendas/{id}/restore', [ServiceBodyAgendaController::class, 'restore'])->name('service-body-agendas.restore');
            Route::get('service-body-agendas/{id}/pdf', [ServiceBodyAgendaController::class, 'pdf'])->name('service-body-agendas.pdf');
            Route::post('service-body-agendas/{id}/approve', [ServiceBodyAgendaController::class, 'approve'])->name('service-body-agendas.approve');
            Route::post('service-body-agendas/{id}/return-to-draft', [ServiceBodyAgendaController::class, 'returnToDraft'])->name('service-body-agendas.returnToDraft');

            // Calendar
            Route::get('/calendar', YearlyCalendar::class)->name('calendar.index');
            Route::post('/web-calendar-events', [CalendarEventController::class, 'store'])->name('web-calendar-events.store');
            Route::put('/web-calendar-events/{calendarEvent}', [CalendarEventController::class, 'update'])->name('web-calendar-events.update');
            Route::delete('/web-calendar-events/{calendarEvent}', [CalendarEventController::class, 'destroy'])->name('web-calendar-events.destroy');
            Route::post('/web-calendar-events/{id}/restore', [CalendarEventController::class, 'restore'])->name('web-calendar-events.restore');

            // Change Requests:
            Route::get('change-requests', [ChangeRequestController::class, 'index'])->name('change-requests.index');
            Route::get('change-requests/create', [ChangeRequestController::class, 'create'])->name('change-requests.create');
            Route::post('change-requests', [ChangeRequestController::class, 'store'])->name('change-requests.store');
            Route::get('change-requests/{id}', [ChangeRequestController::class, 'show'])->name('change-requests.show');
            Route::patch('change-requests/{id}/status', [ChangeRequestController::class, 'updateStatus'])->name('change-requests.update-status');
            Route::get('change-requests/{id}/download', [ChangeRequestController::class, 'downloadAttachment'])->name('change-requests.download-attachment');
        });

        // Logout:
        Route::post('/logout', [AzureAuthController::class, 'logout'])->name('logout');

        Route::get('/meetings', [MeetingFilterController::class, 'filterMeetings'])->name('frontend.meetings');
        Route::post('/meetings/{meeting}/contact', [MeetingFilterController::class, 'revealMeetingContact'])
            ->middleware('throttle:15,1')
            ->name('meetings.contact');
        Route::post('/meetings/group-contact/{group}', [MeetingFilterController::class, 'revealGroupContact'])
            ->middleware('throttle:15,1')
            ->name('meetings.group.contact');
        Route::get('/export-meetings-pdf', [MeetingFilterController::class, 'exportMeetingsToPDF'])->name('exportMeetingsToPDF');
        Route::get('/export-meetings-csv', [MeetingFilterController::class, 'exportMeetingsToCSV'])->name('exportMeetingsToCSV');

        // Meeting Export Routes:
        Route::get('/meetings/export-print', [MeetingExportController::class, 'wizard'])->name('meetings.export.wizard');
        Route::get('/meetings/export-print/download', [MeetingExportController::class, 'download'])->name('meetings.export.download');

        // Searches:
        Route::get('/searches/city/{id}/groups', [SearchController::class, 'city'])->name('searches.city');
        Route::get('/group/{id}/meetings', [SearchController::class, 'groupMeetings'])->name('searches.meeting');

        Route::post('/subscribers/subscriber', [SubscriberController::class, 'storeFrontend'])->name('subscribers.store');

        // Frontend:
        Route::get('/', function () {
            $homeStats = Cache::remember('frontend_home_stats', 3600, function () {
                return [
                    'weekly_meetings' => Meeting::notMonthlyRecurrent()->inPersonOnly()->count(),
                    'groups' => Group::inPersonOnly()->count(),
                    'governorates' => City::count(),
                ];
            });

            $currentLocale = LaravelLocalization::getCurrentLocale() ?: app()->getLocale() ?: 'ar';
            $jftReading = Cache::remember('frontend_jft_reading_'.$currentLocale.'_'.date('Y_m_d'), 86400, function () use ($currentLocale) {
                return app(JftService::class)->getReading(null, $currentLocale);
            });
            $jftContent = $jftReading['content_html'] ?? '';

            $cities = Cache::remember('frontend_cities_list', 86400, function () {
                return City::select('id', 'ar_name', 'en_name')->orderBy('ar_name')->get();
            });

            $days = Cache::remember('frontend_days_list', 86400, function () {
                return Day::select('id', 'ar_name', 'en_name')->get();
            });

            $previewMeetings = Cache::remember('frontend_preview_meetings', 1800, function () {
                return Meeting::with(['day', 'group.neighborhood.city', 'directOnlineGroup'])
                    ->take(12)
                    ->get();
            });

            $locations = Cache::remember('frontend_autocomplete_locations', 86400, function () {
                $cities = City::select('id', 'ar_name', 'en_name', 'latitude', 'longitude')
                    ->orderBy('ar_name')
                    ->get()
                    ->map(fn ($c) => [
                        'id' => 'city_'.$c->id,
                        'ar_name' => $c->ar_name,
                        'en_name' => $c->en_name ?: $c->ar_name,
                        'type' => 'governorate',
                        'city_name' => $c->ar_name,
                        'lat' => (float) $c->latitude,
                        'lng' => (float) $c->longitude,
                    ]);

                $neighborhoods = Neighborhood::with('city')
                    ->select('id', 'ar_name', 'en_name', 'city_id', 'latitude', 'longitude')
                    ->orderBy('ar_name')
                    ->get()
                    ->map(fn ($n) => [
                        'id' => 'neigh_'.$n->id,
                        'ar_name' => $n->ar_name,
                        'en_name' => $n->en_name ?: $n->ar_name,
                        'type' => 'district',
                        'city_name' => $n->city?->ar_name ?? '',
                        'lat' => (float) ($n->latitude ?: ($n->city?->latitude ?: 0)),
                        'lng' => (float) ($n->longitude ?: ($n->city?->longitude ?: 0)),
                    ]);

                return $cities->concat($neighborhoods)->values();
            });

            $allMeetingsJson = Cache::remember('frontend_all_meetings_json', 1800, function () {
                return Meeting::with([
                    'day',
                    'group.neighborhood.city',
                    'directOnlineGroup',
                    'options',
                    'topics',
                ])->notMonthlyRecurrent()->get()->map(function ($m) {
                    $isOnline = ! empty($m->direct_online_group_id);
                    $group = $isOnline ? $m->directOnlineGroup : $m->group;
                    $city = $isOnline ? null : $group?->neighborhood?->city;
                    $neighborhood = $isOnline ? null : $group?->neighborhood;

                    $lat = null;
                    $lng = null;
                    if (! $isOnline) {
                        $lat = (float) ($neighborhood?->latitude ?: ($city?->latitude ?: null));
                        $lng = (float) ($neighborhood?->longitude ?: ($city?->longitude ?: null));
                    }

                    $tags = [];
                    if ($m->type === 'open') {
                        $tags[] = 'مفتوح';
                    } elseif ($m->type === 'closed') {
                        $tags[] = 'مغلق';
                    }
                    foreach ($m->topics as $top) {
                        $tags[] = $top->ar_name;
                    }
                    foreach ($m->options as $opt) {
                        $tags[] = $opt->ar_name;
                    }

                    $mapUrl = null;
                    if (! $isOnline) {
                        if (! empty($group?->location) && (str_contains($group->location, 'http://') || str_contains($group->location, 'https://'))) {
                            $mapUrl = $group->location;
                        } elseif ($lat && $lng) {
                            $mapUrl = "https://www.google.com/maps/search/?api=1&query={$lat},{$lng}";
                        }
                    }

                    $startTime = '19:00';
                    $timeFormatted = '07:00 PM';
                    if (! empty($m->start_time)) {
                        try {
                            $parsedTime = Carbon::parse($m->start_time);
                            $startTime = $parsedTime->format('H:i');
                            $timeFormatted = $parsedTime->format('h:i A');
                        } catch (Throwable $e) {
                            $startTime = '19:00';
                            $timeFormatted = (string) $m->start_time;
                        }
                    }

                    return [
                        'id' => $m->id,
                        'name' => $group?->ar_name ?? 'اجتماع زمالة',
                        'name_en' => $group?->en_name ?: ($group?->ar_name ?? 'NA Meeting'),
                        'is_online' => $isOnline,
                        'day_id' => $m->day_id,
                        'day_ar' => $m->day?->ar_name ?? '',
                        'day_en' => $m->day?->en_name ?? '',
                        'start_time' => $startTime,
                        'time_fmt' => $timeFormatted,
                        'city_ar' => $isOnline ? 'أونلاين (زووم)' : ($city?->ar_name ?? 'مصر'),
                        'city_en' => $isOnline ? 'Online (Zoom)' : ($city?->en_name ?? 'Egypt'),
                        'neighborhood_ar' => $neighborhood?->ar_name ?? '',
                        'neighborhood_en' => $neighborhood?->en_name ?? '',
                        'address' => $isOnline ? 'عبر تطبيق زووم' : ($group?->ar_address ?: ($group?->en_address ?: 'العنوان متاح بالدليل')),
                        'map_url' => $mapUrl,
                        'zoom_url' => $isOnline ? $group?->location : null,
                        'lat' => $lat,
                        'lng' => $lng,
                        'type' => $m->type,
                        'tags' => array_slice(array_unique($tags), 0, 4),
                    ];
                });
            });

            $announcements = Cache::remember('frontend_home_announcements', 1800, function () {
                return CalendarEvent::orderBy('start', 'desc')->take(3)->get();
            });

            $heroEvent = Cache::remember('frontend_home_hero_event', 1800, function () {
                return CalendarEvent::where('is_hero', true)->latest('updated_at')->first();
            });

            return view('frontend.home', compact('homeStats', 'jftContent', 'jftReading', 'cities', 'days', 'previewMeetings', 'announcements', 'heroEvent', 'locations', 'allMeetingsJson'));
        })->name('frontend.home');

        Route::get('/literature', function () {
            return view('frontend.literature');
        })->name('frontend.literature');

        Route::get('/forpublic', function () {
            return view('frontend.forpublic');
        })->name('frontend.forpublic');

        Route::get('/questions', function () {
            return view('frontend.questions');
        })->name('frontend.questions');

        Route::get('/clean-time', function () {
            return view('frontend.cleantime');
        })->name('frontend.cleantime');

        Route::get('/app', function () {
            return view('frontend.downloads');
        })->name('frontend.app');

        Route::get('/events', [FrontendEventController::class, 'index'])->name('frontend.events');
        Route::get('/web-calendar-events', [CalendarEventController::class, 'index'])->name('web-calendar-events.index');

        Route::get('/test', [ForPublicController::class, 'index'])->name('frontend.test');

        Route::get('/test', function () {
            return view('frontend.test');
        })->name('frontend.test');

        Route::get('/speakers', function () {
            return view('frontend.speakers');
        })->name('frontend.speakers');

        Route::get('/fdsurvey', function () {
            $groups = Cache::remember('fdsurvey_groups', 3600, fn () => Group::all());
            $serviceBody = Cache::remember('fdsurvey_service_bodies', 3600, fn () => ServiceBody::all());
            $serviceCommittee = Cache::remember('fdsurvey_service_committees', 3600, fn () => ServiceCommittee::all());

            return view('frontend.fdsurvey', compact('groups', 'serviceBody', 'serviceCommittee'));
        })->name('frontend.fdsurvey');

        Route::get('/committees', [ServiceCommitteeController::class, '__invoke'])->name('frontend.comms');
        Route::get('/service-bodies/map', [ServiceBodyMapController::class, 'index'])->name('frontend.service_bodies.map');
        Route::get('/api/service-bodies/map-data', [ServiceBodyMapController::class, 'apiData'])->name('api.service_bodies.map_data');

        Route::get('/contactus', [ContactUsController::class, 'create'])->name('contactus.create');
        Route::post('/contactus', [ContactUsController::class, 'store'])->name('contactus.store');

        Route::get('/privacy-policy', function () {
            return view('frontend.privacy');
        })->name('privacy.policy');

        // Public Custom Forms
        Route::get('/f/{slug}', [PublicFormController::class, 'show'])->name('forms.show.public');
        Route::post('/f/{slug}', [PublicFormController::class, 'submit'])->name('forms.submit.public');

        // Pre-release APK Download Link Request
        Route::post('/apk/request-link', [ApkDownloadController::class, 'requestLink'])
            ->middleware('throttle:5,1')
            ->name('apk.request_link');

        // Decisions Archive
        Route::get('/decarch', function () {
            return redirect('/decarch');
        });
        Route::get('/decisions-archive', function () {
            return redirect('/decarch');
        });
    }
);

// Public Decisions Archive (Excluded from search engines)
$decarchHandler = function () {
    $filePath = public_path('decarch.html');
    if (! file_exists($filePath)) {
        abort(404);
    }

    return response()->file($filePath, [
        'Content-Type' => 'text/html; charset=UTF-8',
        'X-Robots-Tag' => 'noindex, nofollow, noarchive, nosnippet',
    ]);
};

Route::get('/decarch', $decarchHandler)->name('decarch');
Route::get('/decisions-archive', $decarchHandler)->name('decisions.archive');

// Internal Guidelines & Inventory Documentation (Excluded from search & AI engines)
$guidelinesHandler = function (string $doc) {
    $filePath = public_path("guidelines/{$doc}/index.html");
    if (! file_exists($filePath)) {
        abort(404);
    }

    return response()->file($filePath, [
        'Content-Type' => 'text/html; charset=UTF-8',
        'X-Robots-Tag' => 'noindex, nofollow, noarchive, nosnippet, noimageindex',
    ]);
};

Route::get('/guidelines/inventory', fn () => $guidelinesHandler('inventory'))->name('guidelines.inventory');
Route::get('/guidelines/rsc', fn () => $guidelinesHandler('rsc'))->name('guidelines.rsc');

// Pre-release APK Secret Streaming Download Route (Legacy token-based)
Route::get('/apk/download/{token}', [ApkDownloadController::class, 'download'])
    ->where('token', '[a-zA-Z0-9]{64}')
    ->middleware('throttle:15,1')
    ->name('apk.download');

// Direct & Transparent Android APK Hub Routes (No personal data, immediate download)
Route::get('/apk/direct-download', [ApkDownloadController::class, 'directDownload'])->name('apk.direct_download');
Route::get('/apk/naegypt-v1.2.10-honesty.apk', [ApkDownloadController::class, 'directDownload'])->name('apk.direct_download.file');
Route::get('/apk/naegypt-v1.2.3-hope.apk', [ApkDownloadController::class, 'directDownload']);
Route::get('/apk/naegypt-v1.2.0-hope.apk', [ApkDownloadController::class, 'directDownload']);

// Fixed Public Helpline Form (Clean direct access)
Route::get('/forms/helpline', [HelplinePublicFormController::class, 'show'])->name('forms.helpline.show');
Route::post('/forms/helpline', [HelplinePublicFormController::class, 'submit'])->name('forms.helpline.submit');

Route::get('/sitemap.xml', function () {
    $baseUrl = rtrim(request()->getSchemeAndHttpHost(), '/');
    if (str_contains($baseUrl, '127.0.0.1') || str_contains($baseUrl, 'localhost')) {
        $baseUrl = 'https://naegypt.org';
    }
    $pages = [
        ['loc' => '/', 'changefreq' => 'daily', 'priority' => '1.0'],
        ['loc' => '/meetings', 'changefreq' => 'daily', 'priority' => '0.9'],
        ['loc' => '/events', 'changefreq' => 'weekly', 'priority' => '0.8'],
        ['loc' => '/literature', 'changefreq' => 'monthly', 'priority' => '0.8'],
        ['loc' => '/forpublic', 'changefreq' => 'monthly', 'priority' => '0.7'],
        ['loc' => '/contactus', 'changefreq' => 'monthly', 'priority' => '0.6'],
        ['loc' => '/questions', 'changefreq' => 'monthly', 'priority' => '0.6'],
        ['loc' => '/clean-time', 'changefreq' => 'weekly', 'priority' => '0.85'],
    ];

    $xml = '<?xml version="1.0" encoding="UTF-8"?>';
    $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

    foreach ($pages as $page) {
        $xml .= '<url>';
        $xml .= '<loc>'.htmlspecialchars($baseUrl.$page['loc']).'</loc>';
        $xml .= '<changefreq>'.$page['changefreq'].'</changefreq>';
        $xml .= '<priority>'.$page['priority'].'</priority>';
        $xml .= '</url>';
    }

    $xml .= '</urlset>';

    return response($xml, 200, ['Content-Type' => 'application/xml']);
});
