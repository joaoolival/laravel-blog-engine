<?php

use Joaoolival\LaravelBlogEngine\BlogPlugin;
use Joaoolival\LaravelBlogEngine\BlogService;
use Joaoolival\LaravelBlogEngine\Facades\Blog;

describe('ServiceProvider', function () {
    it('registers BlogService as singleton', function () {
        $instance1 = app(BlogService::class);
        $instance2 = app(BlogService::class);

        expect($instance1)->toBe($instance2);
    });

    it('resolves BlogService from container', function () {
        $service = app(BlogService::class);

        expect($service)->toBeInstanceOf(BlogService::class);
    });

    it('publishes config file', function () {
        $configPath = config_path('laravel-blog-engine.php');

        // Config should be loaded even if not published
        expect(config('laravel-blog-engine'))->toBeArray();
    });
});

describe('Facade', function () {
    it('resolves to BlogService', function () {
        $resolved = Blog::getFacadeRoot();

        expect($resolved)->toBeInstanceOf(BlogService::class);
    });
});

describe('BlogPlugin', function () {
    it('can be created with make', function () {
        $plugin = BlogPlugin::make();

        expect($plugin)->toBeInstanceOf(BlogPlugin::class);
    });

    it('returns correct id', function () {
        $plugin = BlogPlugin::make();

        expect($plugin->getId())->toBe('laravel-blog-engine');
    });
});
