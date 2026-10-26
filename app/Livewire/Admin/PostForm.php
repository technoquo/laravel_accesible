<?php

namespace App\Livewire\Admin;

use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use App\PostStatus;
use App\Services\MarkdownService;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.admin')]
class PostForm extends Component
{
    public ?int $postId = null;

    public string $activeLocale = 'es';

    public bool $showPreview = false;

    // Common fields
    public ?int $categoryId = null;

    public string $status = 'draft';

    public string $publishedAt = '';

    public array $tagIds = [];

    // Spanish translation
    public string $titleEs = '';

    public string $slugEs = '';

    public string $excerptEs = '';

    public string $markdownEs = '';

    public string $metaTitleEs = '';

    public string $metaDescriptionEs = '';

    // English translation
    public string $titleEn = '';

    public string $slugEn = '';

    public string $excerptEn = '';

    public string $markdownEn = '';

    public string $metaTitleEn = '';

    public string $metaDescriptionEn = '';

    public function mount(?Post $post = null): void
    {
        if ($post?->exists) {
            $this->postId = $post->id;
            $this->categoryId = $post->category_id;
            $this->status = $post->status->value;
            $this->publishedAt = $post->published_at?->format('Y-m-d\TH:i') ?? '';
            $this->tagIds = $post->tags->pluck('id')->toArray();

            $post->loadMissing('translations');

            foreach (['es', 'en'] as $locale) {
                $translation = $post->translationForLocale($locale);
                $suffix = ucfirst($locale);

                $this->{'title'.$suffix} = $translation?->title ?? '';
                $this->{'slug'.$suffix} = $translation?->slug ?? '';
                $this->{'excerpt'.$suffix} = $translation?->excerpt ?? '';
                $this->{'markdown'.$suffix} = $translation?->markdown_content ?? '';
                $this->{'metaTitle'.$suffix} = $translation?->meta_title ?? '';
                $this->{'metaDescription'.$suffix} = $translation?->meta_description ?? '';
            }
        }
    }

    public function updatedTitleEs(string $value): void
    {
        if (! $this->slugEs) {
            $this->slugEs = Str::slug($value);
        }
    }

    public function updatedTitleEn(string $value): void
    {
        if (! $this->slugEn) {
            $this->slugEn = Str::slug($value);
        }
    }

    public function togglePreview(): void
    {
        $this->showPreview = ! $this->showPreview;
    }

    #[Computed]
    public function previewHtml(): string
    {
        $markdown = $this->activeLocale === 'es' ? $this->markdownEs : $this->markdownEn;

        return app(MarkdownService::class)->toHtml($markdown);
    }

    #[Computed]
    public function categories()
    {
        return Category::with('translations')->orderBy('sort_order')->get();
    }

    #[Computed]
    public function tags()
    {
        return Tag::with('translations')->orderBy('id')->get();
    }

    public function save(): void
    {
        $this->validate($this->validationRules());

        $post = $this->postId ? Post::findOrFail($this->postId) : new Post;
        $post->author_id = auth()->id();
        $post->category_id = $this->categoryId;
        $post->status = PostStatus::from($this->status);
        $post->published_at = $this->status === PostStatus::Published->value
            ? ($this->publishedAt ? Carbon::parse($this->publishedAt) : now())
            : null;
        $post->save();

        foreach (['es', 'en'] as $locale) {
            $suffix = ucfirst($locale);
            $post->translations()->updateOrCreate(
                ['locale' => $locale],
                [
                    'title' => $this->{'title'.$suffix},
                    'slug' => $this->{'slug'.$suffix},
                    'excerpt' => $this->{'excerpt'.$suffix} ?: null,
                    'markdown_content' => $this->{'markdown'.$suffix},
                    'meta_title' => $this->{'metaTitle'.$suffix} ?: null,
                    'meta_description' => $this->{'metaDescription'.$suffix} ?: null,
                ]
            );
        }

        $post->tags()->sync($this->tagIds);

        session()->flash('success', $this->postId ? 'Post actualizado.' : 'Post creado.');

        $this->redirect(route('admin.posts.index'), navigate: true);
    }

    /** @return array<string, mixed> */
    private function validationRules(): array
    {
        $postId = $this->postId;

        return [
            'titleEs' => ['required', 'string', 'max:255'],
            'titleEn' => ['required', 'string', 'max:255'],
            'slugEs' => [
                'required', 'string', 'max:255',
                Rule::unique('post_translations', 'slug')
                    ->where('locale', 'es')
                    ->when($postId, fn ($rule) => $rule->whereNot('post_id', $postId)),
            ],
            'slugEn' => [
                'required', 'string', 'max:255',
                Rule::unique('post_translations', 'slug')
                    ->where('locale', 'en')
                    ->when($postId, fn ($rule) => $rule->whereNot('post_id', $postId)),
            ],
            'markdownEs' => ['required', 'string'],
            'markdownEn' => ['required', 'string'],
            'excerptEs' => ['nullable', 'string', 'max:500'],
            'excerptEn' => ['nullable', 'string', 'max:500'],
            'metaTitleEs' => ['nullable', 'string', 'max:70'],
            'metaTitleEn' => ['nullable', 'string', 'max:70'],
            'metaDescriptionEs' => ['nullable', 'string', 'max:160'],
            'metaDescriptionEn' => ['nullable', 'string', 'max:160'],
            'categoryId' => ['nullable', 'integer', 'exists:categories,id'],
            'status' => ['required', Rule::in(['draft', 'published'])],
            'publishedAt' => ['nullable', 'date'],
            'tagIds' => ['array'],
            'tagIds.*' => ['integer', 'exists:tags,id'],
        ];
    }

    public function render()
    {
        return view('livewire.admin.post-form')
            ->title($this->postId ? 'Editar post' : 'Nuevo post');
    }
}
