<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use SocialiteProviders\Manager\ServiceProvider as SocialiteProvider;
use SocialiteProviders\Manager\SocialiteWasCalled;
use SocialiteProviders\Azure\AzureExtendSocialite;
use SocialiteProviders\Manager\Contracts\Helpers\ConfigRetrieverInterface;

class SocialiteAzureServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Register any application services.
    }

    public function boot(): void
    {
        $socialite = $this->app->make('Laravel\Socialite\Contracts\Factory');
        $configRetriever = $this->app->make(ConfigRetrieverInterface::class);

        (new AzureExtendSocialite())->handle(new SocialiteWasCalled(
            $this->app,
            $configRetriever,
            $socialite
        ));
    }
}
