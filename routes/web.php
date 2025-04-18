<?php

use App\Http\Controllers\CityController;
use App\Http\Controllers\GreatingPagesController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\MeetingController;
use App\Http\Controllers\NeighborhoodController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\ServiceBodyController;
use App\Http\Controllers\TopicController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MeetingFilterController;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use App\Http\Controllers\AzureAuthController;

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

            // Group Routes:
            Route::get('/group',[GroupController::class,'index'])->name('group.index');
            Route::get('/group/create',[GroupController::class,'create'])->name('group.create');
            Route::post('/group',[GroupController::class,'store'])->name('group.store');
            Route::get('/group/show/{group}',[GroupController::class,'show'])->name('group.show');
            Route::get('/group/{group}',[GroupController::class,'edit'])->name('group.edit');
            Route::put('/group/{group}',[GroupController::class,'update'])->name('group.update');
            Route::delete('/group/{group}',[GroupController::class,'destroy'])->name('group.destroy');

            // Meeting Routes:
            Route::get('/meeting',[MeetingController::class,'index'])->name('meeting.index');
            Route::get('/meeting/create',[MeetingController::class,'create'])->name('meeting.create');
            Route::post('/meeting',[MeetingController::class,'store'])->name('meeting.store');
            Route::get('/meeting/{meeting}',[MeetingController::class,'edit'])->name('meeting.edit');
            Route::put('/meeting/{meeting}',[MeetingController::class,'update'])->name('meeting.update');
            Route::delete('/meeting/{meeting}',[MeetingController::class,'destroy'])->name('meeting.destroy');

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
            Route::resource('users', UserController::class)->only(['index', 'edit', 'update']);
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

        // Auth:
        Route::get('/login/microsoft', [AzureAuthController::class, 'redirectToAzure']);
        Route::get('/login/microsoft/callback', [AzureAuthController::class, 'handleAzureCallback']);

        // Logout:
        Route::post('/logout', [AzureAuthController::class, 'logout'])->name('logout');

        // Frontend:
        Route::get('/', function(){
            return view('frontend.home');
        })->name('frontend.home');

//        Route::get('/frontend/meetings', [MeetingFilterController::class, 'filterMeetings'])->name('frontend.meetings');
        Route::get('/meetings', [MeetingFilterController::class, 'filterMeetings'])->name('frontend.meetings');


        // Searches:
        Route::get('/searches/city/{id}/groups', [SearchController::class, 'city'])->name('searches.city');
        Route::get('/group/{id}/meetings', [SearchController::class, 'groupMeetings'])->name('searches.meeting');



    }
);


