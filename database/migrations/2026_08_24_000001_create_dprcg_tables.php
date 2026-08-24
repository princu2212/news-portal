<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Hindi Name, e.g., 'प्रेस विज्ञप्ति'
            $table->string('slug')->unique(); // URL Slug, e.g., 'press-releases'
            $table->string('color')->nullable(); // Styling color code if needed
            $table->timestamps();
        });

        Schema::create('districts', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Hindi Name, e.g., 'रायपुर'
            $table->string('slug')->unique(); // URL Slug, e.g., 'raipur'
            $table->timestamps();
        });

        Schema::create('news_articles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->foreignId('district_id')->nullable()->constrained()->onDelete('set null');
            $table->string('title'); // Hindi Headline
            $table->string('slug')->unique();
            $table->text('summary')->nullable(); // Brief snippet
            $table->text('content'); // Full Hindi press release body
            $table->string('image_url')->nullable();
            $table->string('document_no')->nullable(); // Official circular/press code e.g., PR/No/2026/102
            $table->string('author_name')->default('जनसंपर्क ब्यूरो');
            $table->boolean('is_featured')->default(false); // Highlighted banner articles
            $table->boolean('is_breaking')->default(false); // Scrolling news ticker
            $table->integer('views')->default(0);
            $table->timestamp('published_at')->useCurrent();
            $table->timestamps();
        });

        Schema::create('tags', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // Hindi e.g., 'विकास'
            $table->string('slug')->unique(); // e.g., 'development'
            $table->timestamps();
        });

        Schema::create('article_tag', function (Blueprint $table) {
            $table->id();
            $table->foreignId('news_article_id')->constrained()->onDelete('cascade');
            $table->foreignId('tag_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('article_tag');
        Schema::dropIfExists('tags');
        Schema::dropIfExists('news_articles');
        Schema::dropIfExists('districts');
        Schema::dropIfExists('categories');
    }
};
