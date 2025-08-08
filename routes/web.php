<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\ContactController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function (Request $request) {
    $query = \App\Models\Product::with(['admin', 'productCategory']);

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
    $categories = \App\Models\Category::active()->ordered()->pluck('name');

    return view('welcome', compact('products', 'categories'));
})->name('home');

// 认证路由
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::post('/login', function (Request $request) {
    $credentials = $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    if (Auth::attempt($credentials)) {
        $request->session()->regenerate();
        return redirect()->intended('/');
    }

    return back()->withErrors([
        'email' => '提供的凭据不匹配我们的记录。',
    ])->onlyInput('email');
})->name('login.post');

Route::get('/register', function () {
    return view('auth.register');
})->name('register');

Route::post('/register', function (Request $request) {
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users',
        'password' => 'required|string|min:8|confirmed',
        'role' => 'required|in:admin,user',
    ]);

    $user = \App\Models\User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => bcrypt($request->password),
        'role' => $request->role,
    ]);

    auth()->login($user);

    return redirect()->route('home')->with('success', '注册成功！');
})->name('register.post');

Route::post('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/');
})->name('logout');

// 商品相关路由
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/search', [ProductController::class, 'search'])->name('products.search');
Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');

// 需要认证的路由
Route::middleware('auth')->group(function () {
    // 购物车路由
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add/{product}', [CartController::class, 'add'])->name('cart.add');
    Route::delete('/cart/{cartItem}', [CartController::class, 'remove'])->name('cart.remove');
    Route::put('/cart/{cartItem}', [CartController::class, 'update'])->name('cart.update');

    // 订单路由
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::get('/checkout', [OrderController::class, 'checkout'])->name('checkout');
    Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
    Route::put('/orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.update-status');
    
    // 取消订单相关路由
    Route::get('/orders/{order}/cancellation-form', [OrderController::class, 'showCancellationForm'])->name('orders.cancellation-form');
    Route::post('/orders/{order}/request-cancellation', [OrderController::class, 'requestCancellation'])->name('orders.request-cancellation');
    Route::post('/orders/{order}/respond-cancellation', [OrderController::class, 'respondToCancellation'])->name('orders.respond-cancellation');
    
    // 联系商家路由
    Route::get('/contact', [ContactController::class, 'showForm'])->name('contact.form');
    Route::post('/contact', [ContactController::class, 'submit'])->name('contact.submit');
    
    // 聊天路由
    Route::get('/chat', [ChatController::class, 'index'])->name('chat.index');
    Route::get('/chat/{user}', [ChatController::class, 'show'])->name('chat.show');
    Route::post('/chat/send/{user}', [ChatController::class, 'send'])->name('chat.send');
    Route::get('/chat/messages/{user}', [ChatController::class, 'getMessages'])->name('chat.messages');
    Route::get('/chat/unread-count/{user}', [ChatController::class, 'getUnreadCount'])->name('chat.unread-count');

// 文件访问路由
Route::get('/storage/{path}', function($path) {
    $filePath = storage_path('app/public/' . $path);
    
    // 调试信息
    \Log::info("文件访问请求", [
        'path' => $path,
        'filePath' => $filePath,
        'exists' => file_exists($filePath)
    ]);
    
    if (file_exists($filePath)) {
        return response()->file($filePath);
    }
    
    abort(404, "文件不存在: $filePath");
})->where('path', '.*');

    // 管理员路由
    Route::middleware('admin')->group(function () {
        Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');

        Route::get('/admin/products', [AdminController::class, 'products'])->name('admin.products.index');
        
        // 商品管理
        Route::get('/admin/products/create', [ProductController::class, 'create'])->name('admin.products.create');
        Route::post('/admin/products', [ProductController::class, 'store'])->name('admin.products.store');
        Route::get('/admin/products/{product}/edit', [ProductController::class, 'edit'])->name('admin.products.edit');
        Route::put('/admin/products/{product}', [ProductController::class, 'update'])->name('admin.products.update');
        Route::delete('/admin/products/{product}', [ProductController::class, 'destroy'])->name('admin.products.destroy');
        
        // 用户管理
        Route::get('/admin/users', [UserController::class, 'index'])->name('admin.users.index');
        Route::get('/admin/users/{user}', [UserController::class, 'show'])->name('admin.users.show');
        Route::get('/admin/users/{user}/edit', [UserController::class, 'edit'])->name('admin.users.edit');
        Route::put('/admin/users/{user}', [UserController::class, 'update'])->name('admin.users.update');
        Route::delete('/admin/users/{user}', [UserController::class, 'destroy'])->name('admin.users.destroy');
        
        // 分类管理
        Route::resource('admin/categories', CategoryController::class, ['as' => 'admin']);
        Route::get('/admin/categories-api', [CategoryController::class, 'getActiveCategories'])->name('admin.categories.api');
        
        // 联系消息管理
        Route::get('/admin/contact-messages', [ContactController::class, 'showAdminMessages'])->name('admin.contact-messages');
    });
});
