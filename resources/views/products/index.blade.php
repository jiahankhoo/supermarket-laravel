<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>商品列表 - Laravel 超市系统</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <!-- 导航栏 -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="{{ route('home') }}">
                <i class="fas fa-store"></i> Laravel 超市系统
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('home') }}">
                            <i class="fas fa-home"></i> 首页
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="{{ route('products.index') }}">
                            <i class="fas fa-box"></i> 商品列表
                        </a>
                    </li>
                    @auth
                        @if(Auth::user()->isAdmin())
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('admin.dashboard') }}">
                                    <i class="fas fa-tachometer-alt"></i> 仪表板
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('admin.products.index') }}">
                                    <i class="fas fa-cog"></i> 商品管理
                                </a>
                            </li>
                        @else
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('cart.index') }}">
                                    <i class="fas fa-shopping-cart"></i> 购物车
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('orders.index') }}">
                                    <i class="fas fa-shopping-bag"></i> 我的订单
                                </a>
                            </li>
                        @endif
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('chat.index') }}">
                                <i class="fas fa-comments"></i> {{ Auth::user()->isAdmin() ? '客户聊天' : '在线客服' }}
                            </a>
                        </li>
                    @endauth
                </ul>
                <ul class="navbar-nav">
                    @guest
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">
                                <i class="fas fa-sign-in-alt"></i> 登录
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('register') }}">
                                <i class="fas fa-user-plus"></i> 注册
                            </a>
                        </li>
                    @else
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user"></i> {{ Auth::user()->name }}
                                @if(Auth::user()->isAdmin())
                                    <span class="badge bg-warning">管理员</span>
                                @else
                                    <span class="badge bg-info">用户</span>
                                @endif
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="{{ route('home') }}">
                                    <i class="fas fa-home"></i> 返回首页
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}" class="d-inline">
                                        @csrf
                                        <button type="submit" class="dropdown-item">
                                            <i class="fas fa-sign-out-alt"></i> 退出登录
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @endguest
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <!-- 页面标题 -->
        <div class="row mb-4">
            <div class="col">
                <h2><i class="fas fa-box"></i> 商品列表</h2>
                <p class="text-muted">浏览所有可用商品</p>
            </div>
        </div>

        <!-- 搜索和筛选栏 -->
        <div class="card mb-4">
            <div class="card-header">
                <h5><i class="fas fa-search"></i> 搜索和筛选商品</h5>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('products.index') }}" class="row g-3">
                    <div class="col-md-4">
                        <label for="search" class="form-label">搜索商品</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                            <input type="text" class="form-control" id="search" name="search" 
                                   placeholder="搜索商品名称、描述或分类..." 
                                   value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label for="category" class="form-label">分类筛选</label>
                        <select class="form-select" id="category" name="category">
                            <option value="">所有分类</option>
                            @foreach($categories as $category)
                                <option value="{{ $category }}" {{ request('category') == $category ? 'selected' : '' }}>
                                    {{ $category }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="min_price" class="form-label">最低价格</label>
                        <input type="number" class="form-control" id="min_price" name="min_price" 
                               placeholder="¥0" value="{{ request('min_price') }}" step="0.01" min="0">
                    </div>
                    <div class="col-md-2">
                        <label for="max_price" class="form-label">最高价格</label>
                        <input type="number" class="form-control" id="max_price" name="max_price" 
                               placeholder="¥999" value="{{ request('max_price') }}" step="0.01" min="0">
                    </div>
                    <div class="col-md-1">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                </form>
                
                @if(request('search') || request('category') || request('min_price') || request('max_price'))
                    <div class="mt-3">
                        <a href="{{ route('products.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-times"></i> 清除筛选
                        </a>
                        <small class="text-muted ms-2">
                            找到 {{ $products->count() }} 个商品
                        </small>
                    </div>
                @endif
            </div>
        </div>

        <!-- 商品网格 -->
        <div class="row">
            @forelse($products as $product)
                <div class="col-md-4 col-lg-3 mb-4">
                    <div class="card h-100 shadow-sm">
                        <!-- 商品图片 -->
                        <div class="card-img-top text-center p-3" style="height: 200px;">
                            @if($product->image_url)
                                <img src="{{ $product->image_url }}" alt="{{ $product->name }}" 
                                     class="img-fluid" style="max-height: 100%; object-fit: cover;">
                            @else
                                <div class="bg-secondary text-white d-flex align-items-center justify-content-center h-100">
                                    <i class="fas fa-image fa-3x"></i>
                                </div>
                            @endif
                        </div>

                        <div class="card-body d-flex flex-column">
                            <!-- 商品名称 -->
                            <h5 class="card-title">{{ $product->name }}</h5>
                            
                            <!-- 分类标签 -->
                            <div class="mb-2">
                                <span class="badge bg-primary">{{ $product->productCategory->name ?? '未分类' }}</span>
                                @if($product->stock > 10)
                                    <span class="badge bg-success">库存充足</span>
                                @elseif($product->stock > 0)
                                    <span class="badge bg-warning">库存不足</span>
                                @else
                                    <span class="badge bg-danger">缺货</span>
                                @endif
                            </div>

                            <!-- 商品描述 -->
                            @if($product->description)
                                <p class="card-text text-muted small">
                                    {{ Str::limit($product->description, 60) }}
                                </p>
                            @endif

                            <!-- 价格和库存 -->
                            <div class="mb-3">
                                <h6 class="text-success mb-1">¥{{ number_format($product->price, 2) }}</h6>
                                <small class="text-muted">
                                    <i class="fas fa-boxes"></i> 库存: {{ $product->stock }} 件
                                </small>
                            </div>

                            <!-- 操作按钮 -->
                            <div class="mt-auto">
                                <div class="d-grid gap-2">
                                    <a href="{{ route('products.show', $product) }}" class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-eye"></i> 查看详情
                                    </a>
                                    
                                    @auth
                                        @if(Auth::user()->isUser() && $product->stock > 0)
                                            <form method="POST" action="{{ route('cart.add', $product) }}" class="d-inline">
                                                @csrf
                                                <input type="hidden" name="quantity" value="1">
                                                <button type="submit" class="btn btn-success btn-sm w-100">
                                                    <i class="fas fa-cart-plus"></i> 加入购物车
                                                </button>
                                            </form>
                                        @elseif(Auth::user()->isAdmin())
                                            <div class="btn-group btn-group-sm w-100" role="group">
                                                <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-warning">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form method="POST" action="{{ route('admin.products.destroy', $product) }}" 
                                                      class="d-inline" onsubmit="return confirm('确定要删除这个商品吗？')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        @endif
                                    @else
                                        <div class="alert alert-info py-2 mb-0">
                                            <small>
                                                <i class="fas fa-info-circle"></i> 
                                                请 <a href="{{ route('login') }}">登录</a> 后购买
                                            </small>
                                        </div>
                                    @endauth
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="text-center py-5">
                        <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">暂无商品</h5>
                        <p class="text-muted">暂时没有可用的商品</p>
                    </div>
                </div>
            @endforelse
        </div>

        <!-- 分页功能暂未启用 -->
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 