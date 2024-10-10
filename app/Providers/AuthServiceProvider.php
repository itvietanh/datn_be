<?php

namespace App\Providers;

// use Illuminate\Support\ServiceProvider;

use Laravel\Passport\Passport;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        'App\Model' => 'App\Policies\Modelpolicy'
    ];
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
        // Passport::routes();
        Passport::tokensExpireIn(now()->addHours(1));
    }
}
