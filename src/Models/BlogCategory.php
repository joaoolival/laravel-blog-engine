<?php

namespace Joaoolival\LaravelBlogEngine\Models;

use Joaoolival\LaravelBlogEngine\Traits\HasVisibility;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;


/**
 * @property-read int $id
 * @property-read string $name
 * @property-read string $slug
 * @property-read string|null $description
 * @property-read bool $is_visible
 * @property-read string|null $seo_title
 * @property-read string|null $seo_description
 * @property-read \Illuminate\Support\Carbon|null $created_at
 * @property-read \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Support\Carbon|null $deleted_at
 */
class BlogCategory extends Model implements HasMedia
{
    use HasVisibility, InteractsWithMedia, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'is_visible',
        'seo_title',
        'seo_description',
    ];

    protected function casts(): array
    {
        return [
            'is_visible' => 'boolean',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * @return HasMany<BlogPost, $this>
     */
    public function posts(): HasMany
    {
        return $this->hasMany(BlogPost::class);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('banner_image')
            ->singleFile();
    }

    public function registerMediaConversions(?\Spatie\MediaLibrary\MediaCollections\Models\Media $media = null): void
    {
        /** @phpstan-ignore-next-line */
        $this->addMediaConversion('webp')
            ->format('webp')
            ->withResponsiveImages()
            ->performOnCollections('banner_image');
    }
}



