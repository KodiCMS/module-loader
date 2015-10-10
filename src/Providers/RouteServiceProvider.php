<?php
namespace KodiCMS\ModulesLoader\Providers;

use Illuminate\Routing\Router;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as BaseRouteServiceProvider;

class RouteServiceProvider extends BaseRouteServiceProvider
{
    /**
     * This namespace is applied to the controller routes in your routes file.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';


    /**
     * Load the cached routes for the application.
     *
     * @return void
     */
    protected function loadCachedRoutes()
    {
        $this->app['events']->fire('routes.loading');

        require $this->app->getCachedRoutesPath();

        $this->app['events']->fire('routes.loaded');
    }


    /**
     * Define the routes for the application.
     *
     * @param  \Illuminate\Routing\Router $router
     *
     * @return void
     */
    public function map(Router $router)
    {
        $this->app['events']->fire('routes.loading');

        foreach ($this->app['modules.loader']->getRegisteredModules() as $module) {
            $this->app->call([ $module, 'loadRoutes' ], [ $router ]);
        }

        $this->app['events']->fire('routes.loaded');
    }
}
