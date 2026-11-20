<?php

namespace App\Providers;

use App\Models\Forward;
use App\Models\StudentReport;
use App\Policies\ForwardPolicy;
use App\Policies\StudentReportPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Gate::policy(StudentReport::class, StudentReportPolicy::class);
        Gate::policy(Forward::class, ForwardPolicy::class);
    }
}
