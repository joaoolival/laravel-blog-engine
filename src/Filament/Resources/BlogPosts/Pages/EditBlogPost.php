<?php

namespace Joaoolival\LaravelBlogEngine\Filament\Resources\BlogPosts\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Joaoolival\LaravelBlogEngine\Actions\Posts\RegenerateRenderedContentAction;
use Joaoolival\LaravelBlogEngine\Filament\Resources\BlogPosts\BlogPostResource;
use Joaoolival\LaravelBlogEngine\Models\BlogPost;

class EditBlogPost extends EditRecord
{
    protected static string $resource = BlogPostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }

    /**
     * Regenerate rendered content after saving the post.
     *
     * @param  BlogPost  $record
     */
    protected function afterSave(Model $record): void
    {
        $action = new RegenerateRenderedContentAction;
        $record->rendered_content = $action->handle($record);
        $record->saveQuietly();
    }
}
