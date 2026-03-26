<?php

namespace Joaoolival\LaravelBlogEngine\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Joaoolival\LaravelBlogEngine\Models\BlogAuthor;

/**
 * @extends Factory<BlogAuthor>
 */
class BlogAuthorFactory extends Factory
{
    protected $model = BlogAuthor::class;

    public function definition(): array
    {
        $name = fake()->name();

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'email' => fake()->unique()->safeEmail(),
            'bio' => fake()->paragraph(),
            'is_visible' => true,
            'github_handle' => fake()->optional()->userName(),
            'twitter_handle' => fake()->optional()->userName(),
            'linkedin_handle' => fake()->optional()->userName(),
            'instagram_handle' => fake()->optional()->userName(),
            'facebook_handle' => fake()->optional()->userName(),
        ];
    }

    public function hidden(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_visible' => false,
        ]);
    }

    public function withAvatar(): static
    {
        return $this->afterCreating(function (BlogAuthor $author) {
            $url = 'https://ui-avatars.com/api/?name='.urlencode($author->name).'&color=7F9CF5&background=EBF4FF';

            try {
                $author->addMediaFromUrl($url)
                    ->toMediaCollection('avatar');
            } catch (\Throwable) {
                //
            }
        });
    }
}
