<?php

namespace Joaoolival\LaravelBlogEngine\Filament\Resources\BlogAuthors\Pages;

use Joaoolival\LaravelBlogEngine\Filament\Resources\BlogAuthors\BlogAuthorResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

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
