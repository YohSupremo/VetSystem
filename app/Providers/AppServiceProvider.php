<?php

namespace App\Providers;

use App\Models\ClinicSetting;
use App\Models\QrScanLog;
use App\Policies\QrScanLogPolicy;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Gate;

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
        // Register the QrScanLog policy
        Gate::policy(QrScanLog::class, QrScanLogPolicy::class);

        $clinicName = 'PawCare';

        try {
            $clinicName = ClinicSetting::query()->value('clinic_name') ?: 'PawCare';
        } catch (\Throwable $e) {
        }

        View::share('clinicName', $clinicName);
        config(['app.name' => $clinicName]);
    }
}
