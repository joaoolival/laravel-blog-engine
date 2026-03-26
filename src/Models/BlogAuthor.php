<?php

namespace Joaoolival\LaravelBlogEngine\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Joaoolival\LaravelBlogEngine\Database\Factories\BlogAuthorFactory;
use Joaoolival\LaravelBlogEngine\Traits\HasVisibility;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

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
 * @property-read Carbon|null $created_at
 * @property-read Carbon|null $updated_at
 * @property-read Carbon|null $deleted_at
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

    #[\Override]
    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'is_visible' => 'boolean',
        ];
    }

    protected static function newFactory(): BlogAuthorFactory
    {
        return BlogAuthorFactory::new();
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

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('webp')
            ->withResponsiveImages()
            ->format('webp')
            ->performOnCollections('avatar');
    }
}
