<?php

namespace Laravel\BreezeApi\Console;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use Symfony\Component\Process\Process;

class InstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'breeze-api:install {--composer=global : Absolute path to the Composer binary which should be used to install packages}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install the Breeze Api controllers, routes and transformers';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        // Install required packages (Sanctum, Laravel Responder & Enlighten)
        $this->requireComposerPackages('laravel/sanctum:^2.6');
        $this->requireComposerPackages('flugg/laravel-responder');
        $this->requireComposerPackages('styde/enlighten --dev');

        // Controllers...
        (new Filesystem)->ensureDirectoryExists(app_path('Http/Controllers/Api/Auth'));
        (new Filesystem)->copyDirectory(__DIR__ . '/../../stubs/default/App/Http/Controllers/Api/Auth', app_path('Http/Controllers/Api/Auth'));

        // Requests...
        (new Filesystem)->ensureDirectoryExists(app_path('Http/Requests/Api/Auth'));
        (new Filesystem)->copyDirectory(__DIR__ . '/../../stubs/default/App/Http/Requests/Api/Auth', app_path('Http/Requests/Api/Auth'));

        // Handling Api Exceptions
        $this->replaceInFile('Illuminate\Foundation\Exceptions\Handler', 'Flugg\Responder\Exceptions\Handler', app_path('Exceptions/Handler.php'));

        // Api Documentation Database
        $this->configureApiDocumentationDatabase();

        // Tests...
        (new Filesystem)->copyDirectory(__DIR__ . '/../../stubs/default/tests/Api', base_path('tests/Api/Auth'));

        // Routes...
        copy(__DIR__ . '/../../stubs/default/routes/web.php', base_path('routes/web.php'));
        copy(__DIR__ . '/../../stubs/default/routes/auth.php', base_path('routes/auth.php'));

        $this->info('Breeze Api scaffolding installed successfully.');
    }

    /**
     * Install the middleware to a group in the application Http Kernel.
     *
     * @param string $after
     * @param string $name
     * @param string $group
     * @return void
     */
    protected function installMiddlewareAfter($after, $name, $group = 'web')
    {
        $httpKernel = file_get_contents(app_path('Http/Kernel.php'));

        $middlewareGroups = Str::before(Str::after($httpKernel, '$middlewareGroups = ['), '];');
        $middlewareGroup = Str::before(Str::after($middlewareGroups, "'$group' => ["), '],');

        if (!Str::contains($middlewareGroup, $name)) {
            $modifiedMiddlewareGroup = str_replace(
                $after . ',',
                $after . ',' . PHP_EOL . '            ' . $name . ',',
                $middlewareGroup,
            );

            file_put_contents(app_path('Http/Kernel.php'), str_replace(
                $middlewareGroups,
                str_replace($middlewareGroup, $modifiedMiddlewareGroup, $middlewareGroups),
                $httpKernel
            ));
        }
    }

    /**
     * Installs the given Composer Packages into the application.
     *
     * @param mixed $packages
     * @return void
     */
    protected function requireComposerPackages($packages)
    {
        $composer = $this->option('composer');

        if ($composer !== 'global') {
            $command = ['php', $composer, 'require'];
        }

        $command = array_merge(
            $command ?? ['composer', 'require'],
            is_array($packages) ? $packages : func_get_args()
        );

        (new Process($command, base_path(), ['COMPOSER_MEMORY_LIMIT' => '-1']))
            ->setTimeout(null)
            ->run(function ($type, $output) {
                $this->output->write($output);
            });
    }

    protected function configureApiDocumentationDatabase()
    {
        $sqliteDatabaseConfig = `'sqlite' => [
            'driver' => 'sqlite',
            'url' => env('DATABASE_URL'),
            'database' => env('DB_DATABASE', database_path('database.sqlite')),
            'prefix' => '',
            'foreign_key_constraints' => env('DB_FOREIGN_KEYS', true),
        ],`;

        $enlightenDatabaseConfig = `
        'enlighten' => [
            'driver' => 'sqlite',
            'url' => env('DATABASE_URL'),
            'database' => database_path('database.sqlite'),
            'prefix' => '',
            'foreign_key_constraints' => env('DB_FOREIGN_KEYS', true),
        ],`;

        $this->replaceInFile($sqliteDatabaseConfig, $sqliteDatabaseConfig . $enlightenDatabaseConfig,
            config_path('database.php'));
    }

    /**
     * Replace a given string within a given file.
     *
     * @param string $search
     * @param string $replace
     * @param string $path
     * @return void
     */
    protected function replaceInFile($search, $replace, $path)
    {
        file_put_contents($path, str_replace($search, $replace, file_get_contents($path)));
    }
}
