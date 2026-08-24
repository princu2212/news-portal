<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class District extends Model
{
    protected $fillable = ['name', 'slug'];

    /**
     * Get the articles associated with this district.
     */
    public function newsArticles(): HasMany
    {
        return $this->hasMany(NewsArticle::class);
    }
}
