<?php

namespace Joaoolival\LaravelBlogEngine\Filament\Resources\BlogAuthors\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;
use Joaoolival\LaravelBlogEngine\Filament\Resources\BlogAuthors\BlogAuthorResource;

class EditBlogAuthor extends EditRecord
{
    protected static string $resource = BlogAuthorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
