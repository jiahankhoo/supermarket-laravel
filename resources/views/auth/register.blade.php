<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>注册 - Laravel 超市系统</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center mt-5">
            <div class="col-md-6 col-lg-5">
                <div class="card shadow">
                    <div class="card-header bg-success text-white text-center">
                        <h4><i class="fas fa-user-plus"></i> 用户注册</h4>
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

                        <form method="POST" action="{{ route('register.post') }}">
                            @csrf
                            <div class="mb-3">
                                <label for="name" class="form-label">
                                    <i class="fas fa-user"></i> 用户名
                                </label>
                                <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required>
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">
                                    <i class="fas fa-envelope"></i> 邮箱地址
                                </label>
                                <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required>
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">
                                    <i class="fas fa-lock"></i> 密码
                                </label>
                                <input type="password" class="form-control" id="password" name="password" required>
                                <div class="form-text">密码至少8位字符</div>
                            </div>

                            <div class="mb-3">
                                <label for="password_confirmation" class="form-label">
                                    <i class="fas fa-lock"></i> 确认密码
                                </label>
                                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                            </div>

                            <div class="mb-3">
                                <label for="role" class="form-label">
                                    <i class="fas fa-users"></i> 角色选择
                                </label>
                                <select class="form-select" id="role" name="role" required>
                                    <option value="">请选择角色</option>
                                    <option value="user" {{ old('role') == 'user' ? 'selected' : '' }}>普通用户 - 购买商品</option>
                                </select>
                                <div class="form-text">
                                    普通用户可以浏览和购买商品，管理员可以管理商品和订单
                                </div>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-user-plus"></i> 注册
                                </button>
                            </div>
                        </form>

                        <hr class="my-4">

                        <div class="text-center">
                            <p class="mb-0">已有账户？</p>
                            <a href="{{ route('login') }}" class="btn btn-outline-primary">
                                <i class="fas fa-sign-in-alt"></i> 立即登录
                            </a>
                        </div>

                        <div class="text-center mt-3">
                            <a href="{{ route('home') }}" class="text-muted">
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