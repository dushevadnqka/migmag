<?php

namespace Dushevadnqka\Migmag\Providers;

/*
  use Illuminate\Support\ServiceProvider;
  use Dushevadnqka\Migmag\Database\Migrations\Migrator;
  use Illuminate\Database\Migrations\DatabaseMigrationRepository;
 */

use Illuminate\Support\ServiceProvider;
use Dushevadnqka\Migmag\Database\Migrations\Migrator;
use Dushevadnqka\Migmag\Database\Console\Migrations\MigrateCommand;
use Illuminate\Database\Migrations\DatabaseMigrationRepository;

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
        $commands = ['Migrate', 'Rollback', 'Reset', 'Refresh', 'Install', 'Make', 'Status'];

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
                'command.migrate.magic'
        );
    }

    /**
     * Register the "migrate" migration command.
     *
     * @return void
     */
    protected function registerMigrateCommand()
    {
        $this->app->singleton('command.migrate.magic', function ($app) {
            $migrator = \App::make('Dushevadnqka\Migmag\Database\Migrations\Migrator');
            return new MigrateCommand($migrator);
        });
    }

    /**
     * Register the "rollback" migration command.
     *
     * @return void
     */
    protected function registerRollbackCommand()
    {
        $this->app->singleton('command.migrate.rollback', function ($app) {
            return new RollbackCommand($app['migrator']);
        });
    }

    /**
     * Register the "reset" migration command.
     *
     * @return void
     */
    protected function registerResetCommand()
    {
        $this->app->singleton('command.migrate.reset', function ($app) {
            return new ResetCommand($app['migrator']);
        });
    }

    /**
     * Register the "refresh" migration command.
     *
     * @return void
     */
    protected function registerRefreshCommand()
    {
        $this->app->singleton('command.migrate.refresh', function () {
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
        $this->app->singleton('command.migrate.status', function ($app) {
            return new StatusCommand($app['migrator']);
        });
    }

    /**
     * Register the "install" migration command.
     *
     * @return void
     */
    protected function registerInstallCommand()
    {
        $this->app->singleton('command.migrate.install', function ($app) {
            return new InstallCommand($app['migration.repository']);
        });
    }

    /**
     * Register the "make" migration command.
     *
     * @return void
     */
    protected function registerMakeCommand()
    {
        $this->registerCreator();

        $this->app->singleton('command.migrate.make', function ($app) {
            // Once we have the migration creator registered, we will create the command
            // and inject the creator. The creator is responsible for the actual file
            // creation of the migrations, and may be extended by these developers.
            $creator = $app['migration.creator'];

            $composer = $app['composer'];

            return new MigrateMakeCommand($creator, $composer);
        });
    }

    /**
     * Register the migration creator.
     *
     * @return void
     */
    protected function registerCreator()
    {
        $this->app->singleton('migration.creator', function ($app) {
            return new MigrationCreator($app['files']);
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
            'migrator', 
            'migration.repository', 
            'command.migrate.magic',
            'command.migrate.rollback', 
            'command.migrate.reset',
            'command.migrate.refresh', 
            'command.migrate.install',
            'command.migrate.status', 
            'migration.creator',
            'command.migrate.make',
        ];
    }

}
