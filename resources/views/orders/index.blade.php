<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>我的订单 - Laravel 超市系统</title>
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
                                <a class="nav-link active" href="{{ route('orders.index') }}">
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
                <h2>
                    @if(Auth::user()->isAdmin())
                        <i class="fas fa-shopping-bag"></i> 所有订单
                    @else
                        <i class="fas fa-shopping-bag"></i> 我的订单
                    @endif
                </h2>
                <p class="text-muted">
                    @if(Auth::user()->isAdmin())
                        管理系统中的所有订单
                    @else
                        查看您的订单历史
                    @endif
                </p>
            </div>
        </div>

        <!-- 订单列表 -->
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-list"></i> 订单列表</h5>
            </div>
            <div class="card-body">
                @if($orders->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>订单号</th>
                                    <th>用户</th>
                                    <th>收货人</th>
                                    <th>商品数量</th>
                                    <th>总金额</th>
                                    <th>状态</th>
                                    <th>创建时间</th>
                                    <th>操作</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($orders as $order)
                                    <tr>
                                        <td>
                                            <strong>#{{ $order->id }}</strong>
                                        </td>
                                        <td>
                                            @if(Auth::user()->isAdmin())
                                                {{ $order->user->name }}
                                            @else
                                                {{ Auth::user()->name }}
                                            @endif
                                        </td>
                                        <td>
                                            @if($order->receiver_name)
                                                <div>
                                                    <strong>{{ $order->receiver_name }}</strong>
                                                    <br><small class="text-muted">{{ $order->city }}</small>
                                                </div>
                                            @else
                                                <span class="text-muted">未填写</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-info">{{ $order->orderItems->count() }} 件</span>
                                        </td>
                                        <td>
                                            <span class="text-success fw-bold">¥{{ number_format($order->total_amount, 2) }}</span>
                                        </td>
                                        <td>
                                            <span class="badge {{ $order->status_badge }}">{{ $order->status_text }}</span>
                                            @if($order->cancellation_requested_at && !$order->admin_response)
                                                <br><small class="text-warning"><i class="fas fa-clock"></i> 等待回复</small>
                                            @endif
                                        </td>
                                        <td>
                                            <small class="text-muted">{{ $order->created_at->format('Y-m-d H:i') }}</small>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('orders.show', $order) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-eye"></i> 查看
                                                </a>
                                                @if(Auth::user()->isAdmin())
                                                    <button type="button" class="btn btn-sm btn-outline-warning" 
                                                            data-bs-toggle="modal" data-bs-target="#statusModal{{ $order->id }}">
                                                        <i class="fas fa-edit"></i> 状态
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-shopping-bag fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">暂无订单</h5>
                        <p class="text-muted">
                            @if(Auth::user()->isAdmin())
                                系统中还没有订单
                            @else
                                您还没有下过订单，去 <a href="{{ route('home') }}">首页</a> 选购商品吧！
                            @endif
                        </p>
                    </div>
                @endif
            </div>
        </div>

        <!-- 统计信息 -->
        @if($orders->count() > 0)
            <div class="row mt-4">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body text-center">
                            <h4>{{ $orders->count() }}</h4>
                            <p class="mb-0">总订单数</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body text-center">
                            <h4>{{ $orders->where('status', 'completed')->count() }}</h4>
                            <p class="mb-0">已完成</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body text-center">
                            <h4>{{ $orders->where('status', 'pending')->count() }}</h4>
                            <p class="mb-0">待处理</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white">
                        <div class="card-body text-center">
                            <h4>¥{{ number_format($orders->where('status', 'completed')->sum('total_amount'), 2) }}</h4>
                            <p class="mb-0">总销售额</p>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- 状态更新模态框 -->
    @if(Auth::user()->isAdmin())
        @foreach($orders as $order)
            <div class="modal fade" id="statusModal{{ $order->id }}" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">更新订单状态</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <form method="POST" action="{{ route('orders.update-status', $order) }}">
                            @csrf
                            @method('PUT')
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label for="status" class="form-label">订单状态</label>
                                    <select class="form-select" id="status" name="status" required>
                                        <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>待处理</option>
                                        <option value="processing" {{ $order->status == 'processing' ? 'selected' : '' }}>处理中</option>
                                        <option value="completed" {{ $order->status == 'completed' ? 'selected' : '' }}>已完成</option>
                                        <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>已取消</option>
                                    </select>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">取消</button>
                                <button type="submit" class="btn btn-primary">更新状态</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    @endif

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 