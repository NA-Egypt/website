<?php

namespace App\Providers;

use App\Models\City;
use App\Models\Group;
use App\Models\Meeting;
use App\Models\Neighborhood;
use App\Models\ServiceBody;
use App\Models\User;
use App\Observers\GenericObserver;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {

    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Group::observe(GenericObserver::class);
        City::observe(GenericObserver::class);
        ServiceBody::observe(GenericObserver::class);
        Neighborhood::observe(GenericObserver::class);
        Meeting::observe(GenericObserver::class);

        // Define the 'is-super-admin' gate
        Gate::define('is-super-admin', function (User $user) {
            return $user->hasRole('super admin');
        });

        Paginator::useBootstrapFive();

        // Fix Livewire 404 with LaravelLocalization
        Livewire::setUpdateRoute(function ($handle) {
            return Route::post(LaravelLocalization::setLocale() . '/livewire/update', $handle)
                ->middleware(['web', 'localeSessionRedirect', 'localizationRedirect', 'localeViewPath']);
        });
    }
}
