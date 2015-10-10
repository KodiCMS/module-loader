<?php
namespace KodiCMS\ModulesLoader\Providers;

use Illuminate\Support\ServiceProvider;

class ConfigServiceProvider extends ServiceProvider
{

    public function boot()
    {
        foreach ($this->app['modules.loader']->getRegisteredModules() as $module) {
            $config = $module->loadConfig();
            foreach ($config as $group => $data) {
                $this->app['config']->set($group, $data);
            }
        }

        $this->app['events']->fire('config.loaded');
    }


    public function register()
    {

    }
}
