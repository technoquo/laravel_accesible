<?php

namespace App\Livewire\Admin;

use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.admin')]
#[Title('Categorías')]
class CategoriesIndex extends Component
{
    public ?int $editingId = null;

    public bool $isCreating = false;

    // Form fields
    public string $stableKey = '';

    public string $sortOrder = '0';

    public string $nameEs = '';

    public string $slugEs = '';

    public string $descriptionEs = '';

    public string $nameEn = '';

    public string $slugEn = '';

    public string $descriptionEn = '';

    #[Computed]
    public function categories()
    {
        return Category::with('translations')->orderBy('sort_order')->get();
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

    public function edit(int $categoryId): void
    {
        $category = Category::with('translations')->findOrFail($categoryId);

        $this->editingId = $categoryId;
        $this->isCreating = false;
        $this->stableKey = $category->stable_key;
        $this->sortOrder = (string) $category->sort_order;

        foreach (['es', 'en'] as $locale) {
            $translation = $category->translationForLocale($locale);
            $suffix = ucfirst($locale);

            $this->{'name'.$suffix} = $translation?->name ?? '';
            $this->{'slug'.$suffix} = $translation?->slug ?? '';
            $this->{'description'.$suffix} = $translation?->description ?? '';
        }
    }

    public function save(): void
    {
        $editingId = $this->editingId;

        $this->validate([
            'stableKey' => ['required', 'string', 'max:100', 'alpha_dash', Rule::unique('categories', 'stable_key')->ignore($editingId)],
            'sortOrder' => ['required', 'integer', 'min:0'],
            'nameEs' => ['required', 'string', 'max:255'],
            'nameEn' => ['required', 'string', 'max:255'],
            'slugEs' => [
                'required', 'string', 'max:255',
                Rule::unique('category_translations', 'slug')
                    ->where('locale', 'es')
                    ->when($editingId, fn ($rule) => $rule->whereNot('category_id', $editingId)),
            ],
            'slugEn' => [
                'required', 'string', 'max:255',
                Rule::unique('category_translations', 'slug')
                    ->where('locale', 'en')
                    ->when($editingId, fn ($rule) => $rule->whereNot('category_id', $editingId)),
            ],
            'descriptionEs' => ['nullable', 'string', 'max:500'],
            'descriptionEn' => ['nullable', 'string', 'max:500'],
        ]);

        $category = $editingId ? Category::findOrFail($editingId) : new Category;
        $category->stable_key = $this->stableKey;
        $category->sort_order = (int) $this->sortOrder;
        $category->save();

        foreach (['es', 'en'] as $locale) {
            $suffix = ucfirst($locale);
            $category->translations()->updateOrCreate(
                ['locale' => $locale],
                [
                    'name' => $this->{'name'.$suffix},
                    'slug' => $this->{'slug'.$suffix},
                    'description' => $this->{'description'.$suffix} ?: null,
                ]
            );
        }

        $this->cancelEditing();
        unset($this->categories);

        session()->flash('success', $editingId ? 'Categoría actualizada.' : 'Categoría creada.');
    }

    public function delete(int $categoryId): void
    {
        Category::findOrFail($categoryId)->delete();

        unset($this->categories);

        session()->flash('success', 'Categoría eliminada.');
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
        $this->sortOrder = '0';
        $this->nameEs = '';
        $this->slugEs = '';
        $this->descriptionEs = '';
        $this->nameEn = '';
        $this->slugEn = '';
        $this->descriptionEn = '';
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.admin.categories-index');
    }
}
