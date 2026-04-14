<?php

namespace App\Http\Controllers\Catalog;

use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryStoreRequest;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(): View
    {
        $categories = Category::orderBy('name')->paginate(20);
        return view('catalog.categories.index', compact('categories'));
    }

    public function create(): View
    {
        return view('catalog.categories.create');
    }

    public function store(CategoryStoreRequest $request): RedirectResponse
    {
        Category::create($request->validated());
        return redirect()->route('categories.index')
            ->with('success', 'Categoria creada exitosamente.');
    }

    public function edit(Category $category): View
    {
        return view('catalog.categories.edit', compact('category'));
    }

    public function update(CategoryStoreRequest $request, Category $category): RedirectResponse
    {
        $category->update($request->validated());
        return redirect()->route('categories.index')
            ->with('success', 'Categoria actualizada exitosamente.');
    }

    public function toggle(Category $category): RedirectResponse
    {
        $category->update(['is_active' => !$category->is_active]);
        $status = $category->is_active ? 'activada' : 'desactivada';
        return redirect()->route('categories.index')
            ->with('success', "Categoria {$status} exitosamente.");
    }
}
