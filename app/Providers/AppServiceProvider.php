<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Database\Eloquent\Model;
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
        // dont provid protection for update method
        Model::unguard();
        // prevent lazy loading, accecing doe
        Model::shouldBeStrict();
        // solves n+1 problem
        Model::automaticallyEagerLoadRelationships();
    }
}
