<?php

namespace App\Providers;

use App\Models\ClinicSetting;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

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
        $clinicName = 'PawCare';

        try {
            $clinicName = ClinicSetting::query()->value('clinic_name') ?: 'PawCare';
        } catch (\Throwable $e) {
        }

        View::share('clinicName', $clinicName);
        config(['app.name' => $clinicName]);
    }
}
