<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tag extends Model
{
    protected $fillable = ['name', 'slug'];

    /**
     * Get the articles associated with this tag.
     */
    public function newsArticles(): BelongsToMany
    {
        return $this->belongsToMany(NewsArticle::class, 'article_tag');
    }
}
