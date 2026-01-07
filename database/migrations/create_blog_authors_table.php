<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('blog_authors', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->boolean('is_visible')->default(true);
            $table->string('slug')->unique()->nullable();
            $table->string('email')->unique();
            $table->text('bio')->nullable();
            $table->string('github_handle')->nullable();
            $table->string('twitter_handle')->nullable();
            $table->string('linkedin_handle')->nullable();
            $table->string('instagram_handle')->nullable();
            $table->string('facebook_handle')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blog_authors');
    }
};
