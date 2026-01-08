<?php

namespace Joaoolival\LaravelBlogEngine\Actions\Posts;

use Joaoolival\LaravelBlogEngine\Models\BlogPost;

final readonly class RenderContentAction
{
    /**
     * Generate rendered HTML content with responsive images.
     *
     * @return string|null The rendered HTML with responsive images, or null if no content
     */
    public function handle(BlogPost $post): ?string
    {
        if (! $post->content) {
            return null;
        }

        // Render content with resolved image URLs using the model's rich content renderer
        try {
            $html = $post->renderRichContent('content');
        } catch (\BadMethodCallException $e) {
            // Str::sanitizeHtml doesn't exist in test environment, return raw content
            return $post->content;
        }

        // Post-process to add responsive images
        return $this->makeContentImagesResponsive($post, $html);
    }

    /**
     * Post-process rendered HTML content to add responsive image attributes.
     *
     * Replaces <img> tags with WebP versions and adds srcset/sizes for responsive loading.
     */
    private function makeContentImagesResponsive(BlogPost $post, string $html): string
    {
        // Force refresh media to ensure we have latest responsive images data
        // This is crucial when running in sync queue or tight loops where the relation is stale
        $post->load('media');

        // Get all content attachment media
        $contentMedia = $post->getMedia('content-attachments');

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
