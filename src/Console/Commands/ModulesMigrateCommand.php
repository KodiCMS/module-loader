<?php
namespace KodiCMS\ModulesLoader\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;
use KodiCMS\ModulesLoader\ModulesInstaller;
use Symfony\Component\Console\Input\InputOption;
use KodiCMS\ModulesLoader\ModulesLoaderFacade as ModulesLoader;

class ModulesMigrateCommand extends Command
{

    use ConfirmableTrait;

    /**
     * The console command name.
     */
    protected $name = 'modules:migrate';


    /**
     * Execute the console command.
     */
    public function fire()
    {
        if ( ! $this->confirmToProceed()) {
            return;
        }

        $this->output->writeln('<info>Run application migrations ...</info>');
        $this->call('migrate');

        $this->output->writeln('<info>Run modules migrations ...</info>');
        $installer = new ModulesInstaller(ModulesLoader::getRegisteredModules());

        $installer->cleanOutputMessages();

        if ($this->input->getOption('rollback')) {
            $installer->resetModules();
        }

        $installer->migrateModules();

        foreach ($installer->getOutputMessages() as $message) {
            $this->output->writeln($message);
        }

        // Finally, if the "seed" option has been given, we will re-run the database
        // seed task to re-populate the database, which is convenient when adding
        // a migration and a seed at the same time, as it is only this command.
        if ($this->input->getOption('seed')) {
            $this->call('db:seed');
            $this->call('modules:seed');
        }
    }


    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['seed', 's', InputOption::VALUE_NONE, 'Indicates if the seed task should be re-run.'],
            ['rollback', 'r', InputOption::VALUE_NONE, 'Rollback database migration.'],
            ['force', null, InputOption::VALUE_NONE, 'Force the operation to run when in production.'],
        ];
    }
}
