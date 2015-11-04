<?php

namespace KodiCMS\ModulesLoader;

use App;
use Schema;
use Illuminate\Database\Migrations\Migrator;
use KodiCMS\ModulesLoader\Contracts\ModuleContainerInterface;
use Illuminate\Database\Migrations\DatabaseMigrationRepository;

class ModulesInstaller
{
    /**
     * @var array
     */
    protected $modules = [];

    /**
     * @var Migrator
     */
    protected $migrator;

    /**
     * @var DatabaseMigrationRepository
     */
    protected $repository;

    /**
     * @var array
     */
    protected $outputMessages = [];

    /**
     * @var array
     */
    protected $migrations = [];

    /**
     * @param array $modules
     */
    public function __construct(array $modules)
    {
        $this->modules = $modules;
        $this->migrator = App::make('migrator');
        $this->repository = App::make('migration.repository');

        $this->init();
    }

    /**
     * @param bool $pretend
     *
     * @return $this
     */
    public function migrateModules($pretend = false)
    {
        $this->output('Starting process of migration...');

        foreach ($this->modules as $module) {
            $this->migrateModule($module);
        }

        sort($this->migrations);

        $this->migrator->runMigrationList(array_unique($this->migrations), $pretend);

        foreach ($this->migrator->getNotes() as $note) {
            $this->output(' - '.$note);
        }

        return $this;
    }

    /**
     * Run migrations on a single module.
     *
     * @param ModuleContainerInterface $module
     *
     * @return $this
     */
    public function migrateModule(ModuleContainerInterface $module)
    {
        $path = $module->getPath(['database', 'migrations']);
        $files = $this->migrator->getMigrationFiles($path);

        // Once we grab all of the migration files for the path, we will compare them
        // against the migrations that have already been run for this package then
        // run each of the outstanding migrations against a database connection.
        $ran = $this->migrator->getRepository()->getRan();

        $migrations = array_diff($files, $ran);
        $this->migrator->requireFiles($path, $migrations);

        foreach ($migrations as $migration) {
            $this->migrations[] = $migration;
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function resetModules()
    {
        $this->output('Starting process of reseting...');

        foreach ($this->modules as $module) {
            $this->addModuleToReset($module);
        }

        return $this->rollbackModules();
    }

    /**
     * Reset migrations on a single module.
     *
     * @param ModuleContainerInterface $module
     *
     * @return $this
     */
    public function addModuleToReset(ModuleContainerInterface $module)
    {
        $path = $module->getPath(['database', 'migrations']);
        $this->migrator->requireFiles($path, $this->migrator->getMigrationFiles($path));

        return $this;
    }

    /**
     * @return $this
     */
    public function rollbackModules()
    {
        while (true) {
            $count = $this->migrator->rollback();

            foreach ($this->migrator->getNotes() as $note) {
                $this->output($note);
            }

            if ($count == 0) {
                break;
            }
        }

        return $this;
    }

    /**
     * @param array $data
     *
     * @return $this
     */
    public function seedModules(array $data = [])
    {
        foreach ($this->modules as $module) {
            $this->seedModule($module, array_get($data, $module->getName(), []));
        }

        return $this;
    }

    /**
     * Run seeds on a module.
     *
     * @param ModuleContainerInterface $module
     * @param array                    $data
     *
     * @return $this
     */
    public function seedModule(ModuleContainerInterface $module, array $data = [])
    {
        $className = $module->getNamespace().'\\database\\seeds\\DatabaseSeeder';

        if (!class_exists($className)) {
            return false;
        }

        $seeder = app($className, $data);
        $seeder->run();

        $this->output(sprintf('<info>Seeded %s</info> ', $module));

        return $this;
    }

    /**
     * @return array
     */
    public function getOutputMessages()
    {
        return $this->outputMessages;
    }

    /**
     * @return $this
     */
    public function cleanOutputMessages()
    {
        $this->outputMessages = [];

        return $this;
    }

    protected function init()
    {
        $firstUp = !Schema::hasTable('migrations');
        if ($firstUp) {
            $this->repository->createRepository();
            $this->output('Migration table created successfully.');
        }
    }

    protected function deinit()
    {
        Schema::dropIfExists('migrations');
        $this->output('Migration table dropped.');
    }

    /**
     * @param string $message
     *
     * @return $this
     */
    protected function output($message)
    {
        $this->outputMessages[] = $message;

        return $this;
    }
}
