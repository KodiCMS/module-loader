<?php
namespace KodiCMS\ModulesLoader\Providers;

use App;
use Illuminate\Support\ServiceProvider;
use \Illuminate\Foundation\AliasLoader;
use KodiCMS\ModulesLoader\ModulesLoader;
use KodiCMS\ModulesLoader\ModulesFileSystem;
use KodiCMS\ModulesLoader\Console\Commands\ModulesList;
use KodiCMS\ModulesLoader\Console\Commands\ModulesSeedCommand;
use KodiCMS\ModulesLoader\Console\Commands\ModulesMigrateCommand;

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
     * @var array
     */
    protected $initedProviders = [];


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
            return new ModulesLoader(config('app.modules', []));
        });

        $this->app->singleton('modules.filesystem', function ($app) {
            return new ModulesFileSystem($app['modules.loader'], $app['files']);
        });

        $this->registerConsoleCommand('modules:list', ModulesList::class);
        $this->registerConsoleCommand('modules:migrate', ModulesMigrateCommand::class);
        $this->registerConsoleCommand('modules:seed', ModulesSeedCommand::class);

        $loader = AliasLoader::getInstance();

        $loader->alias('ModulesLoader', \KodiCMS\ModulesLoader\ModulesLoaderFacade::class);
        $loader->alias('ModulesFileSystem', \KodiCMS\ModulesLoader\ModulesFileSystemFacade::class);

        $this->registerProviders();
    }


    public function boot()
    {
        foreach ($this->initedProviders as $provider) {
            if (method_exists($provider, 'boot')) {
                $this->app->call([$provider, 'boot']);
            }
        }
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
     * Register providers
     */
    protected function registerProviders()
    {
        foreach ($this->providers as $providerClass) {
            $provider = $this->app->make($providerClass, [$this->app]);
            $provider->register();

            $this->initedProviders[] = $provider;
        }
    }
}