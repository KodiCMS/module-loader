<?php namespace KodiCMS\ModuleLoader\Providers;

use ModuleLoader;
use Illuminate\Routing\Router;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as BaseRouteServiceProvider;

class RouteServiceProvider extends BaseRouteServiceProvider
{
	/**
	 * Define your route model bindings, pattern filters, etc.
	 *
	 * @param  \Illuminate\Routing\Router $router
	 * @return void
	 */
	public function boot(Router $router)
	{
		parent::boot($router);

	}

	/**
	 * Define the routes for the application.
	 *
	 * @param  \Illuminate\Routing\Router $router
	 * @return void
	 */
	public function map(Router $router)
	{
		foreach (ModuleLoader::getRegisteredModules() as $module)
		{
			$this->app->call([$module, 'loadRoutes'], [$router]);
		}
	}
}
