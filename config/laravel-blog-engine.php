<?php

return [

    'resources' => [
        'posts' => [
            'label' => 'Blog Post',
            'plural_label' => 'Blog Posts',
            'navigation_label' => 'Blog Posts',
            'navigation_group' => 'Blog',
            'navigation_sort' => 1,
            'navigation_icon' => 'heroicon-o-pencil',
            'slug' => 'blog-posts',
        ],
        'authors' => [
            'label' => 'Blog Author',
            'plural_label' => 'Blog Authors',
            'navigation_label' => 'Blog Authors',
            'navigation_group' => 'Blog',
            'navigation_sort' => 2,
            'navigation_icon' => 'heroicon-o-users',
            'slug' => 'blog-authors',
        ],
        'categories' => [
            'label' => 'Blog Category',
            'plural_label' => 'Blog Categories',
            'navigation_label' => 'Blog Categories',
            'navigation_group' => 'Blog',
            'navigation_sort' => 3,
            'navigation_icon' => 'heroicon-o-rectangle-stack',
            'slug' => 'blog-categories',
        ],
    ],
];
