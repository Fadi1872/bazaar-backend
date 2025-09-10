<?php

namespace App\Providers;

use App\Models\Address;
use App\Models\Bazaar;
use App\Models\BazaarCategory;
use App\Models\Comment;
use App\Models\Product;
use App\Models\Store;
use App\Models\StoreCategory;
use App\Models\User;
use App\Policies\AddressPolicy;
use App\Policies\BazaarCategoryPolicy;
use App\Policies\BazaarPolicy;
use App\Policies\CommentPolicy;
use App\Policies\ProductPolicy;
use App\Policies\StoreCategoryPolicy;
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
        Gate::policy(StoreCategory::class, StoreCategoryPolicy::class);
        Gate::policy(Comment::class, CommentPolicy::class);
        Gate::policy(Product::class, ProductPolicy::class);
        Gate::policy(BazaarCategory::class, BazaarCategoryPolicy::class);
        Gate::policy(Bazaar::class, BazaarPolicy::class);
    }
}
