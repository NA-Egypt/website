<?php

namespace App\Providers;

use App\Models\CalendarEvent;
use App\Models\City;
use App\Models\CommitteeReport;
use App\Models\CustomForm;
use App\Models\DirectOnlineGroup;
use App\Models\Event;
use App\Models\Group;
use App\Models\InventoryItem;
use App\Models\Meeting;
use App\Models\Neighborhood;
use App\Models\ScMeeting;
use App\Models\ServiceBody;
use App\Models\ServiceBodyAgenda;
use App\Models\ServiceCommittee;
use App\Models\Topic;
use App\Models\User;
use App\Observers\GenericObserver;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use Mcamara\LaravelLocalization\Traits\LoadsTranslatedCachedRoutes;

class AppServiceProvider extends ServiceProvider
{
    use LoadsTranslatedCachedRoutes;

    /**
     * Register any application services.
     */
    public function register(): void {}

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
        User::observe(GenericObserver::class);
        Topic::observe(GenericObserver::class);
        ServiceCommittee::observe(GenericObserver::class);
        Event::observe(GenericObserver::class);
        CommitteeReport::observe(GenericObserver::class);
        ScMeeting::observe(GenericObserver::class);
        CalendarEvent::observe(GenericObserver::class);
        DirectOnlineGroup::observe(GenericObserver::class);
        CustomForm::observe(GenericObserver::class);
        InventoryItem::observe(GenericObserver::class);
        ServiceBodyAgenda::observe(GenericObserver::class);

        // Define the 'is-super-admin' gate
        Gate::define('is-super-admin', function (User $user) {
            return $user->hasRole('super admin');
        });

        // Implicitly grant "Super Admin" role all permissions
        Gate::before(function ($user, $ability) {
            return $user->hasRole('super admin') ? true : null;
        });

        Paginator::useBootstrapFive();
        RouteServiceProvider::loadCachedRoutesUsing(fn () => $this->loadCachedRoutes());

        if (env('APP_ENV') === 'production' || app()->environment('production')) {
            URL::forceScheme('https');
        }

        Livewire::setUpdateRoute(function ($handle) {
            Route::match(['get', 'post'], '/ar/livewire/update', $handle)->middleware('web');
            Route::match(['get', 'post'], '/en/livewire/update', $handle)->middleware('web');

            return Route::match(['get', 'post'], '/livewire/update', $handle)
                ->middleware('web')
                ->name('livewire.update');
        });
    }
}
