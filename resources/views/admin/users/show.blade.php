@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-info text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-0">
                                <i class="fas fa-user me-2"></i>用户详情
                            </h4>
                            <small>查看用户 {{ $user->name }} 的详细信息</small>
                        </div>
                        <div>
                            <a href="{{ route('admin.users.index') }}" class="btn btn-light btn-sm">
                                <i class="fas fa-arrow-left me-1"></i>返回用户列表
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="text-center mb-4">
                                <div class="avatar-lg mx-auto mb-3">
                                    <span class="avatar-initial rounded-circle bg-primary">
                                        {{ strtoupper(substr($user->name, 0, 2)) }}
                                    </span>
                                </div>
                                <h5>{{ $user->name }}</h5>
                                @if($user->role === 'admin')
                                    <span class="badge bg-danger">
                                        <i class="fas fa-crown me-1"></i>管理员
                                    </span>
                                @else
                                    <span class="badge bg-success">
                                        <i class="fas fa-user me-1"></i>普通用户
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label text-muted">用户ID</label>
                                        <div class="form-control-plaintext">{{ $user->id }}</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label text-muted">注册时间</label>
                                        <div class="form-control-plaintext">
                                            {{ $user->created_at->format('Y-m-d H:i:s') }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label text-muted">姓名</label>
                                <div class="form-control-plaintext">{{ $user->name }}</div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label text-muted">邮箱地址</label>
                                <div class="form-control-plaintext">{{ $user->email }}</div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label text-muted">角色</label>
                                <div class="form-control-plaintext">
                                    @if($user->role === 'admin')
                                        <span class="badge bg-danger">管理员</span>
                                    @else
                                        <span class="badge bg-success">普通用户</span>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label text-muted">最后登录时间</label>
                                <div class="form-control-plaintext">
                                    {{ $user->updated_at->format('Y-m-d H:i:s') }}
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label text-muted">密码</label>
                                <div class="input-group">
                                    <input type="password" 
                                           class="form-control" 
                                           value="••••••••" 
                                           id="passwordField" 
                                           readonly>
                                    <button class="btn btn-outline-secondary" 
                                            type="button" 
                                            onclick="togglePassword()">
                                        <i class="fas fa-eye" id="passwordIcon"></i>
                                    </button>
                                </div>
                                <small class="text-muted">
                                    <i class="fas fa-info-circle me-1"></i>
                                    密码已加密存储，无法直接查看明文
                                </small>
                            </div>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <h6 class="card-title text-muted">购物车项目</h6>
                                    <h3 class="text-primary">{{ $user->cartItems->count() }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <h6 class="card-title text-muted">订单数量</h6>
                                    <h3 class="text-success">{{ $user->orders->count() }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <h6 class="card-title text-muted">总消费</h6>
                                    <h3 class="text-warning">¥{{ number_format($user->orders->sum('total_amount'), 2) }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.users.edit', $user) }}" 
                               class="btn btn-warning">
                                <i class="fas fa-edit me-1"></i>编辑用户
                            </a>
                            <button type="button" 
                                    class="btn btn-danger" 
                                    onclick="confirmDelete({{ $user->id }}, '{{ $user->name }}')">
                                <i class="fas fa-trash me-1"></i>删除用户
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 删除确认模态框 -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-triangle text-danger me-2"></i>确认删除
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>您确定要删除用户 <strong id="userName"></strong> 吗？</p>
                <p class="text-danger">
                    <i class="fas fa-warning me-1"></i>
                    此操作不可撤销，将同时删除该用户的所有购物车项目和订单！
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">取消</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash me-1"></i>确认删除
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
.avatar-lg {
    width: 100px;
    height: 100px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.avatar-initial {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: bold;
    font-size: 36px;
}
</style>

<script>
function togglePassword() {
    const passwordField = document.getElementById('passwordField');
    const passwordIcon = document.getElementById('passwordIcon');
    
    if (passwordField.type === 'password') {
        passwordField.type = 'text';
        passwordField.value = '••••••••';
        passwordIcon.className = 'fas fa-eye-slash';
    } else {
        passwordField.type = 'password';
        passwordField.value = '••••••••';
        passwordIcon.className = 'fas fa-eye';
    }
}

function confirmDelete(userId, userName) {
    document.getElementById('userName').textContent = userName;
    document.getElementById('deleteForm').action = `/admin/users/${userId}`;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}
</script>
@endsection 