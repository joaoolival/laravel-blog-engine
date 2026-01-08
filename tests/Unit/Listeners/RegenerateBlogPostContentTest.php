<?php

use Joaoolival\LaravelBlogEngine\Listeners\RegenerateBlogPostContent;
use Joaoolival\LaravelBlogEngine\Models\BlogPost;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\ResponsiveImages\Events\ResponsiveImagesGeneratedEvent;

beforeEach(function () {
    // Run package migrations
    foreach (glob(__DIR__.'/../../../database/migrations/*.php') as $migration) {
        (include $migration)->up();
    }

    // Run media library migration
    $mediaLibraryMigration = __DIR__.'/../../../vendor/spatie/laravel-medialibrary/database/migrations/create_media_table.php.stub';
    if (Illuminate\Support\Facades\File::exists($mediaLibraryMigration)) {
        (include $mediaLibraryMigration)->up();
    }
});

test('it regenerates content when responsive images are generated', function () {
    // Arrange
    $post = BlogPost::factory()->create(['content' => '<p>Original</p>']);

    $media = new Media;
    $media->collection_name = 'content-attachments';
    $media->model_type = BlogPost::class;
    $media->setAttribute('model_type', BlogPost::class);
    $media->setAttribute('model_id', $post->id);
    $media->setRelation('model', $post);

    $event = new ResponsiveImagesGeneratedEvent($media);
    $listener = new RegenerateBlogPostContent;

    // Act
    $listener->handle($event);

    // Assert
    expect($post->rendered_content)->not->toBeNull();
});

test('it does not regenerate if collection name is wrong', function () {
    $post = BlogPost::factory()->create();
    $post->rendered_content = null;
    $post->saveQuietly();

    $media = new Media;
    $media->collection_name = 'gallery'; // Wrong collection
    $media->setAttribute('model_type', BlogPost::class);
    $media->setAttribute('model_id', $post->id);
    $media->setRelation('model', $post);

    $event = new ResponsiveImagesGeneratedEvent($media);
    $listener = new RegenerateBlogPostContent;

    $listener->handle($event);

    // Should remain null
    expect($post->rendered_content)->toBeNull();
});

test('it does not regenerate if model is not blog post', function () {
    $media = new Media;
    $media->collection_name = 'content-attachments';
    $media->model_type = 'App\Models\User'; // Wrong model

    $model = new class extends Illuminate\Database\Eloquent\Model {};
    $media->setRelation('model', $model);

    $event = new ResponsiveImagesGeneratedEvent($media);
    $listener = new RegenerateBlogPostContent;

    // Should not throw error
    $listener->handle($event);

    expect(true)->toBeTrue();
});
