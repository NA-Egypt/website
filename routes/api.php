<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->name('api.v1.')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    })->middleware('auth:sanctum');

    Route::post('/auth/azure/login', [\App\Http\Controllers\Api\AzureAuthController::class, 'login']);
    Route::post('/login/azure', [\App\Http\Controllers\Api\AzureAuthController::class, 'login']);

    // Public composite & content endpoints
    Route::get('/home', [\App\Http\Controllers\Api\HomeController::class, 'index'])->name('home');
    Route::get('/frontpage', [\App\Http\Controllers\Api\HomeController::class, 'index'])->name('frontpage');
    Route::get('/jft', [\App\Http\Controllers\Api\JftController::class, 'show'])->name('jft');
    Route::get('/stats', [\App\Http\Controllers\Api\StatsController::class, 'index'])->name('stats');

    // Sensitive resources requiring authentication for all operations
    $protectedControllers = [
        'committee-reports'     => \App\Http\Controllers\Api\CommitteeReportController::class,
        'contact-requests'      => \App\Http\Controllers\Api\ContactUsController::class,
        'contact-us'            => \App\Http\Controllers\Api\ContactUsController::class,
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
    ];

    foreach ($protectedControllers as $uri => $controller) {
        Route::apiResource($uri, $controller)->middleware('auth:sanctum');
    }

    foreach ($publicControllers as $uri => $controller) {
        Route::apiResource($uri, $controller)->only(['index', 'show']);
        Route::apiResource($uri, $controller)->except(['index', 'show'])->middleware('auth:sanctum');
    }
});
