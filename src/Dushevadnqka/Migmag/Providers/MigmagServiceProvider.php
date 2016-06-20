<?php
namespace Dushevadnqka\Migmag\Providers;

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
        $commands = ['Migrate'];

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
        ];
    }

}
