<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>管理员仪表板 - Laravel 超市系统</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .quick-action-btn {
            transition: all 0.3s ease;
            border-radius: 10px;
            min-height: 120px;
        }
        .quick-action-btn:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        .quick-action-btn i {
            transition: all 0.3s ease;
        }
        .quick-action-btn:hover i {
            transform: scale(1.1);
        }
        .card-header.bg-primary {
            background: linear-gradient(135deg, #007bff, #0056b3) !important;
        }
        .stats-card {
            transition: all 0.3s ease;
        }
        .stats-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
    </style>
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
                        <a class="nav-link active" href="{{ route('admin.dashboard') }}">
                            <i class="fas fa-tachometer-alt"></i> 仪表板
                        </a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user-shield"></i> {{ Auth::user()->name }} (管理员)
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
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <!-- 页面标题 -->
        <div class="row mb-4">
            <div class="col">
                <h2><i class="fas fa-tachometer-alt"></i> 管理员仪表板</h2>
                <p class="text-muted">欢迎回来，{{ Auth::user()->name }}！</p>
            </div>
        </div>

        <!-- 统计卡片 -->
        <div class="row mb-4">
            <div class="col-md-3 mb-3">
                <div class="card bg-primary text-white stats-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4 class="card-title">{{ $stats['totalProducts'] }}</h4>
                                <p class="card-text">总商品数</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-box fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card bg-success text-white stats-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4 class="card-title">{{ $stats['totalOrders'] }}</h4>
                                <p class="card-text">总订单数</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-shopping-cart fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card bg-warning text-white stats-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4 class="card-title">¥{{ number_format($stats['totalSales'], 2) }}</h4>
                                <p class="card-text">总销售额</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-yen-sign fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card bg-info text-white stats-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4 class="card-title">{{ $stats['lowStockProducts'] }}</h4>
                                <p class="card-text">库存不足商品</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-exclamation-triangle fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 快速操作 -->
        <div class="row mb-4">
            <div class="col">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-bolt"></i> 快速操作</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <!-- 商品管理 -->
                            <div class="col-md-2 col-sm-4 col-6 mb-3">
                                <a href="{{ route('admin.products.create') }}" class="btn btn-primary w-100 h-100 d-flex flex-column align-items-center justify-content-center quick-action-btn">
                                    <i class="fas fa-plus-circle fa-2x mb-2"></i>
                                    <span>添加商品</span>
                                </a>
                            </div>
                            <div class="col-md-2 col-sm-4 col-6 mb-3">
                                <a href="{{ route('admin.products.index') }}" class="btn btn-success w-100 h-100 d-flex flex-column align-items-center justify-content-center quick-action-btn">
                                    <i class="fas fa-box fa-2x mb-2"></i>
                                    <span>商品管理</span>
                                </a>
                            </div>
                            
                            <!-- 用户管理 -->
                            <div class="col-md-2 col-sm-4 col-6 mb-3">
                                <a href="{{ route('admin.users.index') }}" class="btn btn-info w-100 h-100 d-flex flex-column align-items-center justify-content-center quick-action-btn">
                                    <i class="fas fa-users fa-2x mb-2"></i>
                                    <span>用户管理</span>
                                </a>
                            </div>
                            
                            <!-- 订单管理 -->
                            <div class="col-md-2 col-sm-4 col-6 mb-3">
                                <a href="{{ route('orders.index') }}" class="btn btn-warning w-100 h-100 d-flex flex-column align-items-center justify-content-center quick-action-btn">
                                    <i class="fas fa-shopping-bag fa-2x mb-2"></i>
                                    <span>我的订单</span>
                                </a>
                            </div>
                            
                            <!-- 分类管理 -->
                            <div class="col-md-2 col-sm-4 col-6 mb-3">
                                <a href="{{ route('admin.categories.index') }}" class="btn btn-dark w-100 h-100 d-flex flex-column align-items-center justify-content-center quick-action-btn">
                                    <i class="fas fa-tags fa-2x mb-2"></i>
                                    <span>分类管理</span>
                                </a>
                            </div>
                            
                            <!-- 客户聊天 -->
                            <div class="col-md-2 col-sm-4 col-6 mb-3">
                                <a href="{{ route('chat.index') }}" class="btn btn-danger w-100 h-100 d-flex flex-column align-items-center justify-content-center quick-action-btn">
                                    <i class="fas fa-comments fa-2x mb-2"></i>
                                    <span>客户聊天</span>
                                </a>
                            </div>
                            
                            <!-- 联系消息 -->
                            <div class="col-md-2 col-sm-4 col-6 mb-3">
                                <a href="{{ route('admin.contact-messages') }}" class="btn btn-secondary w-100 h-100 d-flex flex-column align-items-center justify-content-center quick-action-btn">
                                    <i class="fas fa-envelope fa-2x mb-2"></i>
                                    <span>联系消息</span>
                                </a>
                            </div>
                            
                            <!-- 查看商店 -->
                            <div class="col-md-2 col-sm-4 col-6 mb-3">
                                <a href="{{ route('home') }}" class="btn btn-outline-primary w-100 h-100 d-flex flex-column align-items-center justify-content-center quick-action-btn">
                                    <i class="fas fa-store fa-2x mb-2"></i>
                                    <span>查看商店</span>
                                </a>
                            </div>
                            
                            <!-- 系统设置 -->
                            <div class="col-md-2 col-sm-4 col-6 mb-3">
                                <a href="#" class="btn btn-outline-secondary w-100 h-100 d-flex flex-column align-items-center justify-content-center quick-action-btn">
                                    <i class="fas fa-cog fa-2x mb-2"></i>
                                    <span>系统设置</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 最新商品 -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-box"></i> 最新商品</h5>
                    </div>
                    <div class="card-body">
                        @if($products->count() > 0)
                            <div class="list-group list-group-flush">
                                @foreach($products->take(5) as $product)
                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-1">{{ $product->name }}</h6>
                                            <small class="text-muted">¥{{ number_format($product->price, 2) }} | 库存: {{ $product->stock }}</small>
                                        </div>
                                        <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-muted">暂无商品</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- 最新订单 -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-shopping-cart"></i> 最新订单</h5>
                    </div>
                    <div class="card-body">
                        @if($orders->count() > 0)
                            <div class="list-group list-group-flush">
                                @foreach($orders->take(5) as $order)
                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-1">订单 #{{ $order->id }}</h6>
                                            <small class="text-muted">¥{{ number_format($order->total_amount, 2) }} | {{ $order->status }}</small>
                                        </div>
                                        <a href="{{ route('orders.show', $order) }}" class="btn btn-sm btn-outline-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-muted">暂无订单</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 