<?php

namespace App\Providers;

use App\Models\Address;
use App\Models\Store;
use App\Models\User;
use App\Policies\AddressPolicy;
use App\Policies\StorePolicy;
use App\Policies\UserPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
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
        Gate::policy(User::class, UserPolicy::class);
        Gate::policy(Address::class, AddressPolicy::class);
        Gate::policy(Store::class, StorePolicy::class);
    }
}
