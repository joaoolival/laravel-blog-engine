<?php

namespace Joaoolival\LaravelBlogEngine\Console;

use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use Joaoolival\LaravelBlogEngine\Models\BlogAuthor;
use Joaoolival\LaravelBlogEngine\Models\BlogCategory;
use Joaoolival\LaravelBlogEngine\Models\BlogPost;

use function Laravel\Prompts\info;
use function Laravel\Prompts\text;

class GenerateBlogContentCommand extends Command implements PromptsForMissingInput
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'blog:generate
                            {--authors= : The number of authors to create}
                            {--categories= : The number of categories to create}
                            {--posts= : The number of posts to create}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate dummy content for the blog (authors, categories, and posts)';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        info('Starting content generation...');

        $authorCount = (int) ($this->option('authors') ?? text(
            label: 'How many authors should be created?',
            default: '1',
            validate: fn (string $value) => is_numeric($value) && $value >= 0 ? null : 'Please enter a valid number.'
        ));

        $categoryCount = (int) ($this->option('categories') ?? text(
            label: 'How many categories should be created?',
            default: '1',
            validate: fn (string $value) => is_numeric($value) && $value >= 0 ? null : 'Please enter a valid number.'
        ));

        $postCount = (int) ($this->option('posts') ?? text(
            label: 'How many posts should be created?',
            default: '12',
            validate: fn (string $value) => is_numeric($value) && $value >= 0 ? null : 'Please enter a valid number.'
        ));

        $totalSteps = $authorCount + $categoryCount + $postCount;

        if ($totalSteps === 0) {
            info('Nothing to generate.');

            return;
        }

        $bar = $this->output->createProgressBar($totalSteps);
        $bar->start();

        // 1. Create Authors
        $authors = collect();
        if ($authorCount > 0) {
            foreach (range(1, $authorCount) as $i) {
                $authors->push(
                    BlogAuthor::factory()->withAvatar()->create()
                );
                $bar->advance();
            }
        } else {
            $authors = BlogAuthor::all();
        }

        // 2. Create Categories
        $categories = collect();
        if ($categoryCount > 0) {
            foreach (range(1, $categoryCount) as $i) {
                $categories->push(
                    BlogCategory::factory()->withBanner()->create()
                );
                $bar->advance();
            }
        } else {
            $categories = BlogCategory::all();
        }

        // 3. Create Posts
        if ($postCount > 0) {
            // Ensure we have at least one author and category to attach to
            if ($authors->isEmpty()) {
                $authors->push(BlogAuthor::factory()->withAvatar()->create());
            }
            if ($categories->isEmpty()) {
                $categories->push(BlogCategory::factory()->withBanner()->create());
            }

            foreach (range(1, $postCount) as $i) {
                BlogPost::factory()
                    ->withGallery()
                    ->recycle($authors)
                    ->recycle($categories)
                    ->create();
                $bar->advance();
            }
        }

        $bar->finish();
        $this->newLine();

        info("Successfully generated {$authorCount} authors, {$categoryCount} categories, and {$postCount} posts!");
    }
}
