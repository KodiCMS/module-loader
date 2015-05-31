<?php namespace KodiCMS\ModuleLoader\Providers;

use ModuleLoader;
use Illuminate\Support\ServiceProvider;

class ConfigServiceProvider extends ServiceProvider {

	public function boot()
	{
		/**
		 * Загрузка конфигов модулей
		 */
		foreach (ModuleLoader::getRegisteredModules() as $module)
		{
			$config = $module->loadConfig();
			foreach($config as $group => $data)
			{
				app('config')->set($group, $data);
			}
		}
	}

	public function register(){}
}
