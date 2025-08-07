@extends('layouts.app')

@section('title', 'Laravel 超市系统')

@section('content')
<div class="container">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row">
            <div class="col-12">
                <h1 class="mb-4">
                    <i class="fas fa-store text-primary"></i> 欢迎来到超市系统
                </h1>
            </div>
        </div>

        <!-- 搜索和筛选栏 -->
        <div class="card mb-4">
            <div class="card-header">
                <h5><i class="fas fa-search"></i> 搜索和筛选商品</h5>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('home') }}" class="row g-3">
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
                        <a href="{{ route('home') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-times"></i> 清除筛选
                        </a>
                        <small class="text-muted ms-2">
                            找到 {{ $products->count() }} 个商品
                        </small>
                    </div>
                @endif
            </div>
        </div>

        <div class="row">
            <div class="col-md-8">
                <h3><i class="fas fa-box"></i> 商品列表</h3>
                @if($products->count() > 0)
                    <div class="row">
                        @foreach($products as $product)
                            <div class="col-md-6 col-lg-4 mb-4">
                                <div class="card h-100">
                                    @if($product->image_url)
                                        <img src="{{ $product->image_url }}" class="card-img-top" alt="{{ $product->name }}" style="height: 200px; object-fit: cover;">
                                    @else
                                        <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                                            <i class="fas fa-image fa-3x text-muted"></i>
                                        </div>
                                    @endif
                                    <div class="card-body">
                                        <h5 class="card-title">{{ $product->name }}</h5>
                                        <p class="card-text text-muted">{{ Str::limit($product->description, 100) }}</p>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="badge bg-primary">{{ $product->productCategory->name ?? '未分类' }}</span>
                                            <span class="text-success fw-bold">¥{{ number_format($product->price, 2) }}</span>
                                        </div>
                                        <div class="mt-2">
                                            <small class="text-muted">库存: {{ $product->stock }}</small>
                                        </div>
                                    </div>
                                    <div class="card-footer">
                                        @auth
                                            @if(Auth::user()->role === 'user')
                                                <form action="{{ route('cart.add', $product) }}" method="POST">
                                                    @csrf
                                                    <div class="input-group mb-2">
                                                        <input type="number" name="quantity" value="1" min="1" max="{{ $product->stock }}" class="form-control">
                                                        <button type="submit" class="btn btn-primary">
                                                            <i class="fas fa-cart-plus"></i> 加入购物车
                                                        </button>
                                                    </div>
                                                </form>
                                            @endif
                                        @else
                                            <a href="{{ route('login') }}" class="btn btn-outline-primary w-100">
                                                <i class="fas fa-sign-in-alt"></i> 登录后购买
                                            </a>
                                        @endauth
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-box-open fa-4x text-muted mb-3"></i>
                        <h4 class="text-muted">
                            @if(request('search') || request('category') || request('min_price') || request('max_price'))
                                没有找到符合条件的商品
                            @else
                                暂无商品
                            @endif
                        </h4>
                        <p class="text-muted">
                            @if(request('search') || request('category') || request('min_price') || request('max_price'))
                                请尝试调整搜索条件或 <a href="{{ route('home') }}">清除筛选</a>
                            @else
                                管理员可以添加商品
                            @endif
                        </p>
                    </div>
                @endif
            </div>
            
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-info-circle"></i> 系统信息</h5>
                    </div>
                    <div class="card-body">
                        <p><strong>商品总数:</strong> {{ $products->count() }}</p>
                        <p><strong>系统状态:</strong> <span class="badge bg-success">运行中</span></p>
                        
                        @auth
                            <hr>
                            <h6>用户信息</h6>
                            <p><strong>用户名:</strong> {{ Auth::user()->name }}</p>
                            <p><strong>角色:</strong> 
                                <span class="badge bg-{{ Auth::user()->role === 'admin' ? 'danger' : 'info' }}">
                                    {{ Auth::user()->role === 'admin' ? '管理员' : '普通用户' }}
                                </span>
                            </p>
                        @else
                            <hr>
                            <p class="text-muted">请登录以使用完整功能</p>
                            <a href="{{ route('login') }}" class="btn btn-primary">
                                <i class="fas fa-sign-in-alt"></i> 登录
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
        </div>
</div>
@endsection
