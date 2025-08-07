@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-0">
                                <i class="fas fa-users me-2"></i>用户管理
                            </h4>
                            <small>管理普通用户账号（系统只有一个管理员）</small>
                        </div>
                        <div>
                            <a href="{{ route('admin.dashboard') }}" class="btn btn-light btn-sm">
                                <i class="fas fa-arrow-left me-1"></i>返回仪表板
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <!-- 搜索和筛选栏 -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <form method="GET" action="{{ route('admin.users.index') }}" class="row g-3">
                                <div class="col-md-4">
                                    <label for="search" class="form-label">搜索用户</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                                        <input type="text" class="form-control" id="search" name="search" 
                                               placeholder="搜索用户姓名或邮箱..." 
                                               value="{{ request('search') }}">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <label for="role" class="form-label">角色筛选</label>
                                    <select class="form-select" id="role" name="role" disabled>
                                        <option value="user" selected>普通用户</option>
                                    </select>
                                    <small class="text-muted">系统中只有一个管理员</small>
                                </div>
                                <div class="col-md-3">
                                    <label for="date_filter" class="form-label">注册时间</label>
                                    <select class="form-select" id="date_filter" name="date_filter">
                                        <option value="">所有时间</option>
                                        <option value="today" {{ request('date_filter') == 'today' ? 'selected' : '' }}>今天</option>
                                        <option value="week" {{ request('date_filter') == 'week' ? 'selected' : '' }}>本周</option>
                                        <option value="month" {{ request('date_filter') == 'month' ? 'selected' : '' }}>本月</option>
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
                            
                            @if(request('search') || request('role') || request('date_filter'))
                                <div class="mt-3">
                                    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary btn-sm">
                                        <i class="fas fa-times"></i> 清除筛选
                                    </a>
                                    <small class="text-muted ms-2">
                                        找到 {{ $users->count() }} 个用户
                                    </small>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>头像</th>
                                    <th>姓名</th>
                                    <th>邮箱</th>
                                    <th>角色</th>
                                    <th>注册时间</th>
                                    <th>操作</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($users as $user)
                                    <tr>
                                        <td>{{ $user->id }}</td>
                                        <td>
                                            <div class="avatar-sm">
                                                <span class="avatar-initial rounded-circle bg-primary">
                                                    {{ strtoupper(substr($user->name, 0, 2)) }}
                                                </span>
                                            </div>
                                        </td>
                                        <td>
                                            <strong>{{ $user->name }}</strong>
                                        </td>
                                        <td>
                                            <span class="text-muted">{{ $user->email }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-success">
                                                <i class="fas fa-user me-1"></i>普通用户
                                            </span>
                                        </td>
                                        <td>
                                            <small class="text-muted">
                                                {{ $user->created_at->format('Y-m-d H:i') }}
                                            </small>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('admin.users.show', $user) }}" 
                                                   class="btn btn-outline-info btn-sm" 
                                                   title="查看详情">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.users.edit', $user) }}" 
                                                   class="btn btn-outline-warning btn-sm" 
                                                   title="编辑用户">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button type="button" 
                                                        class="btn btn-outline-danger btn-sm" 
                                                        title="删除用户"
                                                        onclick="confirmDelete({{ $user->id }}, '{{ $user->name }}')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="fas fa-users fa-3x mb-3"></i>
                                                <p>暂无普通用户数据</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        <small class="text-muted">
                            <i class="fas fa-info-circle me-1"></i>
                            共 {{ $users->count() }} 个普通用户
                        </small>
                        <small class="text-muted ms-3">
                            <i class="fas fa-crown me-1"></i>
                            系统管理员：{{ Auth::user()->name }}
                        </small>
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
.avatar-sm {
    width: 40px;
    height: 40px;
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
    font-size: 14px;
}
</style>

<script>
function confirmDelete(userId, userName) {
    document.getElementById('userName').textContent = userName;
    document.getElementById('deleteForm').action = `/admin/users/${userId}`;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}
</script>
@endsection 