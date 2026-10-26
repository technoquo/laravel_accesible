<?php

namespace App\Models;

use Database\Factories\CategoryFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    /** @use HasFactory<CategoryFactory> */
    use HasFactory;

    protected $fillable = ['stable_key', 'sort_order'];

    public function translations(): HasMany
    {
        return $this->hasMany(CategoryTranslation::class);
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    public function translationForLocale(string $locale): ?CategoryTranslation
    {
        return $this->translations->firstWhere('locale', $locale);
    }
}
