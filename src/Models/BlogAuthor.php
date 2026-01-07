<?php

namespace Joaoolival\LaravelBlogEngine\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Joaoolival\LaravelBlogEngine\Database\Factories\BlogAuthorFactory;
use Joaoolival\LaravelBlogEngine\Traits\HasVisibility;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

/**
 * @property-read int $id
 * @property-read string $name
 * @property-read bool $is_visible
 * @property-read string $slug
 * @property-read string $email
 * @property-read string|null $bio
 * @property-read string|null $github_handle
 * @property-read string|null $twitter_handle
 * @property-read string|null $linkedin_handle
 * @property-read string|null $instagram_handle
 * @property-read string|null $facebook_handle
 * @property-read \Illuminate\Support\Carbon|null $created_at
 * @property-read \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Support\Carbon|null $deleted_at
 */
class BlogAuthor extends Model implements HasMedia
{
    /** @use HasFactory<BlogAuthorFactory> */
    use HasFactory, HasVisibility, InteractsWithMedia, SoftDeletes;

    protected $fillable = [
        'name',
        'is_visible',
        'slug',
        'email',
        'bio',
        'github_handle',
        'twitter_handle',
        'linkedin_handle',
        'instagram_handle',
        'facebook_handle',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'is_visible' => 'boolean',
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
        $this->addMediaCollection('avatar')
            ->singleFile();
    }

    public function registerMediaConversions(?\Spatie\MediaLibrary\MediaCollections\Models\Media $media = null): void
    {
        /** @phpstan-ignore-next-line */
        $this->addMediaConversion('webp')
            ->format('webp')
            ->withResponsiveImages()
            ->performOnCollections('avatar');
    }
}
