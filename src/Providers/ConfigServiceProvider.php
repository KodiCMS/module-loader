<?php namespace KodiCMS\ModulesLoader\Providers;

use Event;
use Config;
use ModulesLoader;

class ConfigServiceProvider extends ServiceProvider {

	public function boot()
	{
		foreach (ModulesLoader::getRegisteredModules() as $module)
		{
			$config = $module->loadConfig();
			foreach($config as $group => $data)
			{
				Config::set($group, $data);
			}
		}

		Event::fire('config.loaded');
	}

	public function register(){}
}
