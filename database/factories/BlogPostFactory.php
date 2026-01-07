<?php

namespace Joaoolival\LaravelBlogEngine\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Joaoolival\LaravelBlogEngine\Models\BlogAuthor;
use Joaoolival\LaravelBlogEngine\Models\BlogCategory;
use Joaoolival\LaravelBlogEngine\Models\BlogPost;

/**
 * @extends Factory<BlogPost>
 */
class BlogPostFactory extends Factory
{
    protected $model = BlogPost::class;

    public function definition(): array
    {
        $title = fake()->sentence();

        return [
            'title' => $title,
            'slug' => Str::slug($title),
            'excerpt' => fake()->paragraph(),
            'content' => fake()->paragraphs(5, true),
            'tags' => fake()->words(3),
            'is_visible' => true,
            'published_at' => now(),
            'blog_author_id' => BlogAuthorFactory::new(),
            'blog_category_id' => BlogCategoryFactory::new(),
        ];
    }

    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'published_at' => null,
        ]);
    }

    public function scheduled(): static
    {
        return $this->state(fn (array $attributes) => [
            'published_at' => now()->addWeek(),
        ]);
    }

    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'published_at' => now()->subDay(),
            'is_visible' => true,
        ]);
    }

    public function hidden(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_visible' => false,
        ]);
    }

    public function forAuthor(BlogAuthor $author): static
    {
        return $this->state(fn (array $attributes) => [
            'blog_author_id' => $author->id,
        ]);
    }

    public function forCategory(BlogCategory $category): static
    {
        return $this->state(fn (array $attributes) => [
            'blog_category_id' => $category->id,
        ]);
    }
}
