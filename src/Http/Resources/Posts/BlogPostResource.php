<?php

namespace Joaoolival\LaravelBlogEngine\Http\Resources\Posts;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Joaoolival\LaravelBlogEngine\Http\Resources\Authors\BlogAuthorResource;
use Joaoolival\LaravelBlogEngine\Http\Resources\Categories\BlogCategoryResource;
use Joaoolival\LaravelBlogEngine\Models\BlogPost;

/**
 * @mixin BlogPost
 */
class BlogPostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $galleryMedia = $this->getMedia('gallery');
        $bannerMedia = $galleryMedia->first();

        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'excerpt' => $this->excerpt,
            'content' => $this->rendered_content ?: $this->content,
            'published_at' => $this->published_at,
            'author' => new BlogAuthorResource($this->whenLoaded('author')),
            'category' => new BlogCategoryResource($this->whenLoaded('category')),
            'banner_image' => $bannerMedia ? [
                'url' => $bannerMedia->getUrl('webp'),
                'srcset' => $bannerMedia->getSrcset('webp'),
                'original_url' => $bannerMedia->getUrl(),
            ] : null,
            'gallery' => $galleryMedia->skip(1)->map(function ($media) {
                return [
                    'id' => $media->id,
                    'url' => $media->getUrl('webp'),
                    'srcset' => $media->getSrcset('webp'),
                    'original_url' => $media->getUrl(),
                ];
            })->values(),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
