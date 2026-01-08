<?php

namespace Joaoolival\LaravelBlogEngine\Filament\Resources\BlogPosts\Pages;

use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Joaoolival\LaravelBlogEngine\Actions\Posts\RegenerateRenderedContentAction;
use Joaoolival\LaravelBlogEngine\Filament\Resources\BlogPosts\BlogPostResource;
use Joaoolival\LaravelBlogEngine\Models\BlogPost;

class CreateBlogPost extends CreateRecord
{
    protected static string $resource = BlogPostResource::class;

    /**
     * Regenerate rendered content after creating the post.
     *
     * @param  BlogPost  $record
     */
    protected function afterCreate(Model $record): void
    {
        $action = new RegenerateRenderedContentAction;
        $record->rendered_content = $action->handle($record);
        $record->saveQuietly();
    }
}
