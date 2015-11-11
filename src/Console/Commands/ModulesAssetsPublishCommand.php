<?php

namespace KodiCMS\ModulesLoader\Console\Commands;

use Illuminate\Console\Command;
use League\Flysystem\MountManager;
use Illuminate\Filesystem\Filesystem;
use KodiCMS\ModulesLoader\ModuleContainer;
use League\Flysystem\Filesystem as Flysystem;
use Illuminate\Contracts\Foundation\Application;
use League\Flysystem\Adapter\Local as LocalAdapter;

class ModulesAssetsPublishCommand extends Command
{
    /**
     * The filesystem instance.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;

    /**
     * The console command name.
     */
    protected $name = 'modules:assets:publish';

    /**
     * Execute the console command.
     *
     * @param Application $app
     * @param Filesystem  $files
     */
    public function fire(Application $app, Filesystem $files)
    {
        $this->files = $files;

        /** @var ModuleContainer[] $modules */
        $modules = $app['modules.loader']->getRegisteredModules();

        foreach ($modules as $module) {
            $from = $module->getAssetsPath();
            if ($this->files->isDirectory($from)) {
                $this->publishDirectory($from, $module->getAssetsPublicPath());
            }
        }
    }

    /**
     * Publish the directory to the given directory.
     *
     * @param string $from
     * @param string $to
     *
     * @return void
     */
    protected function publishDirectory($from, $to)
    {
        $manager = new MountManager([
            'from' => new Flysystem(new LocalAdapter($from)),
            'to'   => new Flysystem(new LocalAdapter($to)),
        ]);

        foreach ($manager->listContents('from://', true) as $file) {
            if ($file['type'] === 'file') {
                $manager->put('to://'.$file['path'], $manager->read('from://'.$file['path']));
            }
        }

        $this->status($from, $to, 'Directory');
    }

    /**
     * Write a status message to the console.
     *
     * @param string $from
     * @param string $to
     * @param string $type
     *
     * @return void
     */
    protected function status($from, $to, $type)
    {
        $from = str_replace(base_path(), '', realpath($from));

        $to = str_replace(base_path(), '', realpath($to));

        $this->line('<info>Copied '.$type.'</info> <comment>['.$from.']</comment> <info>To</info> <comment>['.$to.']</comment>');
    }
}
