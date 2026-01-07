<?php

namespace Joaoolival\LaravelBlogEngine\Http\Resources\Categories;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Joaoolival\LaravelBlogEngine\Models\BlogCategory;

/**
 * @mixin BlogCategory
 */
class BlogCategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $media = $this->getFirstMedia('banner_image');

        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'is_visible' => $this->is_visible,
            'seo_title' => $this->seo_title,
            'seo_description' => $this->seo_description,
            'banner_image' => $media ? [
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
