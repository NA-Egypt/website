<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/login/azure', [\App\Http\Controllers\Api\AzureAuthController::class, 'login']);

$controllers = [
    'calendar-events' => \App\Http\Controllers\Api\CalendarEventController::class,
    'cities' => \App\Http\Controllers\Api\CityController::class,
    'committee-reports' => \App\Http\Controllers\Api\CommitteeReportController::class,
    'contact-us' => \App\Http\Controllers\Api\ContactUsController::class,
    'days' => \App\Http\Controllers\Api\DayController::class,
    'events' => \App\Http\Controllers\Api\EventController::class,
    'groups' => \App\Http\Controllers\Api\GroupController::class,
    'meetings' => \App\Http\Controllers\Api\MeetingController::class,
    'neighborhoods' => \App\Http\Controllers\Api\NeighborhoodController::class,
    'newsletter-members' => \App\Http\Controllers\Api\NewsletterMemberController::class,
    'options' => \App\Http\Controllers\Api\OptionController::class,
    'permissions' => \App\Http\Controllers\Api\PermissionController::class,
    'roles' => \App\Http\Controllers\Api\RoleController::class,
    'sc-meetings' => \App\Http\Controllers\Api\ScMeetingController::class,
    'service-bodies' => \App\Http\Controllers\Api\ServiceBodyController::class,
    'service-committees' => \App\Http\Controllers\Api\ServiceCommitteeController::class,
    'topics' => \App\Http\Controllers\Api\TopicController::class,
    'transactions' => \App\Http\Controllers\Api\TransactionController::class,
    'users' => \App\Http\Controllers\Api\UserController::class,
];

foreach ($controllers as $uri => $controller) {
    Route::apiResource($uri, $controller)->only(['index', 'show']);
    Route::apiResource($uri, $controller)->except(['index', 'show'])->middleware('auth:sanctum');
}
