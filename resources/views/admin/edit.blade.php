<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>编辑商品 - Laravel 超市系统</title>
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
                        <a class="nav-link" href="{{ route('admin.products.index') }}">
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
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-header bg-warning text-white">
                        <h4><i class="fas fa-edit"></i> 编辑商品</h4>
                    </div>
                    <div class="card-body p-4">
                        @if($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('admin.products.update', $product) }}" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="name" class="form-label">
                                            <i class="fas fa-tag"></i> 商品名称 <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="name" name="name" 
                                               value="{{ old('name', $product->name) }}" required>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="category" class="form-label">
                                            <i class="fas fa-list"></i> 商品分类 <span class="text-danger">*</span>
                                        </label>
                                        <select class="form-select" id="category_id" name="category_id" required>
                                            <option value="">请选择分类</option>
                                            @foreach($categories as $category)
                                                <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                                    @if($category->icon)
                                                        <i class="{{ $category->icon }}"></i>
                                                    @endif
                                                    {{ $category->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="price" class="form-label">
                                            <i class="fas fa-yen-sign"></i> 价格 <span class="text-danger">*</span>
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text">¥</span>
                                            <input type="number" class="form-control" id="price" name="price" 
                                                   value="{{ old('price', $product->price) }}" step="0.01" min="0" required>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="stock" class="form-label">
                                            <i class="fas fa-boxes"></i> 库存数量 <span class="text-danger">*</span>
                                        </label>
                                        <input type="number" class="form-control" id="stock" name="stock" 
                                               value="{{ old('stock', $product->stock) }}" min="0" required>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">
                                    <i class="fas fa-align-left"></i> 商品描述
                                </label>
                                <textarea class="form-control" id="description" name="description" rows="4" 
                                          placeholder="请输入商品详细描述...">{{ old('description', $product->description) }}</textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">
                                    <i class="fas fa-image"></i> 商品图片
                                </label>
                                
                                <!-- 当前图片显示 -->
                                @if($product->image_url)
                                    <div class="mb-3">
                                        <label class="form-label">当前图片：</label>
                                        <div class="border rounded p-3 text-center">
                                            <img src="{{ $product->image_url }}" alt="{{ $product->name }}" 
                                                 class="img-thumbnail" style="max-width: 200px; max-height: 200px;">
                                        </div>
                                    </div>
                                @endif
                                
                                <!-- 图片上传方式选择 -->
                                <div class="mb-3">
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="image_type" id="image_type_url" value="url" checked>
                                        <label class="form-check-label" for="image_type_url">
                                            <i class="fas fa-link"></i> 网络图片URL
                                        </label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="image_type" id="image_type_file" value="file">
                                        <label class="form-check-label" for="image_type_file">
                                            <i class="fas fa-upload"></i> 上传本地文件
                                        </label>
                                    </div>
                                </div>

                                <!-- 网络图片URL输入 -->
                                <div id="url_input" class="mb-3">
                                    <input type="url" class="form-control" id="image_url" name="image_url" 
                                           value="{{ old('image_url', $product->image_url) }}" placeholder="https://example.com/image.jpg">
                                    <div class="form-text">请输入商品图片的网络地址</div>
                                </div>

                                <!-- 本地文件上传 -->
                                <div id="file_input" class="mb-3" style="display: none;">
                                    <input type="file" class="form-control" id="image_file" name="image_file" 
                                           accept="image/*">
                                    <div class="form-text">支持 JPG, PNG, GIF 格式，最大 500MB</div>
                                </div>

                                <!-- 新图片预览 -->
                                <div id="image_preview" class="mt-3" style="display: none;">
                                    <label class="form-label">新图片预览：</label>
                                    <div class="border rounded p-3 text-center">
                                        <img id="preview_img" src="" alt="预览图片" class="img-thumbnail" style="max-width: 200px; max-height: 200px;">
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> 返回列表
                                </a>
                                <button type="submit" class="btn btn-warning">
                                    <i class="fas fa-save"></i> 更新商品
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // 图片上传方式切换
        document.addEventListener('DOMContentLoaded', function() {
            const urlRadio = document.getElementById('image_type_url');
            const fileRadio = document.getElementById('image_type_file');
            const urlInput = document.getElementById('url_input');
            const fileInput = document.getElementById('file_input');
            const imageUrl = document.getElementById('image_url');
            const imageFile = document.getElementById('image_file');
            const imagePreview = document.getElementById('image_preview');
            const previewImg = document.getElementById('preview_img');

            // 切换上传方式
            function toggleInput() {
                if (urlRadio.checked) {
                    urlInput.style.display = 'block';
                    fileInput.style.display = 'none';
                    imageFile.value = ''; // 清空文件输入
                } else {
                    urlInput.style.display = 'none';
                    fileInput.style.display = 'block';
                    imageUrl.value = ''; // 清空URL输入
                }
                hidePreview();
            }

            // 隐藏预览
            function hidePreview() {
                imagePreview.style.display = 'none';
                previewImg.src = '';
            }

            // 显示预览
            function showPreview(src) {
                previewImg.src = src;
                imagePreview.style.display = 'block';
            }

            // 监听单选按钮变化
            urlRadio.addEventListener('change', toggleInput);
            fileRadio.addEventListener('change', toggleInput);

            // 监听URL输入变化
            imageUrl.addEventListener('input', function() {
                if (this.value.trim()) {
                    showPreview(this.value);
                } else {
                    hidePreview();
                }
            });

            // 监听文件选择
            imageFile.addEventListener('change', function() {
                if (this.files && this.files[0]) {
                    const file = this.files[0];
                    const reader = new FileReader();
                    
                    reader.onload = function(e) {
                        showPreview(e.target.result);
                    };
                    
                    reader.readAsDataURL(file);
                } else {
                    hidePreview();
                }
            });

            // 初始化
            toggleInput();
        });
    </script>
</body>
</html> 