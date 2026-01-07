<?php

namespace Joaoolival\LaravelBlogEngine;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Joaoolival\LaravelBlogEngine\Console\InstallCommand;

class LaravelBlogEngineServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-blog-engine')
            ->setBasePath(__DIR__ . '/../')
            ->hasConfigFile()
            ->hasViews()
            ->hasCommand(InstallCommand::class);
    }

    public function packageBooted(): void
    {
        // Manually publish migrations to avoid issues with hasMigrations() magic
        $this->publishes([
            __DIR__ . '/../database/migrations' => database_path('migrations'),
        ], 'laravel-blog-engine-migrations');

    }
}
