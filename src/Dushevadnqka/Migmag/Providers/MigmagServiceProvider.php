<?php

namespace Dushevadnqka\Migmag\Providers;

use Illuminate\Support\ServiceProvider;
use Dushevadnqka\Migmag\Database\Migrations\Migrator;
use Illuminate\Database\Migrations\DatabaseMigrationRepository;
use Dushevadnqka\Migmag\Database\Console\Migrations\MigrateCommand;
use Dushevadnqka\Migmag\Database\Console\Migrations\ResetCommand;
use Dushevadnqka\Migmag\Database\Console\Migrations\RefreshCommand;
use Dushevadnqka\Migmag\Database\Console\Migrations\StatusCommand;

class MigmagServiceProvider extends ServiceProvider
{

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerRepository();
        $this->registerMigrator();
        $this->registerCommands();
    }

    private function getMigrator()
    {
        return \App::make('Dushevadnqka\Migmag\Database\Migrations\Migrator');
    }

    /**
     * Register the migration repository service.
     *
     * @return void
     */
    protected function registerRepository()
    {
        $this->app->singleton('migration.repository', function ($app) {
            $table = $app['config']['database.migrations'];

            return new DatabaseMigrationRepository($app['db'], $table);
        });
    }

    /**
     * Register the migrator service.
     *
     * @return void
     */
    protected function registerMigrator()
    {
        $this->app->singleton('Dushevadnqka\Migmag\Database\Migrations\Migrator', function ($app) {
            $repository = $app['migration.repository'];

            return new Migrator($repository, $app['db'], $app['files']);
        });
    }

    /**
     * Register all of the migration commands.
     *
     * @return void
     */
    protected function registerCommands()
    {
        $commands = ['Migrate', 'Reset', 'Refresh', 'Status'];

        // We'll simply spin through the list of commands that are migration related
        // and register each one of them with an application container. They will
        // be resolved in the Artisan start file and registered on the console.
        foreach ($commands as $command) {
            $this->{'register' . $command . 'Command'}();
        }

        // Once the commands are registered in the application IoC container we will
        // register them with the Artisan start event so that these are available
        // when the Artisan application actually starts up and is getting used.
        $this->commands(
                'command.migmag.migrate', 
                'command.migmag.migrate.reset', 
                'command.migmag.migrate.refresh', 
                'command.migmag.migrate.status'
        );
    }

    /**
     * Register the "migrate" migration command.
     *
     * @return void
     */
    protected function registerMigrateCommand()
    {
        $this->app->singleton('command.migmag.migrate', function ($app) {
            $migrator = $this->getMigrator();
            return new MigrateCommand($migrator);
        });
    }

    /**
     * Register the "reset" migration command.
     *
     * @return void
     */
    protected function registerResetCommand()
    {
        $this->app->singleton('command.migmag.migrate.reset', function ($app) {
            $migrator = $this->getMigrator();
            return new ResetCommand($migrator);
        });
    }

    /**
     * Register the "refresh" migration command.
     *
     * @return void
     */
    protected function registerRefreshCommand()
    {
        $this->app->singleton('command.migmag.migrate.refresh', function () {
            return new RefreshCommand;
        });
    }

    /**
     * Register the "status" migration command.
     *
     * @return void
     */
    protected function registerStatusCommand()
    {
        $this->app->singleton('command.migmag.migrate.status', function ($app) {
            $migrator = $this->getMigrator();
            return new StatusCommand($migrator);
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
            'migration.repository',
            'command.migmag.migrate',
            'command.migmag.migrate.reset',
            'command.migmag.migrate.refresh',
            'command.migmag.migrate.status',
        ];
    }

}
