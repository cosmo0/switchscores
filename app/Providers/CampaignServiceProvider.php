<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\Services\CampaignService;

class CampaignServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('Services\CampaignService', function($app) {
            return new CampaignService();
        });
    }
}
