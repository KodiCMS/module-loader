<?php namespace KodiCMS\ModuleLoader;

use Illuminate\Support\Facades\Facade;

/**
 * @see \KodiCMS\CMS\Loader\ModuleLoader
 */
class ModuleLoaderFacade extends Facade {

	/**
	 * Get the registered name of the component.
	 *
	 * @return string
	 */
	protected static function getFacadeAccessor()
	{
		return 'module.loader';
	}

}
