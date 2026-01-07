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

        // Render content with resolved image URLs using the model's rich content renderer
        // then post-process to add responsive images
        $renderedContent = $this->content
            ? $this->makeContentImagesResponsive($this->resource->renderRichContent('content'))
            : null;

        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'excerpt' => $this->excerpt,
            'content' => $renderedContent,
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

    /**
     * Post-process rendered HTML content to add responsive image attributes.
     *
     * Replaces <img> tags with WebP versions and adds srcset/sizes for responsive loading.
     */
    private function makeContentImagesResponsive(string $html): string
    {
        // Get all content attachment media
        $contentMedia = $this->resource->getMedia('content-attachments');

        if ($contentMedia->isEmpty()) {
            return $html;
        }

        // Build a map keyed by media ID (extracted from URL path like /storage/16/image.png)
        $mediaMap = [];
        foreach ($contentMedia as $media) {
            $originalUrl = $media->getUrl();
            // Extract media ID from URL path - format: /storage/{id}/filename
            $mediaMap[(string) $media->id] = [
                'originalUrl' => $originalUrl,
                'url' => $media->getUrl('webp'),
                'srcset' => $media->getSrcset('webp'),
            ];
        }

        // Replace img src attributes with WebP versions and add srcset
        return preg_replace_callback(
            '/<img\s+([^>]*?)src=["\']([^"\']+)["\']([^>]*?)>/i',
            function ($matches) use ($mediaMap) {
                $before = $matches[1];
                $src = $matches[2];
                $after = $matches[3];

                // Extract media ID from URL - matches /storage/{id}/ pattern
                if (preg_match('#/storage/(\d+)/#', $src, $idMatch)) {
                    $mediaId = $idMatch[1];

                    if (isset($mediaMap[$mediaId])) {
                        $data = $mediaMap[$mediaId];
                        $srcset = htmlspecialchars($data['srcset']);
                        $webpUrl = htmlspecialchars($data['url']);

                        // Add responsive attributes if srcset is available
                        if ($srcset) {
                            return sprintf(
                                '<img %ssrc="%s" srcset="%s" sizes="(max-width: 768px) 100vw, 800px" loading="lazy" decoding="async"%s>',
                                $before,
                                $webpUrl,
                                $srcset,
                                $after
                            );
                        }

                        // Use WebP URL at minimum
                        return sprintf('<img %ssrc="%s"%s>', $before, $webpUrl, $after);
                    }
                }

                // No match found, return original
                return $matches[0];
            },
            $html
        ) ?? $html;
    }
}
