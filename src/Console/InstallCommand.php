<?php

namespace Joaoolival\LaravelBlogEngine\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Joaoolival\LaravelBlogEngine\BlogPlugin;
use Joaoolival\LaravelBlogEngine\LaravelBlogEngineServiceProvider;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\info;
use function Laravel\Prompts\warning;

class InstallCommand extends Command
{
    protected $signature = 'blog-engine:install
                            {--force : Overwrite existing files}
                            {--skip-migrations : Skip running migrations}';

    protected $description = 'Install the Laravel Blog Engine package';

    public function handle(): int
    {
        info('Installing Laravel Blog Engine...');

        $this->publishMigrations();
        $this->publishConfig();
        $this->runMigrations();
        $this->setupFilamentPlugin();
        $this->upgradeFilamentAssets();
        $this->createFilamentUser();

        $this->components->info('Laravel Blog Engine installed successfully.');
        $this->newLine();
        $this->components->bulletList([
            'Admin dashboard: <comment>/admin</comment>',
            'Blog posts: <comment>/admin/blog-posts</comment>',
            'Authors: <comment>/admin/blog-authors</comment>',
            'Categories: <comment>/admin/blog-categories</comment>',
        ]);

        return self::SUCCESS;
    }

    protected function createFilamentUser(): void
    {
        if (! confirm('Would you like to create a Filament admin user?', default: false)) {
            return;
        }

        $this->call('make:filament-user');
    }

    protected function upgradeFilamentAssets(): void
    {
        if (! confirm('Would you like to publish/refresh Filament assets?', default: true)) {
            $this->components->warn('Skipping Filament assets. Run later with: php artisan filament:upgrade');

            return;
        }

        $this->components->task('Publishing Filament assets', function () {
            $this->callSilently('filament:upgrade');
        });
    }

    protected function publishMigrations(): void
    {
        $this->components->task('Publishing migrations', function () {
            $this->callSilently('vendor:publish', [
                '--provider' => LaravelBlogEngineServiceProvider::class,
                '--tag' => 'laravel-blog-engine-migrations',
                '--force' => $this->option('force'),
            ]);

            $this->callSilently('vendor:publish', [
                '--provider' => 'Spatie\MediaLibrary\MediaLibraryServiceProvider',
                '--tag' => 'medialibrary-migrations',
            ]);
        });
    }

    protected function publishConfig(): void
    {
        $this->components->task('Publishing configuration', function () {
            $this->callSilently('vendor:publish', [
                '--provider' => LaravelBlogEngineServiceProvider::class,
                '--tag' => 'laravel-blog-engine-config',
                '--force' => $this->option('force'),
            ]);
        });
    }

    protected function runMigrations(): void
    {
        if ($this->option('skip-migrations')) {
            $this->components->warn('Skipping migrations.');

            return;
        }

        if (! confirm('Would you like to run the migrations now?', default: true)) {
            $this->components->warn('Skipping migrations. Run them later with: php artisan migrate');

            return;
        }

        $this->components->task('Running migrations', function () {
            $this->callSilently('migrate');
        });
    }

    protected function setupFilamentPlugin(): void
    {
        $panelProviderPath = $this->findPanelProvider();

        if (! $panelProviderPath) {
            if (confirm('No Filament panel found. Would you like to create one?', default: true)) {
                $this->createFilamentPanel();
                $panelProviderPath = $this->findPanelProvider();
            }
        }

        if ($panelProviderPath) {
            $this->registerBlogPlugin($panelProviderPath);
        } else {
            warning('No panel provider found. Please register the BlogPlugin manually.');
        }
    }

    protected function findPanelProvider(): ?string
    {
        $providersPath = app_path('Providers/Filament');

        if (! is_dir($providersPath)) {
            return null;
        }

        $providers = glob("{$providersPath}/*PanelProvider.php");

        return $providers[0] ?? null;
    }

    protected function createFilamentPanel(): void
    {
        $this->components->task('Creating Filament admin panel', function () {
            if (! File::isDirectory(app_path('Providers/Filament'))) {
                File::makeDirectory(app_path('Providers/Filament'), 0755, true);
            }

            $this->callSilently('make:filament-panel', ['id' => 'admin']);
        });
    }

    protected function registerBlogPlugin(string $filePath): void
    {
        $contents = File::get($filePath);
        $modified = false;

        // Add use statement if not present
        $useStatement = 'use '.BlogPlugin::class.';';

        if (! str_contains($contents, $useStatement)) {
            $contents = preg_replace(
                '/(use Filament\\\\PanelProvider;)/',
                "$1\n{$useStatement}",
                $contents
            );
            $modified = true;
        }

        // Register plugin if not present
        if (! str_contains($contents, 'BlogPlugin::make()') && ! str_contains($contents, 'new BlogPlugin()')) {
            $patterns = [
                '/(->\s*authMiddleware\s*\(\s*\[)/' => "->plugin(BlogPlugin::make())\n            \$1",
                '/(->\s*plugins\s*\(\s*\[)/' => "\$1\n                BlogPlugin::make(),",
            ];

            foreach ($patterns as $pattern => $replacement) {
                if (preg_match($pattern, $contents)) {
                    $contents = preg_replace($pattern, $replacement, $contents, 1);
                    $modified = true;
                    break;
                }
            }
        }

        if ($modified) {
            File::put($filePath, $contents);
            $this->components->task('Registering BlogPlugin in '.basename($filePath), fn () => true);
        } else {
            $this->components->twoColumnDetail(
                'BlogPlugin registration',
                '<fg=yellow;options=bold>ALREADY REGISTERED</>'
            );
        }
    }
}
