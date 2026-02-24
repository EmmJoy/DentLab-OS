<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Patient;
use Illuminate\Support\Facades\Route;

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
        // Explicit route model binding for Patient model using Predict3DId
        Route::bind('patient', function ($value) {
            return Patient::where('Predict3DId', $value)->firstOrFail();
        });
    }
}