<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>购物车 - Laravel 超市系统</title>
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
                                <a class="nav-link active" href="{{ route('cart.index') }}">
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
                <h2><i class="fas fa-shopping-cart"></i> 购物车</h2>
                <p class="text-muted">管理您的购物车商品</p>
            </div>
        </div>

        @if($cartItems->count() > 0)
            <div class="row">
                <!-- 购物车商品列表 -->
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h5><i class="fas fa-list"></i> 商品列表</h5>
                        </div>
                        <div class="card-body">
                            @foreach($cartItems as $item)
                                <div class="row mb-3 align-items-center">
                                    <div class="col-md-2">
                                        @if($item->product->image_url)
                                            <img src="{{ $item->product->image_url }}" alt="{{ $item->product->name }}" 
                                                 class="img-thumbnail" style="width: 80px; height: 80px; object-fit: cover;">
                                        @else
                                            <div class="bg-secondary text-white d-flex align-items-center justify-content-center" 
                                                 style="width: 80px; height: 80px;">
                                                <i class="fas fa-image fa-2x"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="col-md-4">
                                        <h6 class="mb-1">{{ $item->product->name }}</h6>
                                        @if($item->product->description)
                                            <small class="text-muted">{{ Str::limit($item->product->description, 50) }}</small>
                                        @endif
                                        <br>
                                        <span class="badge bg-primary">{{ $item->product->productCategory->name ?? '未分类' }}</span>
                                    </div>
                                    <div class="col-md-2">
                                        <span class="text-success fw-bold">¥{{ number_format($item->product->price, 2) }}</span>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="input-group input-group-sm">
                                            <button class="btn btn-outline-secondary" type="button" 
                                                    onclick="updateQuantity({{ $item->id }}, -1)">-</button>
                                            <input type="number" class="form-control text-center" 
                                                   value="{{ $item->quantity }}" min="1" max="{{ $item->product->stock }}"
                                                   onchange="updateQuantity({{ $item->id }}, this.value - {{ $item->quantity }})">
                                            <button class="btn btn-outline-secondary" type="button" 
                                                    onclick="updateQuantity({{ $item->id }}, 1)">+</button>
                                        </div>
                                        <small class="text-muted">库存: {{ $item->product->stock }}</small>
                                    </div>
                                    <div class="col-md-1">
                                        <span class="text-success fw-bold">¥{{ number_format($item->product->price * $item->quantity, 2) }}</span>
                                    </div>
                                    <div class="col-md-1">
                                        <form method="POST" action="{{ route('cart.remove', $item) }}" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger btn-sm" 
                                                    onclick="return confirm('确定要删除这个商品吗？')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                                @if(!$loop->last)
                                    <hr>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- 购物车摘要 -->
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h5><i class="fas fa-receipt"></i> 购物车摘要</h5>
                        </div>
                        <div class="card-body">
                            <div class="row mb-2">
                                <div class="col-6">商品数量:</div>
                                <div class="col-6 text-end">{{ $cartItems->count() }} 件</div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-6">商品总价:</div>
                                <div class="col-6 text-end">¥{{ number_format($total, 2) }}</div>
                            </div>
                            <hr>
                            <div class="row mb-3">
                                <div class="col-6"><strong>订单总额:</strong></div>
                                <div class="col-6 text-end">
                                    <strong class="text-success fs-5">¥{{ number_format($total, 2) }}</strong>
                                </div>
                            </div>

                            <!-- 结算按钮 -->
                            <a href="{{ route('checkout') }}" class="btn btn-success w-100 mb-2">
                                <i class="fas fa-credit-card"></i> 去结账
                            </a>

                            <!-- 继续购物 -->
                            <a href="{{ route('home') }}" class="btn btn-outline-primary w-100">
                                <i class="fas fa-shopping-bag"></i> 继续购物
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <!-- 空购物车 -->
            <div class="text-center py-5">
                <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">购物车是空的</h5>
                <p class="text-muted">您还没有添加任何商品到购物车</p>
                <a href="{{ route('home') }}" class="btn btn-primary">
                    <i class="fas fa-shopping-bag"></i> 去购物
                </a>
            </div>
        @endif
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function updateQuantity(cartItemId, change) {
            const input = document.querySelector(`input[onchange*="${cartItemId}"]`);
            const currentValue = parseInt(input.value);
            const newQuantity = currentValue + change;
            
            if (newQuantity < 1) return;
            
            // 创建表单数据
            const formData = new FormData();
            formData.append('quantity', newQuantity);
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('_method', 'PUT');
            
            fetch(`/cart/${cartItemId}`, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('更新失败：' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('更新失败，请重试');
            });
        }
    </script>
</body>
</html> 