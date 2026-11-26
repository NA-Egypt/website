<?php

use App\Http\Controllers\Api\AgendaController;
use App\Http\Controllers\Api\AzureAuthController;
use App\Http\Controllers\Api\CalendarEventController;
use App\Http\Controllers\Api\ChangeRequestController;
use App\Http\Controllers\Api\CityController;
use App\Http\Controllers\Api\CommitteeReportController;
use App\Http\Controllers\Api\ContactUsController;
use App\Http\Controllers\Api\CustomFormController;
use App\Http\Controllers\Api\DayController;
use App\Http\Controllers\Api\DirectOnlineGroupController;
use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Api\GroupController;
use App\Http\Controllers\Api\HelplineCallApiController;
use App\Http\Controllers\Api\HomeController;
use App\Http\Controllers\Api\JftController;
use App\Http\Controllers\Api\MeController;
use App\Http\Controllers\Api\MeetingController;
use App\Http\Controllers\Api\NeighborhoodController;
use App\Http\Controllers\Api\NewsletterMemberController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\OptionController;
use App\Http\Controllers\Api\PermissionController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\ScMeetingController;
use App\Http\Controllers\Api\ServiceBodyAgendaController;
use App\Http\Controllers\Api\ServiceBodyController;
use App\Http\Controllers\Api\ServiceCommitteeController;
use App\Http\Controllers\Api\StatsController;
use App\Http\Controllers\Api\TopicController;
use App\Http\Controllers\Api\TransactionController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\WhatsAppWebhookController;
use App\Http\Controllers\Api\WorkgroupController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->name('api.v1.')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    })->middleware('auth:sanctum');

    // Notifications & Profile (Authenticated)
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/me', [MeController::class, 'show'])->name('me');
        Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
        Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
        Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read_all');
    });

    Route::post('/auth/azure/login', [AzureAuthController::class, 'login']);
    Route::post('/login/azure', [AzureAuthController::class, 'login']);
    Route::post('/auth/logout', [AzureAuthController::class, 'logout'])->middleware('auth:sanctum');
    Route::post('/logout', [AzureAuthController::class, 'logout'])->middleware('auth:sanctum');

    // Public composite & content endpoints
    Route::get('/home', [HomeController::class, 'index'])->name('home');
    Route::get('/frontpage', [HomeController::class, 'index'])->name('frontpage');
    Route::get('/jft', [JftController::class, 'show'])->name('jft');
    Route::get('/stats', [StatsController::class, 'index'])->name('stats');

    // Public Custom Form access & submission
    Route::get('/forms/public/{slug}', [CustomFormController::class, 'showPublic'])->name('forms.public.show');
    Route::post('/forms/public/{slug}/submit', [CustomFormController::class, 'submitPublic'])->name('forms.public.submit');

    // Public Helpline Form Schema & Submission
    Route::get('/helpline-calls/schema', [HelplineCallApiController::class, 'schema'])->name('helpline-calls.schema');
    Route::post('/helpline-calls', [HelplineCallApiController::class, 'store'])->name('helpline-calls.store');
    Route::get('/helpline-calls', [HelplineCallApiController::class, 'index'])->middleware('auth:sanctum')->name('helpline-calls.index');

    // WhatsApp Microservice Inbound Webhook
    Route::post('/whatsapp/webhook', [WhatsAppWebhookController::class, 'handle'])->name('whatsapp.webhook');

    // Sensitive resources requiring authentication for all operations
    $protectedControllers = [
        'change-requests' => ChangeRequestController::class,
        'committee-reports' => CommitteeReportController::class,
        'contact-requests' => ContactUsController::class,
        'contact-us' => ContactUsController::class,
        'forms' => CustomFormController::class,
        'newsletter-members' => NewsletterMemberController::class,
        'permissions' => PermissionController::class,
        'roles' => RoleController::class,
        'transactions' => TransactionController::class,
        'users' => UserController::class,
    ];

    // Public resources allowing unauthenticated index/show operations
    $publicControllers = [
        'agendas' => AgendaController::class,
        'calendar-events' => CalendarEventController::class,
        'cities' => CityController::class,
        'days' => DayController::class,
        'direct-online-groups' => DirectOnlineGroupController::class,
        'events' => EventController::class,
        'groups' => GroupController::class,
        'meetings' => MeetingController::class,
        'neighborhoods' => NeighborhoodController::class,
        'options' => OptionController::class,
        'sc-meetings' => ScMeetingController::class,
        'service-bodies' => ServiceBodyController::class,
        'service-body-agendas' => ServiceBodyAgendaController::class,
        'service-committees' => ServiceCommitteeController::class,
        'topics' => TopicController::class,
        'workgroups' => WorkgroupController::class,
    ];

    // Nested / specific actions for protected controllers
    Route::patch('change-requests/{changeRequest}/status', [ChangeRequestController::class, 'updateStatus'])->middleware('auth:sanctum');
    Route::get('forms/{form}/submissions', [CustomFormController::class, 'submissions'])->middleware('auth:sanctum');

    foreach ($protectedControllers as $uri => $controller) {
        Route::apiResource($uri, $controller)->middleware('auth:sanctum');
    }

    foreach ($publicControllers as $uri => $controller) {
        Route::apiResource($uri, $controller)->only(['index', 'show']);
        Route::apiResource($uri, $controller)->except(['index', 'show'])->middleware('auth:sanctum');
    }
});
