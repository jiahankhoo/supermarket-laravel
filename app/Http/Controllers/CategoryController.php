<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{
    public function index()
    {
        if (!Auth::user()->isAdmin()) {
            return redirect()->route('home')->with('error', '只有管理员可以管理分类');
        }

        $categories = Category::ordered()->get();
        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        if (!Auth::user()->isAdmin()) {
            return redirect()->route('home')->with('error', '只有管理员可以添加分类');
        }

        return view('admin.categories.create');
    }

    public function store(Request $request)
    {
        if (!Auth::user()->isAdmin()) {
            return redirect()->route('home')->with('error', '只有管理员可以添加分类');
        }

        $request->validate([
            'name' => 'required|string|max:255|unique:categories',
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:255',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        Category::create([
            'name' => $request->name,
            'description' => $request->description,
            'icon' => $request->icon,
            'sort_order' => $request->sort_order ?? 0,
            'is_active' => true,
        ]);

        return redirect()->route('admin.categories.index')->with('success', '分类添加成功');
    }

    public function show(Category $category)
    {
        if (!Auth::user()->isAdmin()) {
            return redirect()->route('home')->with('error', '只有管理员可以查看分类');
        }

        return view('admin.categories.show', compact('category'));
    }

    public function edit(Category $category)
    {
        if (!Auth::user()->isAdmin()) {
            return redirect()->route('home')->with('error', '只有管理员可以编辑分类');
        }

        return view('admin.categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        if (!Auth::user()->isAdmin()) {
            return redirect()->route('home')->with('error', '只有管理员可以编辑分类');
        }

        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:255',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $category->update([
            'name' => $request->name,
            'description' => $request->description,
            'icon' => $request->icon,
            'sort_order' => $request->sort_order ?? 0,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.categories.index')->with('success', '分类更新成功');
    }

    public function destroy(Category $category)
    {
        if (!Auth::user()->isAdmin()) {
            return redirect()->route('home')->with('error', '只有管理员可以删除分类');
        }

        // 检查是否有商品使用此分类
        if ($category->products()->count() > 0) {
            return redirect()->route('admin.categories.index')->with('error', '该分类下还有商品，无法删除');
        }

        $category->delete();

        return redirect()->route('admin.categories.index')->with('success', '分类删除成功');
    }

    // API方法：获取所有活跃分类（用于下拉选择）
    public function getActiveCategories()
    {
        $categories = Category::active()->ordered()->get(['id', 'name']);
        return response()->json($categories);
    }
}
