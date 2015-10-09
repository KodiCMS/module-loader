<?php
namespace KodiCMS\ModulesLoader\Providers;

use App;
use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\AliasLoader;
use KodiCMS\ModulesLoader\Console\Commands\ModulesList;
use KodiCMS\ModulesLoader\Console\Commands\ModulesSeedCommand;
use KodiCMS\ModulesLoader\ModulesLoader as ModulesLoaderClass;
use KodiCMS\ModulesLoader\Console\Commands\ModulesMigrateCommand;
use KodiCMS\ModulesLoader\ModulesFileSystem as ModulesFileSystemClass;

class ModuleServiceProvider extends ServiceProvider
{

    /**
     * Providers to register
     * @var array
     */
    protected $providers = [
        RouteServiceProvider::class,
        AppServiceProvider::class,
        ConfigServiceProvider::class,
    ];


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
        $this->app->singleton('modules.loader', function () {
            return new ModulesLoaderClass(config('app.modules', []));
        });

        $this->app->singleton('modules.filesystem', function ($app) {
            return new ModulesFileSystemClass($app['modules.loader'], $app['files']);
        });

        $this->registerAliases();
        $this->registerProviders();

        $this->registerConsoleCommand('modules:list', ModulesList::class);
        $this->registerConsoleCommand('modules:migrate', ModulesMigrateCommand::class);
        $this->registerConsoleCommand('modules:seed', ModulesSeedCommand::class);
    }


    /**
     * Registers a new console (artisan) command
     *
     * @param $key   The command name
     * @param $class The command class
     *
     * @return void
     */
    public function registerConsoleCommand($key, $class)
    {
        $key             = 'command.' . $key;
        $this->app[$key] = $this->app->share(function ($app) use ($class) {
            return $app->make($class);
        });

        $this->commands($key);
    }


    /**
     * Register aliases
     */
    protected function registerAliases()
    {
        AliasLoader::getInstance([
            'ModulesLoader'     => \KodiCMS\ModulesLoader\ModulesLoaderFacade::class,
            'ModulesFileSystem' => \KodiCMS\ModulesLoader\ModulesFileSystemFacade::class,
        ]);
    }


    /**
     * Register providers
     */
    protected function registerProviders()
    {
        foreach ($this->providers as $providerClass) {
            $this->app->register($providerClass);
        }
    }
}