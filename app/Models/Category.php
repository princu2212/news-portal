<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $fillable = ['name', 'slug', 'color'];

    /**
     * Get the articles associated with this category.
     */
    public function newsArticles(): HasMany
    {
        return $this->hasMany(NewsArticle::class);
    }
}
