<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class NewsArticle extends Model
{
    protected $fillable = [
        'category_id',
        'district_id',
        'title',
        'slug',
        'summary',
        'content',
        'image_url',
        'document_no',
        'author_name',
        'is_featured',
        'is_breaking',
        'views',
        'published_at',
        'rss_guid',
        'source_url'
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'is_featured' => 'boolean',
        'is_breaking' => 'boolean',
    ];

    /**
     * Get the category that this article belongs to.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the district that this article belongs to (if any).
     */
    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class);
    }

    /**
     * Get the tags associated with this article.
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'article_tag');
    }

    /**
     * Scope query to only include featured articles.
     */
    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope query to only include breaking articles.
     */
    public function scopeBreaking(Builder $query): Builder
    {
        return $query->where('is_breaking', true);
    }

    /**
     * Get the article's image URL, falling back to a default premium news image if none is present.
     */
    public function getImageUrlAttribute($value)
    {
        return $value ?: asset('images/default-news.jpg');
    }
}
