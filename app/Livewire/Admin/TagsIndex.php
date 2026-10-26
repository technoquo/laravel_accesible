<?php

namespace App\Livewire\Admin;

use App\Models\Tag;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.admin')]
#[Title('Etiquetas')]
class TagsIndex extends Component
{
    public ?int $editingId = null;

    public bool $isCreating = false;

    public string $stableKey = '';

    public string $nameEs = '';

    public string $slugEs = '';

    public string $nameEn = '';

    public string $slugEn = '';

    #[Computed]
    public function tags()
    {
        return Tag::with('translations')->orderBy('id')->get();
    }

    public function updatedNameEs(string $value): void
    {
        if (! $this->slugEs) {
            $this->slugEs = Str::slug($value);
        }

        if (! $this->stableKey) {
            $this->stableKey = Str::slug($value);
        }
    }

    public function create(): void
    {
        $this->isCreating = true;
        $this->editingId = null;
        $this->resetForm();
    }

    public function edit(int $tagId): void
    {
        $tag = Tag::with('translations')->findOrFail($tagId);

        $this->editingId = $tagId;
        $this->isCreating = false;
        $this->stableKey = $tag->stable_key;

        foreach (['es', 'en'] as $locale) {
            $translation = $tag->translationForLocale($locale);
            $suffix = ucfirst($locale);

            $this->{'name'.$suffix} = $translation?->name ?? '';
            $this->{'slug'.$suffix} = $translation?->slug ?? '';
        }
    }

    public function save(): void
    {
        $editingId = $this->editingId;

        $this->validate([
            'stableKey' => ['required', 'string', 'max:100', 'alpha_dash', Rule::unique('tags', 'stable_key')->ignore($editingId)],
            'nameEs' => ['required', 'string', 'max:255'],
            'nameEn' => ['required', 'string', 'max:255'],
            'slugEs' => [
                'required', 'string', 'max:255',
                Rule::unique('tag_translations', 'slug')
                    ->where('locale', 'es')
                    ->when($editingId, fn ($rule) => $rule->whereNot('tag_id', $editingId)),
            ],
            'slugEn' => [
                'required', 'string', 'max:255',
                Rule::unique('tag_translations', 'slug')
                    ->where('locale', 'en')
                    ->when($editingId, fn ($rule) => $rule->whereNot('tag_id', $editingId)),
            ],
        ]);

        $tag = $editingId ? Tag::findOrFail($editingId) : new Tag;
        $tag->stable_key = $this->stableKey;
        $tag->save();

        foreach (['es', 'en'] as $locale) {
            $suffix = ucfirst($locale);
            $tag->translations()->updateOrCreate(
                ['locale' => $locale],
                [
                    'name' => $this->{'name'.$suffix},
                    'slug' => $this->{'slug'.$suffix},
                ]
            );
        }

        $this->cancelEditing();
        unset($this->tags);

        session()->flash('success', $editingId ? 'Etiqueta actualizada.' : 'Etiqueta creada.');
    }

    public function delete(int $tagId): void
    {
        Tag::findOrFail($tagId)->delete();

        unset($this->tags);

        session()->flash('success', 'Etiqueta eliminada.');
    }

    public function cancelEditing(): void
    {
        $this->editingId = null;
        $this->isCreating = false;
        $this->resetForm();
    }

    private function resetForm(): void
    {
        $this->stableKey = '';
        $this->nameEs = '';
        $this->slugEs = '';
        $this->nameEn = '';
        $this->slugEn = '';
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.admin.tags-index');
    }
}
