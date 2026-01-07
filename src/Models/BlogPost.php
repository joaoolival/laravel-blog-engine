<?php

namespace Joaoolival\LaravelBlogEngine\Models;

use Filament\Forms\Components\RichEditor\FileAttachmentProviders\SpatieMediaLibraryFileAttachmentProvider;
use Filament\Forms\Components\RichEditor\Models\Concerns\InteractsWithRichContent;
use Filament\Forms\Components\RichEditor\Models\Contracts\HasRichContent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Joaoolival\LaravelBlogEngine\Traits\HasVisibility;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

/**
 * @property-read int $id
 * @property-read string $title
 * @property-read bool $is_visible
 * @property-read string $slug
 * @property-read string|null $excerpt
 * @property-read string|null $content
 * @property-read array<int, string>|null $tags
 * @property-read \Illuminate\Support\Carbon|null $published_at
 * @property-read int $blog_author_id
 * @property-read int $blog_category_id
 * @property-read \Illuminate\Support\Carbon|null $created_at
 * @property-read \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Support\Carbon|null $deleted_at
 */
class BlogPost extends Model implements HasMedia, HasRichContent
{
    use HasVisibility, InteractsWithMedia, InteractsWithRichContent, SoftDeletes;

    protected $fillable = [
        'title',
        'is_visible',
        'slug',
        'excerpt',
        'content',
        'tags',
        'published_at',
        'blog_author_id',
        'blog_category_id',
    ];

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
            'is_visible' => 'boolean',
            'tags' => 'array',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * @param  Builder<BlogPost>  $query
     */
    public function scopeWhereIsDraft(Builder $query): void
    {
        $query->whereNull('published_at')
            ->orWhere('published_at', '>', now());
    }

    /**
     * @param  Builder<BlogPost>  $query
     */
    public function scopeWhereIsPublished(Builder $query): void
    {
        $query->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->whereIsVisible();
    }

    /**
     * @return BelongsTo<BlogAuthor, $this>
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(BlogAuthor::class, 'blog_author_id');
    }

    /**
     * @return BelongsTo<BlogCategory, $this>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(BlogCategory::class, 'blog_category_id');
    }

    public function setUpRichContent(): void
    {
        $this->registerRichContent('content')
            ->fileAttachmentProvider(
                SpatieMediaLibraryFileAttachmentProvider::make()
                    ->collection('content-attachments')
                    ->preserveFilenames()
            );
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('gallery');
        $this->addMediaCollection('content-attachments');
    }

    public function registerMediaConversions(?\Spatie\MediaLibrary\MediaCollections\Models\Media $media = null): void
    {
        /** @phpstan-ignore-next-line */
        $this->addMediaConversion('webp')
            ->format('webp')
            ->withResponsiveImages()
            ->performOnCollections('gallery', 'content-attachments');
    }
}
