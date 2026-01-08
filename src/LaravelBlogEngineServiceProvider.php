<?php

namespace Joaoolival\LaravelBlogEngine;

use Joaoolival\LaravelBlogEngine\Console\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

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
            ->hasConfigFile('laravel-blog-engine')
            ->hasCommand(InstallCommand::class);
    }

    public function packageRegistered(): void
    {
        $this->app->singleton(BlogService::class);
    }

    public function packageBooted(): void
    {
        // Manually publish migrations to avoid issues with hasMigrations() magic
        $this->publishes([
            __DIR__.'/../database/migrations' => database_path('migrations'),
        ], 'laravel-blog-engine-migrations');
    }
}
