<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->name('api.v1.')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    })->middleware('auth:sanctum');

    Route::post('/auth/azure/login', [\App\Http\Controllers\Api\AzureAuthController::class, 'login']);
    Route::post('/login/azure', [\App\Http\Controllers\Api\AzureAuthController::class, 'login']);
    Route::post('/auth/logout', [\App\Http\Controllers\Api\AzureAuthController::class, 'logout'])->middleware('auth:sanctum');
    Route::post('/logout', [\App\Http\Controllers\Api\AzureAuthController::class, 'logout'])->middleware('auth:sanctum');

    // Public composite & content endpoints
    Route::get('/home', [\App\Http\Controllers\Api\HomeController::class, 'index'])->name('home');
    Route::get('/frontpage', [\App\Http\Controllers\Api\HomeController::class, 'index'])->name('frontpage');
    Route::get('/jft', [\App\Http\Controllers\Api\JftController::class, 'show'])->name('jft');
    Route::get('/stats', [\App\Http\Controllers\Api\StatsController::class, 'index'])->name('stats');

    // Public Custom Form access & submission
    Route::get('/forms/public/{slug}', [\App\Http\Controllers\Api\CustomFormController::class, 'showPublic'])->name('forms.public.show');
    Route::post('/forms/public/{slug}/submit', [\App\Http\Controllers\Api\CustomFormController::class, 'submitPublic'])->name('forms.public.submit');

    // Sensitive resources requiring authentication for all operations
    $protectedControllers = [
        'change-requests'       => \App\Http\Controllers\Api\ChangeRequestController::class,
        'committee-reports'     => \App\Http\Controllers\Api\CommitteeReportController::class,
        'contact-requests'      => \App\Http\Controllers\Api\ContactUsController::class,
        'contact-us'            => \App\Http\Controllers\Api\ContactUsController::class,
        'forms'                 => \App\Http\Controllers\Api\CustomFormController::class,
        'newsletter-members'    => \App\Http\Controllers\Api\NewsletterMemberController::class,
        'permissions'           => \App\Http\Controllers\Api\PermissionController::class,
        'roles'                 => \App\Http\Controllers\Api\RoleController::class,
        'transactions'          => \App\Http\Controllers\Api\TransactionController::class,
        'users'                 => \App\Http\Controllers\Api\UserController::class,
    ];

    // Public resources allowing unauthenticated index/show operations
    $publicControllers = [
        'agendas'               => \App\Http\Controllers\Api\AgendaController::class,
        'calendar-events'       => \App\Http\Controllers\Api\CalendarEventController::class,
        'cities'                => \App\Http\Controllers\Api\CityController::class,
        'days'                  => \App\Http\Controllers\Api\DayController::class,
        'direct-online-groups'  => \App\Http\Controllers\Api\DirectOnlineGroupController::class,
        'events'                => \App\Http\Controllers\Api\EventController::class,
        'groups'                => \App\Http\Controllers\Api\GroupController::class,
        'meetings'              => \App\Http\Controllers\Api\MeetingController::class,
        'neighborhoods'         => \App\Http\Controllers\Api\NeighborhoodController::class,
        'options'               => \App\Http\Controllers\Api\OptionController::class,
        'sc-meetings'           => \App\Http\Controllers\Api\ScMeetingController::class,
        'service-bodies'        => \App\Http\Controllers\Api\ServiceBodyController::class,
        'service-body-agendas'  => \App\Http\Controllers\Api\ServiceBodyAgendaController::class,
        'service-committees'    => \App\Http\Controllers\Api\ServiceCommitteeController::class,
        'topics'                => \App\Http\Controllers\Api\TopicController::class,
        'workgroups'            => \App\Http\Controllers\Api\WorkgroupController::class,
    ];

    // Nested / specific actions for protected controllers
    Route::patch('change-requests/{changeRequest}/status', [\App\Http\Controllers\Api\ChangeRequestController::class, 'updateStatus'])->middleware('auth:sanctum');
    Route::get('forms/{form}/submissions', [\App\Http\Controllers\Api\CustomFormController::class, 'submissions'])->middleware('auth:sanctum');

    foreach ($protectedControllers as $uri => $controller) {
        Route::apiResource($uri, $controller)->middleware('auth:sanctum');
    }

    foreach ($publicControllers as $uri => $controller) {
        Route::apiResource($uri, $controller)->only(['index', 'show']);
        Route::apiResource($uri, $controller)->except(['index', 'show'])->middleware('auth:sanctum');
    }
});
