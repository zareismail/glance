<?php

namespace Zareismail\Glance;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route; 
use Laravel\Nova\Nova;
use Zareismail\Glance\Http\Middleware\Authorize;

class GlanceServiceProvider extends ServiceProvider 
{  
    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        $this->routes();
        Nova::serving([$this, 'servingNova']);
    }

    /**
     * Register the tool's routes.
     *
     * @return void
     */
    protected function routes()
    {
        if ($this->app->routesAreCached()) {
            return;
        } 

        Route::middleware(['nova']) 
                ->get('/nova-api/dashboards/{dashboard}/filters', Http\Controllers\DashboardFilterController::class.'@index');
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    { 
    }  

    /**
     * Register any nova services.
     *
     * @return void
     */
    public function servingNova($value='')
    {
        Nova::script('zareismail-glance', __DIR__.'/../dist/js/tool.js');
        Nova::style('zareismail-glance', __DIR__.'/../dist/css/tool.css');
    }
}
