<?php

namespace Dushevadnqka\Migmag\Database\Console\Migrations;

use Illuminate\Console\ConfirmableTrait;
use Dushevadnqka\Migmag\Database\Migrations\Migrator;
use Symfony\Component\Console\Input\InputOption;

class MigrateCommand extends BaseCommand
{

    use ConfirmableTrait;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'migmag:migrate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run the database migrations as single- single migration file exec';

    /**
     * The migrator instance.
     *
     * @var \Illuminate\Database\Migrations\Migrator
     */
    protected $migrator;

    /**
     * Create a new migration command instance.
     *
     * @param  \Illuminate\Database\Migrations\Migrator  $migrator
     * @return void
     */
    public function __construct(Migrator $migrator)
    {
        parent::__construct();

        $this->migrator = $migrator;
    }

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

        if (is_null($path = $this->input->getOption('path'))) {
            $path = $this->ask('Please enter the full path of the migration file, without file extension, in the following format: path/migration-file');
        }

        $this->prepareDatabase();

        // The pretend option can be used for "simulating" the migration and grabbing
        // the SQL queries that would fire if the migration were to be run against
        // a database for real, which is helpful for double checking migrations.
        $pretend = $this->input->getOption('pretend');

        // diff from original
        $path = $this->laravel->basePath() . '/' . $path;

        $this->migrator->run($path, [
            'pretend' => $pretend,
            'step' => $this->input->getOption('step'),
        ]);

        // Once the migrator has run we will grab the note output and send it out to
        // the console screen, since the migrator itself functions without having
        // any instances of the OutputInterface contract passed into the class.
        foreach ($this->migrator->getNotes() as $note) {
            $this->output->writeln($note);
        }

        // Finally, if the "seed" option has been given, we will re-run the database
        // seed task to re-populate the database, which is convenient when adding
        // a migration and a seed at the same time, as it is only this command.
        if ($this->input->getOption('seed')) {
            $this->call('db:seed', ['--force' => true]);
        }
    }

    /**
     * Prepare the migration database for running.
     *
     * @return void
     */
    protected function prepareDatabase()
    {
        $this->migrator->setConnection($this->input->getOption('database'));

        if (!$this->migrator->repositoryExists()) {
            $options = ['--database' => $this->input->getOption('database')];

            $this->call('migrate:install', $options);
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
            ['database', null, InputOption::VALUE_OPTIONAL, 'The database connection to use.'],
            
            ['force', null, InputOption::VALUE_NONE, 'Force the operation to run when in production.'],
            
            ['path', null, InputOption::VALUE_OPTIONAL, 'The path of migrations files to be executed.'],
            
            ['pretend', null, InputOption::VALUE_NONE, 'Dump the SQL queries that would be run.'],
            
            ['seed', null, InputOption::VALUE_NONE, 'Indicates if the seed task should be re-run.'],
            
            ['step', null, InputOption::VALUE_NONE, 'Force the migrations to be run so they can be rolled back individually.'],
        ];
    }

}
