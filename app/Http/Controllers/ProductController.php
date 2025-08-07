<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['admin', 'productCategory']);

        // 搜索功能
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('productCategory', function($catQuery) use ($search) {
                      $catQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // 分类筛选
        if ($request->filled('category')) {
            $query->whereHas('productCategory', function($catQuery) use ($request) {
                $catQuery->where('name', $request->category);
            });
        }

        // 价格范围筛选
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        // 只显示有库存的商品
        $query->where('stock', '>', 0);

        $products = $query->latest()->get();

        // 获取所有活跃分类用于筛选
        $categories = Category::active()->ordered()->pluck('name');

        return view('products.index', compact('products', 'categories'));
    }

    public function show(Product $product)
    {
        return view('products.show', compact('product'));
    }

    public function create()
    {
        if (!Auth::user()->isAdmin()) {
            return redirect()->route('home')->with('error', '只有管理员可以添加商品');
        }
        
        $categories = Category::active()->ordered()->get();
        return view('admin.create', compact('categories'));
    }

    public function store(Request $request)
    {
        if (!Auth::user()->isAdmin()) {
            return redirect()->route('home')->with('error', '只有管理员可以添加商品');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'image_url' => 'nullable|url',
            'image_file' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:512000',
        ]);

        $imageUrl = null;

        // 处理图片上传
        if ($request->hasFile('image_file') && $request->file('image_file')->isValid()) {
            // 上传本地文件
            $file = $request->file('image_file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('products', $fileName, 'public');
            $imageUrl = asset('storage/' . $filePath);
        } elseif ($request->filled('image_url')) {
            // 使用网络图片URL
            $imageUrl = $request->image_url;
        }

        $product = Product::create([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'stock' => $request->stock,
            'category_id' => $request->category_id,
            'image_url' => $imageUrl,
            'admin_id' => Auth::id(),
        ]);

        return redirect()->route('admin.products.index')->with('success', '商品添加成功');
    }

    public function edit(Product $product)
    {
        if (!Auth::user()->isAdmin() || $product->admin_id !== Auth::id()) {
            return redirect()->route('home')->with('error', '无权限编辑此商品');
        }

        $categories = Category::active()->ordered()->get();
        return view('admin.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        if (!Auth::user()->isAdmin() || $product->admin_id !== Auth::id()) {
            return redirect()->route('home')->with('error', '无权限编辑此商品');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'image_url' => 'nullable|url',
            'image_file' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:512000',
        ]);

        $imageUrl = $product->image_url; // 保持原有图片

        // 处理图片上传
        if ($request->hasFile('image_file') && $request->file('image_file')->isValid()) {
            // 上传本地文件
            $file = $request->file('image_file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('products', $fileName, 'public');
            $imageUrl = asset('storage/' . $filePath);
        } elseif ($request->filled('image_url')) {
            // 使用网络图片URL
            $imageUrl = $request->image_url;
        }

        $product->update([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'stock' => $request->stock,
            'category_id' => $request->category_id,
            'image_url' => $imageUrl,
        ]);

        return redirect()->route('admin.products.index')->with('success', '商品更新成功');
    }

    public function destroy(Product $product)
    {
        if (!Auth::user()->isAdmin() || $product->admin_id !== Auth::id()) {
            return redirect()->route('home')->with('error', '无权限删除此商品');
        }

        $product->delete();

        return redirect()->route('admin.products.index')->with('success', '商品删除成功');
    }

    public function search(Request $request)
    {
        $query = $request->get('q');
        
        if ($query) {
            $products = Product::where('name', 'like', "%{$query}%")
                ->orWhere('description', 'like', "%{$query}%")
                ->orWhere('category', 'like', "%{$query}%")
                ->with('admin')
                ->get();
        } else {
            $products = collect();
        }

        return view('products.search', compact('products', 'query'));
    }
} 