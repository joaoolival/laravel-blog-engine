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
            ->discoversMigrations()
            ->hasCommands([
                InstallCommand::class,
                Console\GenerateBlogContentCommand::class,
            ]);
    }

    public function packageRegistered(): void
    {
        $this->app->singleton(BlogService::class);
    }
}
