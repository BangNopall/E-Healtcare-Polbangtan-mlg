<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;
use Carbon\Carbon;

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
        config(['app.locale' => 'id']);
        Carbon::setLocale('id');
        date_default_timezone_set('Asia/Jakarta');
        // Model::preventLazyLoading(!$this->app->isProduction());
        Model::preventLazyLoading(true);
        // Model::handleLazyLoadingViolationUsing(function ($model, $relation) {
        //     $class = get_class($model);

        //     info("Attemppt Lazy load to: {$class}::{$relation}.");
        // });
    }
}
