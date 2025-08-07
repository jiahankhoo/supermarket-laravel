<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>订单详情 #{{ $order->id }} - Laravel 超市系统</title>
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
                <li class="breadcrumb-item"><a href="{{ route('orders.index') }}">订单列表</a></li>
                <li class="breadcrumb-item active" aria-current="page">订单 #{{ $order->id }}</li>
            </ol>
        </nav>

        <div class="row">
            <!-- 订单信息 -->
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h4><i class="fas fa-shopping-bag"></i> 订单详情 #{{ $order->id }}</h4>
                    </div>
                    <div class="card-body">
                        <!-- 订单状态 -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h6><i class="fas fa-info-circle"></i> 订单状态</h6>
                                <span class="badge {{ $order->status_badge }} fs-6">{{ $order->status_text }}</span>
                            </div>
                            <div class="col-md-6">
                                <h6><i class="fas fa-calendar"></i> 创建时间</h6>
                                <p class="text-muted">{{ $order->created_at->format('Y-m-d H:i:s') }}</p>
                            </div>
                        </div>

                        <!-- 收货地址 -->
                        @if($order->receiver_name)
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6><i class="fas fa-map-marker-alt"></i> 收货地址</h6>
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <p><strong>收货人：</strong>{{ $order->receiver_name }}</p>
                                                <p><strong>联系电话：</strong>{{ $order->receiver_phone }}</p>
                                            </div>
                                            <div class="col-md-6">
                                                <p><strong>城市：</strong>{{ $order->city }}</p>
                                                <p><strong>邮政编码：</strong>{{ $order->postal_code }}</p>
                                            </div>
                                        </div>
                                        <p><strong>详细地址：</strong>{{ $order->shipping_address }}</p>
                                        @if($order->notes)
                                            <p><strong>备注：</strong>{{ $order->notes }}</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- 取消申请信息 -->
                        @if($order->cancellation_requested_at)
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6><i class="fas fa-exclamation-triangle text-warning"></i> 取消申请信息</h6>
                                <div class="card border-warning">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <p><strong>申请时间：</strong>{{ $order->cancellation_requested_at->format('Y-m-d H:i:s') }}</p>
                                                <p><strong>取消原因：</strong></p>
                                                <div class="bg-light p-2 rounded">
                                                    {{ $order->cancellation_reason }}
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                @if($order->admin_response)
                                                    <p><strong>商家回复：</strong></p>
                                                    <div class="bg-light p-2 rounded">
                                                        {{ $order->admin_response }}
                                                    </div>
                                                    <p class="mt-2">
                                                        <strong>回复时间：</strong>{{ $order->admin_responded_at->format('Y-m-d H:i:s') }}<br>
                                                        <strong>回复人：</strong>{{ $order->adminResponder->name ?? '未知' }}
                                                    </p>
                                                @else
                                                    <p class="text-muted"><em>等待商家回复...</em></p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- 商品列表 -->
                        <h6><i class="fas fa-box"></i> 商品列表</h6>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>商品图片</th>
                                        <th>商品名称</th>
                                        <th>单价</th>
                                        <th>数量</th>
                                        <th>小计</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($order->orderItems as $item)
                                        <tr>
                                            <td>
                                                @if($item->product->image_url)
                                                    <img src="{{ $item->product->image_url }}" alt="{{ $item->product->name }}" 
                                                         class="img-thumbnail" style="width: 50px; height: 50px; object-fit: cover;">
                                                @else
                                                    <div class="bg-secondary text-white d-flex align-items-center justify-content-center" 
                                                         style="width: 50px; height: 50px;">
                                                        <i class="fas fa-image"></i>
                                                    </div>
                                                @endif
                                            </td>
                                            <td>
                                                <strong>{{ $item->product->name }}</strong>
                                                @if($item->product->description)
                                                    <br><small class="text-muted">{{ Str::limit($item->product->description, 50) }}</small>
                                                @endif
                                            </td>
                                            <td>¥{{ number_format($item->price, 2) }}</td>
                                            <td>{{ $item->quantity }}</td>
                                            <td class="text-success fw-bold">¥{{ number_format($item->price * $item->quantity, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 订单摘要 -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-receipt"></i> 订单摘要</h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-2">
                            <div class="col-6">商品数量:</div>
                            <div class="col-6 text-end">{{ $order->orderItems->count() }} 件</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-6">商品总价:</div>
                            <div class="col-6 text-end">¥{{ number_format($order->total_amount, 2) }}</div>
                        </div>
                        <hr>
                        <div class="row mb-3">
                            <div class="col-6"><strong>订单总额:</strong></div>
                            <div class="col-6 text-end">
                                <strong class="text-success fs-5">¥{{ number_format($order->total_amount, 2) }}</strong>
                            </div>
                        </div>

                        <!-- 管理员操作 -->
                        @if(Auth::user()->isAdmin())
                            <div class="mt-3">
                                <h6><i class="fas fa-cog"></i> 管理员操作</h6>
                                <button type="button" class="btn btn-warning btn-sm w-100" 
                                        data-bs-toggle="modal" data-bs-target="#statusModal">
                                    <i class="fas fa-edit"></i> 更新订单状态
                                </button>
                                
                                <!-- 取消申请回复 -->
                                @if($order->status === 'cancellation_requested')
                                    <button type="button" class="btn btn-info btn-sm w-100 mt-2" 
                                            data-bs-toggle="modal" data-bs-target="#cancellationResponseModal">
                                        <i class="fas fa-reply"></i> 回复取消申请
                                    </button>
                                @endif
                            </div>
                        @endif

                        <!-- 用户操作 -->
                        @if(Auth::user()->isUser() && $order->user_id === Auth::id())
                            <div class="mt-3">
                                <h6><i class="fas fa-user-cog"></i> 用户操作</h6>
                                @if($order->canRequestCancellation())
                                    <a href="{{ route('orders.cancellation-form', $order) }}" class="btn btn-warning btn-sm w-100">
                                        <i class="fas fa-times-circle"></i> 申请取消订单
                                    </a>
                                @endif
                            </div>
                        @endif

                        <!-- 返回按钮 -->
                        <div class="mt-3">
                            <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary w-100">
                                <i class="fas fa-arrow-left"></i> 返回订单列表
                            </a>
                        </div>
                    </div>
                </div>

                <!-- 用户信息 -->
                <div class="card mt-3">
                    <div class="card-header">
                        <h6><i class="fas fa-user"></i> 用户信息</h6>
                    </div>
                    <div class="card-body">
                        <p class="mb-1"><strong>用户名:</strong> {{ $order->user->name }}</p>
                        <p class="mb-1"><strong>邮箱:</strong> {{ $order->user->email }}</p>
                        <p class="mb-0"><strong>角色:</strong> 
                            @if($order->user->isAdmin())
                                <span class="badge bg-warning">管理员</span>
                            @else
                                <span class="badge bg-info">用户</span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 状态更新模态框 -->
    @if(Auth::user()->isAdmin())
        <div class="modal fade" id="statusModal" tabindex="-1">
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
                                    <option value="cancellation_requested" {{ $order->status == 'cancellation_requested' ? 'selected' : '' }}>申请取消中</option>
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

        <!-- 取消申请回复模态框 -->
        @if($order->status === 'cancellation_requested')
        <div class="modal fade" id="cancellationResponseModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="fas fa-reply text-info"></i> 回复取消申请
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form method="POST" action="{{ route('orders.respond-cancellation', $order) }}">
                        @csrf
                        <div class="modal-body">
                            <!-- 取消申请详情 -->
                            <div class="alert alert-warning">
                                <h6><i class="fas fa-info-circle"></i> 取消申请详情</h6>
                                <p><strong>申请时间：</strong>{{ $order->cancellation_requested_at->format('Y-m-d H:i:s') }}</p>
                                <p><strong>取消原因：</strong></p>
                                <div class="bg-light p-2 rounded">
                                    {{ $order->cancellation_reason }}
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="admin_response" class="form-label">
                                    <i class="fas fa-comment"></i> 回复内容 <span class="text-danger">*</span>
                                </label>
                                <textarea 
                                    class="form-control @error('admin_response') is-invalid @enderror" 
                                    id="admin_response" 
                                    name="admin_response" 
                                    rows="4" 
                                    placeholder="请详细说明您的回复..."
                                    required
                                >{{ old('admin_response') }}</textarea>
                                @error('admin_response')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="action" class="form-label">
                                    <i class="fas fa-check-circle"></i> 处理决定 <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('action') is-invalid @enderror" id="action" name="action" required>
                                    <option value="">请选择处理决定</option>
                                    <option value="approve" {{ old('action') == 'approve' ? 'selected' : '' }}>
                                        <i class="fas fa-check"></i> 批准取消申请
                                    </option>
                                    <option value="reject" {{ old('action') == 'reject' ? 'selected' : '' }}>
                                        <i class="fas fa-times"></i> 拒绝取消申请
                                    </option>
                                </select>
                                @error('action')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="alert alert-info">
                                <h6><i class="fas fa-lightbulb"></i> 处理说明</h6>
                                <ul class="mb-0">
                                    <li><strong>批准取消：</strong>订单将被取消，商品库存将自动恢复</li>
                                    <li><strong>拒绝取消：</strong>订单将继续处理，状态变为"处理中"</li>
                                    <li>您的回复将发送给客户，请确保回复内容清晰明确</li>
                                </ul>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">取消</button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane"></i> 提交回复
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endif
    @endif

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 