<?php

namespace Joaoolival\LaravelBlogEngine\Http\Resources\Authors;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Joaoolival\LaravelBlogEngine\Models\BlogAuthor;

/**
 * @mixin BlogAuthor
 */
class BlogAuthorResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $media = $this->getFirstMedia('avatar');

        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'email' => $this->email,
            'bio' => $this->bio,
            'github_handle' => $this->github_handle,
            'twitter_handle' => $this->twitter_handle,
            'linkedin_handle' => $this->linkedin_handle,
            'instagram_handle' => $this->instagram_handle,
            'facebook_handle' => $this->facebook_handle,
            'avatar' => $media ? [
                'url' => $media->getUrl('webp'),
                'srcset' => $media->getSrcset('webp'),
                'original_url' => $media->getUrl(),
            ] : null,
            'posts_count' => $this->whenCounted('posts'),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
