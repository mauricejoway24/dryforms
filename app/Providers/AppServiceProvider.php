<?php

namespace App\Providers;

use App\Models\Company;
use App\Models\Project;
use App\Models\ProjectArea;
use App\Observers\CompanyObserver;
use App\Observers\ProjectAreaObserver;
use App\Observers\ProjectObserver;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);

        Project::observe(ProjectObserver::class);
        ProjectArea::observe(ProjectAreaObserver::class);
        Company::observe(CompanyObserver::class);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {

    }
}
