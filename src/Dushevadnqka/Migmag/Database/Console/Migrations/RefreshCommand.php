<?php

namespace Dushevadnqka\Migmag\Database\Console\Migrations;

use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;
use Symfony\Component\Console\Input\InputOption;

class RefreshCommand extends Command
{
    use ConfirmableTrait;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'migmag:migrate:refresh';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset and re-run single migration by path.';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {
        if (!$this->confirmToProceed()) {
            return;
        }

        $database = $this->input->getOption('database');

        $force = $this->input->getOption('force');

        if (is_null($path = $this->input->getOption('path'))) {
            $path = $this->ask('Please enter the full path of the migration file, without file extension, in the following format: path/migration-file');
        }

        $arrayPath = explode('/', $path);

        $migration = end($arrayPath);

        $this->call('migmag:migrate:reset', [
            '--database' => $database, '--force' => $force, '--migration' => $migration
        ]);

        // The refresh command is essentially just a brief aggregate of a few other of
        // the migration commands and just provides a convenient wrapper to execute
        // them in succession. We'll also see if we need to re-seed the database.
        $this->call('migmag:migrate', [
            '--database' => $database,
            '--force' => $force,
            '--path' => $path,
        ]);

        if ($this->needsSeeding()) {
            $this->runSeeder($database);
        }
    }

    /**
     * Determine if the developer has requested database seeding.
     *
     * @return bool
     */
    protected function needsSeeding()
    {
        return $this->option('seed') || $this->option('seeder');
    }

    /**
     * Run the database seeder command.
     *
     * @param  string  $database
     * @return void
     */
    protected function runSeeder($database)
    {
        $class = $this->option('seeder') ? : 'DatabaseSeeder';

        $force = $this->input->getOption('force');

        $this->call('db:seed', [
            '--database' => $database, '--class' => $class, '--force' => $force,
        ]);
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['database', null, InputOption::VALUE_OPTIONAL, 'The database connection to use.'],
            
            ['force', null, InputOption::VALUE_NONE, 'Force the operation to run when in production.'],
            
            ['path', null, InputOption::VALUE_OPTIONAL, 'The path of migrations files to be executed.'],
            
            ['seed', null, InputOption::VALUE_NONE, 'Indicates if the seed task should be re-run.'],
            
            ['seeder', null, InputOption::VALUE_OPTIONAL, 'The class name of the root seeder.'],
        ];
    }
}
