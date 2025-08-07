<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>结账 - Laravel 超市系统</title>
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
                        <a class="nav-link" href="{{ route('products.index') }}">
                            <i class="fas fa-box"></i> 商品列表
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('cart.index') }}">
                            <i class="fas fa-shopping-cart"></i> 购物车
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('chat.index') }}">
                            <i class="fas fa-comments"></i> 在线客服
                        </a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user"></i> {{ Auth::user()->name }}
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('orders.index') }}">
                                <i class="fas fa-list"></i> 我的订单
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
        <div class="row">
            <div class="col-lg-8">
                <!-- 收货地址表单 -->
                <div class="card shadow mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5><i class="fas fa-map-marker-alt"></i> 收货地址</h5>
                    </div>
                    <div class="card-body">
                        @if($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('orders.store') }}">
                            @csrf
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="receiver_name" class="form-label">
                                            <i class="fas fa-user"></i> 收货人姓名 <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="receiver_name" name="receiver_name" 
                                               value="{{ old('receiver_name') }}" required>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="receiver_phone" class="form-label">
                                            <i class="fas fa-phone"></i> 联系电话 <span class="text-danger">*</span>
                                        </label>
                                        <input type="tel" class="form-control" id="receiver_phone" name="receiver_phone" 
                                               value="{{ old('receiver_phone') }}" required>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="shipping_address" class="form-label">
                                    <i class="fas fa-map-marker-alt"></i> 详细地址 <span class="text-danger">*</span>
                                </label>
                                <textarea class="form-control" id="shipping_address" name="shipping_address" 
                                          rows="3" placeholder="请输入详细地址..." required>{{ old('shipping_address') }}</textarea>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="city" class="form-label">
                                            <i class="fas fa-city"></i> 城市 <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="city" name="city" 
                                               value="{{ old('city') }}" required>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="postal_code" class="form-label">
                                            <i class="fas fa-mail-bulk"></i> 邮政编码 <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="postal_code" name="postal_code" 
                                               value="{{ old('postal_code') }}" required>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="notes" class="form-label">
                                    <i class="fas fa-sticky-note"></i> 备注信息
                                </label>
                                <textarea class="form-control" id="notes" name="notes" 
                                          rows="2" placeholder="如有特殊要求请在此说明...">{{ old('notes') }}</textarea>
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="{{ route('cart.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> 返回购物车
                                </a>
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-check"></i> 确认下单
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <!-- 订单摘要 -->
                <div class="card shadow">
                    <div class="card-header bg-info text-white">
                        <h5><i class="fas fa-shopping-cart"></i> 订单摘要</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <h6>商品清单</h6>
                            @foreach($cartItems as $item)
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div class="flex-grow-1">
                                        <div class="fw-bold">{{ $item->product->name }}</div>
                                        <small class="text-muted">
                                            <i class="fas fa-tag"></i> {{ $item->product->productCategory->name ?? '未分类' }}
                                        </small>
                                    </div>
                                    <div class="text-end">
                                        <div class="fw-bold">¥{{ number_format($item->product->price, 2) }}</div>
                                        <small class="text-muted">x{{ $item->quantity }}</small>
                                    </div>
                                </div>
                                <hr class="my-2">
                            @endforeach
                        </div>

                        <div class="border-top pt-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">总计</h5>
                                <h5 class="mb-0 text-success">¥{{ number_format($total, 2) }}</h5>
                            </div>
                        </div>

                        <div class="mt-3">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i>
                                <strong>温馨提示：</strong>
                                <ul class="mb-0 mt-2">
                                    <li>请确保收货地址信息准确无误</li>
                                    <li>订单提交后将无法修改</li>
                                    <li>我们会在24小时内处理您的订单</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 