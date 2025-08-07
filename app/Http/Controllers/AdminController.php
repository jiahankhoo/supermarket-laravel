<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Order;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function dashboard()
    {
        if (!Auth::user()->isAdmin()) {
            return redirect()->route('home')->with('error', '只有管理员可以访问管理面板');
        }

        $products = Auth::user()->products;
        $orders = Order::with('user')->latest()->take(5)->get();
        $totalSales = Order::where('status', 'completed')->sum('total_amount');

        $stats = [
            'totalProducts' => $products->count(),
            'totalOrders' => Order::count(),
            'totalSales' => $totalSales,
            'lowStockProducts' => $products->where('stock', '<=', 10)->where('stock', '>', 0)->count(),
        ];

        return view('admin.dashboard', compact('products', 'orders', 'stats'));
    }

    public function products(Request $request)
    {
        if (!Auth::user()->isAdmin()) {
            return redirect()->route('home')->with('error', '只有管理员可以管理商品');
        }

        $query = Auth::user()->products()->with('productCategory');

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

        // 库存状态筛选
        if ($request->filled('stock_status')) {
            switch ($request->stock_status) {
                case 'in_stock':
                    $query->where('stock', '>', 0);
                    break;
                case 'low_stock':
                    $query->where('stock', '<=', 10)->where('stock', '>', 0);
                    break;
                case 'out_of_stock':
                    $query->where('stock', 0);
                    break;
            }
        }

        $products = $query->latest()->get();

        // 获取所有活跃分类用于筛选
        $categories = Category::active()->ordered()->pluck('name');

        return view('admin.products', compact('products', 'categories'));
    }
} 