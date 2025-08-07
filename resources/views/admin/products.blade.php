<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>商品管理 - Laravel 超市系统</title>
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
                        <a class="nav-link" href="{{ route('admin.dashboard') }}">
                            <i class="fas fa-tachometer-alt"></i> 仪表板
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="{{ route('admin.products.index') }}">
                            <i class="fas fa-box"></i> 商品管理
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
        <!-- 页面标题和操作按钮 -->
        <div class="row mb-4">
            <div class="col-md-6">
                <h2><i class="fas fa-box"></i> 商品管理</h2>
                <p class="text-muted">管理您的商品库存</p>
            </div>
            <div class="col-md-6 text-end">
                <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> 添加新商品
                </a>
            </div>
        </div>

        <!-- 搜索和筛选栏 -->
        <div class="card mb-4">
            <div class="card-header">
                <h5><i class="fas fa-search"></i> 搜索和筛选</h5>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('admin.products.index') }}" class="row g-3">
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
                    <div class="col-md-3">
                        <label for="stock_status" class="form-label">库存状态</label>
                        <select class="form-select" id="stock_status" name="stock_status">
                            <option value="">所有状态</option>
                            <option value="in_stock" {{ request('stock_status') == 'in_stock' ? 'selected' : '' }}>有库存</option>
                            <option value="low_stock" {{ request('stock_status') == 'low_stock' ? 'selected' : '' }}>库存不足</option>
                            <option value="out_of_stock" {{ request('stock_status') == 'out_of_stock' ? 'selected' : '' }}>缺货</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i> 搜索
                            </button>
                        </div>
                    </div>
                </form>
                
                @if(request('search') || request('category') || request('stock_status'))
                    <div class="mt-3">
                        <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-times"></i> 清除筛选
                        </a>
                        <small class="text-muted ms-2">
                            找到 {{ $products->count() }} 个商品
                        </small>
                    </div>
                @endif
            </div>
        </div>

        <!-- 商品列表 -->
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-list"></i> 商品列表</h5>
            </div>
            <div class="card-body">
                @if($products->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>商品图片</th>
                                    <th>商品名称</th>
                                    <th>分类</th>
                                    <th>价格</th>
                                    <th>库存</th>
                                    <th>状态</th>
                                    <th>创建时间</th>
                                    <th>操作</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($products as $product)
                                    <tr>
                                        <td>{{ $product->id }}</td>
                                        <td>
                                            @if($product->image_url)
                                                <img src="{{ $product->image_url }}" alt="{{ $product->name }}" 
                                                     class="img-thumbnail" style="width: 50px; height: 50px; object-fit: cover;">
                                            @else
                                                <div class="bg-secondary text-white d-flex align-items-center justify-content-center" 
                                                     style="width: 50px; height: 50px;">
                                                    <i class="fas fa-image"></i>
                                                </div>
                                            @endif
                                        </td>
                                        <td>
                                            <strong>{{ $product->name }}</strong>
                                            @if($product->description)
                                                <br><small class="text-muted">{{ Str::limit($product->description, 50) }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-info">{{ $product->productCategory->name ?? '未分类' }}</span>
                                        </td>
                                        <td>
                                            <span class="text-success fw-bold">¥{{ number_format($product->price, 2) }}</span>
                                        </td>
                                        <td>
                                            @if($product->stock > 10)
                                                <span class="badge bg-success">{{ $product->stock }}</span>
                                            @elseif($product->stock > 0)
                                                <span class="badge bg-warning">{{ $product->stock }}</span>
                                            @else
                                                <span class="badge bg-danger">缺货</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($product->stock > 0)
                                                <span class="badge bg-success">在售</span>
                                            @else
                                                <span class="badge bg-secondary">下架</span>
                                            @endif
                                        </td>
                                        <td>
                                            <small class="text-muted">{{ $product->created_at->format('Y-m-d H:i') }}</small>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('products.show', $product) }}" class="btn btn-sm btn-outline-info" title="查看">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-sm btn-outline-primary" title="编辑">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form method="POST" action="{{ route('admin.products.destroy', $product) }}" 
                                                      class="d-inline" onsubmit="return confirm('确定要删除这个商品吗？')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="删除">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- 分页功能暂未启用 -->
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">暂无商品</h5>
                        <p class="text-muted">开始添加您的第一个商品吧！</p>
                        <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> 添加商品
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- 统计信息 -->
        <div class="row mt-4">
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body text-center">
                        <h4>{{ $products->count() }}</h4>
                        <p class="mb-0">总商品数</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body text-center">
                        <h4>{{ $products->where('stock', '>', 0)->count() }}</h4>
                        <p class="mb-0">在售商品</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-white">
                    <div class="card-body text-center">
                        <h4>{{ $products->where('stock', '<=', 10)->where('stock', '>', 0)->count() }}</h4>
                        <p class="mb-0">库存不足</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-danger text-white">
                    <div class="card-body text-center">
                        <h4>{{ $products->where('stock', 0)->count() }}</h4>
                        <p class="mb-0">缺货商品</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 