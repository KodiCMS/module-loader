<?php namespace KodiCMS\ModulesLoader\Providers;

use Illuminate\Support\ServiceProvider;
use KodiCMS\ModulesLoader\ModulesLoader;
use KodiCMS\ModulesLoader\ModulesFileSystem;
use KodiCMS\ModulesLoader\Console\Commands\ModulesList;

class ModuleServiceProvider extends ServiceProvider
{
	/**
	 * Register any application services.
	 *
	 * This service provider is a great spot to register your various container
	 * bindings with the application. As you can see, we are registering our
	 * "Registrar" implementation here. You can add your own bindings too!
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app->singleton('modules.loader', function ()
		{
			return new ModulesLoader(config('app.modules'));
		});

		$this->app->singleton('modules.filesystem', function ($app)
		{
			return new ModulesFileSystem($app['modules.loader'], $app['files']);
		});

		$this->registerConsoleCommand('modules:list', ModulesList::class);
	}

	/**
	 * Registers a new console (artisan) command
	 * @param $key The command name
	 * @param $class The command class
	 * @return void
	 */
	public function registerConsoleCommand($key, $class)
	{
		$key = 'command.' . $key;
		$this->app[$key] = $this->app->share(function ($app) use ($class)
		{
			return $app->make($class);
		});

		$this->commands($key);
	}
}