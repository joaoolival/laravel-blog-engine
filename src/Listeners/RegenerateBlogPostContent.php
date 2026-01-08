<?php

namespace Joaoolival\LaravelBlogEngine\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Joaoolival\LaravelBlogEngine\Models\BlogPost;
use Spatie\MediaLibrary\ResponsiveImages\Events\ResponsiveImagesGeneratedEvent;

class RegenerateBlogPostContent implements ShouldQueue
{
    /**
     * Handle the event.
     */
    public function handle(ResponsiveImagesGeneratedEvent $event): void
    {
        $media = $event->media;

        // Only process if the model is a BlogPost and collection is correct
        if (
            $media->model_type !== BlogPost::class ||
            ! ($media->model instanceof BlogPost)
        ) {
            return;
        }

        // We specifically care about content images being optimized
        // as they are embedded in the rendered content
        if ($media->collection_name !== 'content-attachments') {
            return;
        }

        // Regenerate the content which will now include the optimized image URLs
        Log::info('Regenerating rendered content for blog post: '.$media->model->id);
        $media->model->regenerateRenderedContent();
    }
}
