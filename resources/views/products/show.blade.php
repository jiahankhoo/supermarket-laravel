<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $product->name }} - Laravel 超市系统</title>
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
                    @auth
                        @if(Auth::user()->isAdmin())
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('admin.dashboard') }}">
                                    <i class="fas fa-tachometer-alt"></i> 仪表板
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('admin.products.index') }}">
                                    <i class="fas fa-box"></i> 商品管理
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
        <!-- 面包屑导航 -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">首页</a></li>
                <li class="breadcrumb-item"><a href="{{ route('products.index') }}">商品列表</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $product->name }}</li>
            </ol>
        </nav>

        <div class="row">
            <!-- 商品图片 -->
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-body text-center">
                        @if($product->image_url)
                            <img src="{{ $product->image_url }}" alt="{{ $product->name }}" 
                                 class="img-fluid rounded" style="max-height: 400px; object-fit: cover;">
                        @else
                            <div class="bg-secondary text-white d-flex align-items-center justify-content-center" 
                                 style="height: 400px; font-size: 4rem;">
                                <i class="fas fa-image"></i>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- 商品信息 -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h2 class="card-title">{{ $product->name }}</h2>
                        
                        <!-- 分类标签 -->
                        <div class="mb-3">
                            <span class="badge bg-primary">{{ $product->productCategory->name ?? '未分类' }}</span>
                            @if($product->stock > 10)
                                <span class="badge bg-success">库存充足</span>
                            @elseif($product->stock > 0)
                                <span class="badge bg-warning">库存不足</span>
                            @else
                                <span class="badge bg-danger">缺货</span>
                            @endif
                        </div>

                        <!-- 价格 -->
                        <div class="mb-3">
                            <h3 class="text-success">¥{{ number_format($product->price, 2) }}</h3>
                        </div>

                        <!-- 库存信息 -->
                        <div class="mb-3">
                            <p class="mb-1">
                                <i class="fas fa-boxes"></i> 
                                <strong>库存:</strong> 
                                @if($product->stock > 0)
                                    <span class="text-success">{{ $product->stock }} 件</span>
                                @else
                                    <span class="text-danger">缺货</span>
                                @endif
                            </p>
                        </div>

                        <!-- 商品描述 -->
                        @if($product->description)
                            <div class="mb-4">
                                <h5><i class="fas fa-align-left"></i> 商品描述</h5>
                                <p class="text-muted">{{ $product->description }}</p>
                            </div>
                        @endif

                        <!-- 管理员信息 -->
                        @if($product->admin)
                            <div class="mb-4">
                                <p class="mb-1">
                                    <i class="fas fa-user-shield"></i> 
                                    <strong>管理员:</strong> {{ $product->admin->name }}
                                </p>
                            </div>
                        @endif

                        <!-- 操作按钮 -->
                        <div class="d-grid gap-2">
                            @auth
                                @if(Auth::user()->isUser() && $product->stock > 0)
                                    <form method="POST" action="{{ route('cart.add', $product) }}" class="d-inline">
                                        @csrf
                                        <div class="input-group mb-3">
                                            <span class="input-group-text">数量</span>
                                            <input type="number" class="form-control" name="quantity" value="1" min="1" max="{{ $product->stock }}">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-cart-plus"></i> 加入购物车
                                            </button>
                                        </div>
                                    </form>
                                @elseif(Auth::user()->isAdmin())
                                    <div class="btn-group w-100" role="group">
                                        <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-warning">
                                            <i class="fas fa-edit"></i> 编辑商品
                                        </a>
                                        <form method="POST" action="{{ route('admin.products.destroy', $product) }}" 
                                              class="d-inline" onsubmit="return confirm('确定要删除这个商品吗？')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger">
                                                <i class="fas fa-trash"></i> 删除商品
                                            </button>
                                        </form>
                                    </div>
                                @endif
                            @else
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i> 
                                    请 <a href="{{ route('login') }}">登录</a> 后购买商品
                                </div>
                            @endauth

                            @if($product->stock <= 0)
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle"></i> 
                                    该商品暂时缺货，请稍后再试
                                </div>
                            @endif
                        </div>

                        <!-- 返回按钮 -->
                        <div class="mt-3">
                            <a href="{{ route('home') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left"></i> 返回首页
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 