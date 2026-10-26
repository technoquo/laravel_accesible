<?php

namespace App\Livewire\Admin;

use App\Models\Post;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
#[Title('Posts')]
class PostsIndex extends Component
{
    use WithPagination;

    public string $search = '';

    public string $statusFilter = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function delete(int $postId): void
    {
        $post = Post::findOrFail($postId);
        $post->delete();

        session()->flash('success', 'Post eliminado correctamente.');
    }

    #[Computed]
    public function posts()
    {
        return Post::with(['translations', 'category.translations'])
            ->when($this->search, fn ($q) => $q->whereHas(
                'translations',
                fn ($q) => $q->where('title', 'like', '%'.$this->search.'%')
            ))
            ->when($this->statusFilter, fn ($q) => $q->where('status', $this->statusFilter))
            ->latest()
            ->paginate(20);
    }

    public function render()
    {
        return view('livewire.admin.posts-index');
    }
}
