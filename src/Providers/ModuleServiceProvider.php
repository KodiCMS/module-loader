<?php

namespace KodiCMS\ModulesLoader\Providers;

use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;
use KodiCMS\ModulesLoader\ModulesLoader;
use KodiCMS\ModulesLoader\ModulesFileSystem;
use KodiCMS\ModulesLoader\ModulesLoaderFacade;
use KodiCMS\ModulesLoader\ModulesFileSystemFacade;
use KodiCMS\ModulesLoader\Console\Commands\ModulesListCommand;
use KodiCMS\ModulesLoader\Console\Commands\ModulesSeedCommand;
use KodiCMS\ModulesLoader\Console\Commands\ModulesMigrateCommand;
use KodiCMS\ModulesLoader\Console\Commands\ModulesAssetsPublishCommand;

class ModuleServiceProvider extends ServiceProvider
{
    /**
     * Providers to register.
     *
     * @var array
     */
    protected $providers = [
        ConfigServiceProvider::class,
        RouteServiceProvider::class,
        AppServiceProvider::class,
    ];

    /**
     * @var array
     */
    protected $commands = [
        ModulesListCommand::class,
        ModulesMigrateCommand::class,
        ModulesSeedCommand::class,
        ModulesAssetsPublishCommand::class,
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
            return new ModulesLoader(config('app.modules', []));
        });

        $this->app->singleton('modules.filesystem', function ($app) {
            return new ModulesFileSystem($app['modules.loader'], $app['files']);
        });

        $this->registerAliases();
        $this->registerProviders();

        $this->registerConsoleCommands();
    }

    /**
     * Registers console (artisan) commands.
     */
    public function registerConsoleCommands()
    {
        foreach ($this->commands as $command) {
            $this->commands($command);
        }
    }

    /**
     * Register aliases.
     */
    protected function registerAliases()
    {
        AliasLoader::getInstance([
            'ModulesLoader'     => ModulesLoaderFacade::class,
            'ModulesFileSystem' => ModulesFileSystemFacade::class,
        ]);
    }

    /**
     * Register providers.
     */
    protected function registerProviders()
    {
        foreach ($this->providers as $providerClass) {
            $this->app->register($providerClass);
        }
    }
}
