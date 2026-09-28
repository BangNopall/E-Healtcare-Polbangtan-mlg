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

        // Paksa HTTPS untuk seluruh URL dan aset Vite saat diakses melalui SSL / production
        if ($this->app->environment('production') || str_starts_with((string) config('app.url'), 'https://')) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }
        // Model::handleLazyLoadingViolationUsing(function ($model, $relation) {
        //     $class = get_class($model);

        //     info("Attemppt Lazy load to: {$class}::{$relation}.");
        // });
    }
}
