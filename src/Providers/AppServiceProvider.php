<?php
namespace KodiCMS\ModulesLoader\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
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
        $this->app['modules.loader']->registerModules($this->app);
    }


    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app['modules.loader']->bootModules($this->app);

        $this->app['modules.filesystem']->getFoundFilesFromCache();

        $this->app['events']->listen('app.shutdown', function () {
            $this->app['modules.filesystem']->cacheFoundFiles();
        });
    }
}
