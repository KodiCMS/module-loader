<?php namespace KodiCMS\ModulesLoader;

use Illuminate\Support\Facades\Facade;

class ModulesFileSystemFacade extends Facade {

	/**
	 * Get the registered name of the component.
	 *
	 * @return string
	 */
	protected static function getFacadeAccessor()
	{
		return 'modules.filesystem';
	}

}
