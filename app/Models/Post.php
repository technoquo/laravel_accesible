<?php

namespace App\Models;

use App\PostStatus;
use Database\Factories\PostFactory;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    /** @use HasFactory<PostFactory> */
    use HasFactory, SoftDeletes;

    protected function casts(): array
    {
        return [
            'status' => PostStatus::class,
            'published_at' => 'datetime',
        ];
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function translations(): HasMany
    {
        return $this->hasMany(PostTranslation::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    public function translationForLocale(string $locale): ?PostTranslation
    {
        return $this->translations->firstWhere('locale', $locale);
    }

    #[Scope]
    protected function publishedForLocale(Builder $query, string $locale): Builder
    {
        return $query
            ->where('status', PostStatus::Published)
            ->where('published_at', '<=', now())
            ->whereHas('translations', fn (Builder $q) => $q->where('locale', $locale));
    }

    #[Scope]
    protected function published(Builder $query): Builder
    {
        return $query
            ->where('status', PostStatus::Published)
            ->where('published_at', '<=', now());
    }
}
