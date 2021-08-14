<?php

namespace Laravel\BreezeApi\Console;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Artisan;
use Symfony\Component\Process\Process;

/**
 * Class InstallCommand
 * @package Laravel\BreezeApi\Console
 */
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
        // Install required packages (Sanctum, Laravel Responder, Enlighten & Api Test Helper)
        $this->requireComposerPackages(
            'laravel/sanctum:^2.6',
            'flugger/laravel-responder:^3.1.3',
            'styde/enlighten:^0.7.1',
            'stephenjude/api-test-helper:^1.0'
        );

        // Controllers...
        (new Filesystem)->ensureDirectoryExists(app_path('Http/Controllers/Api/Auth'));
        (new Filesystem)->copyDirectory(__DIR__ . '/../../stubs/default/App/Http/Controllers/Api/Auth', app_path('Http/Controllers/Api/Auth'));

        // Transformers...
        (new Filesystem)->ensureDirectoryExists(app_path('Transformers'));
        (new Filesystem)->copyDirectory(__DIR__ . '/../../stubs/default/App/Transformers', app_path('Transformers'));

        // Models...
        (new Filesystem)->ensureDirectoryExists(app_path('Models'));
        (new Filesystem)->copyDirectory(__DIR__ . '/../../stubs/default/App/Models', app_path('Models'));

        // Documentation...
        (new Filesystem)->ensureDirectoryExists(base_path('public'));
        (new Filesystem)->copyDirectory(__DIR__ . '/../../stubs/default/public', base_path('public'));

        // Handling Api Exceptions with Laravel Responder...
        $this->replaceInFile('Illuminate\Foundation\Exceptions\Handler', 'Flugg\Responder\Exceptions\Handler', app_path('Exceptions/Handler.php'));

        // Configure Enlighten...
        $this->configureEnlighten();

        // Views...
        (new Filesystem)->ensureDirectoryExists(resource_path('views/auth'));
        (new Filesystem)->ensureDirectoryExists(resource_path('views/layouts'));
        (new Filesystem)->ensureDirectoryExists(resource_path('views/components'));

        (new Filesystem)->copyDirectory(__DIR__ . '/../../stubs/default/resources/views/auth', resource_path('views/auth'));
        (new Filesystem)->copyDirectory(__DIR__ . '/../../stubs/default/resources/views/layouts', resource_path('views/layouts'));
        (new Filesystem)->copyDirectory(__DIR__ . '/../../stubs/default/resources/views/components', resource_path('views/components'));

        // Components...
        (new Filesystem)->ensureDirectoryExists(app_path('View/Components'));
        (new Filesystem)->copyDirectory(__DIR__ . '/../../stubs/default/App/View/Components', app_path('View/Components'));

        // Tests...
        (new Filesystem)->copyDirectory(__DIR__ . '/../../stubs/default/tests/Api', base_path('tests/Api/Auth'));
        $this->replaceInFile('assertStatus(200)', "assertRedirect('/docs/index.html')", base_path('tests/Feature/ExampleTest.php'));

        // PHPUnit...
        copy(__DIR__ . '/../../stubs/default/phpunit.xml', base_path('phpunit.xml'));

        // Routes...
        copy(__DIR__ . '/../../stubs/default/routes/web.php', base_path('routes/web.php'));
        copy(__DIR__ . '/../../stubs/default/routes/api.php', base_path('routes/api.php'));

        // NPM Packages...
        $this->updateNodePackages(function ($packages) {
            return [
                    '@tailwindcss/forms' => '^0.2.1',
                    'alpinejs' => '^2.7.3',
                    'autoprefixer' => '^10.1.0',
                    'postcss' => '^8.2.1',
                    'postcss-import' => '^12.0.1',
                    'tailwindcss' => '^2.0.2',
                ] + $packages;
        });

        // Tailwind / Webpack...
        copy(__DIR__ . '/../../stubs/default/tailwind.config.js', base_path('tailwind.config.js'));
        copy(__DIR__ . '/../../stubs/default/webpack.mix.js', base_path('webpack.mix.js'));
        copy(__DIR__ . '/../../stubs/default/resources/css/app.css', resource_path('css/app.css'));
        copy(__DIR__ . '/../../stubs/default/resources/js/app.js', resource_path('js/app.js'));

        $this->info('Breeze Api scaffolding installed successfully.');
        $this->comment('Execute the "npm install && npm run dev" command to build your assets.');
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

    /**
     * Update the "package.json" file.
     *
     * @param callable $callback
     * @param bool $dev
     * @return void
     */
    protected static function updateNodePackages(callable $callback, $dev = true)
    {
        if (!file_exists(base_path('package.json'))) {
            return;
        }

        $configurationKey = $dev ? 'devDependencies' : 'dependencies';

        $packages = json_decode(file_get_contents(base_path('package.json')), true);

        $packages[$configurationKey] = $callback(
            array_key_exists($configurationKey, $packages) ? $packages[$configurationKey] : [],
            $configurationKey
        );

        ksort($packages[$configurationKey]);

        file_put_contents(
            base_path('package.json'),
            json_encode($packages, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . PHP_EOL
        );
    }

    /**
     * Configure Enlighten package
     * @return void
     */
    protected function configureEnlighten()
    {
        copy(__DIR__ . '/../../stubs/default/config/database.php', base_path('config/database.php'));

        copy(__DIR__ . '/../../stubs/default/config/enlighten.php', base_path('config/enlighten.php'));

        copy(__DIR__ . '/../../stubs/default/database/database.sqlite', base_path('database/database.sqlite'));

        Artisan::call('enlighten:install');
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
