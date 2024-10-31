<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(Request $request) 
    {
        $keyword = $request->keyword;

        $query = Category::query();

        if ($keyword) {
            $query->where('name', 'LIKE', "%{$keyword}%");
        }

        $categories = $query->paginate(15);

        $total = $query->count();

        return view ('admin.categories.index', compact('categories', 'keyword', 'total'));
    }

    public function store(Request $request) 
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $category = new Category($validated);
        $category->save();

        return redirect()->route('admin.categories.index')
                            ->with('flash_message', 'カテゴリを登録しました。');
    }
    public function update(Request $request, Category $category) 
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $category->update($request->all());

        return redirect()->route('admin.categories.index')
                            ->with('flash_message', 'カテゴリを編集しました。');
    }

    public function destroy(Category $category) 
    {
        $category->delete();
        return redirect()->route('admin.categories.index')
                            ->with('flash_message', 'カテゴリを削除しました。');
    }
}