@extends('layouts.app')

@section('title', '客户聊天管理')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">
                        <i class="fas fa-comments text-primary"></i>
                        客户聊天管理
                    </h4>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <h6><i class="fas fa-info-circle"></i> 功能说明</h6>
                        <p class="mb-0">
                            您可以在这里查看所有客户，点击客户名称开始聊天。
                            系统会显示每个客户的未读消息数量。
                        </p>
                    </div>

                    @if($customers->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>客户信息</th>
                                        <th>注册时间</th>
                                        <th>订单数量</th>
                                        <th>未读消息</th>
                                        <th>操作</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($customers as $customer)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar me-3">
                                                        <i class="fas fa-user-circle fa-2x text-primary"></i>
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-0">{{ $customer->name }}</h6>
                                                        <small class="text-muted">{{ $customer->email }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <small class="text-muted">
                                                    {{ $customer->created_at->format('Y-m-d H:i') }}
                                                </small>
                                            </td>
                                            <td>
                                                <span class="badge bg-info">
                                                    {{ $customer->orders()->count() }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-danger unread-count" data-user-id="{{ $customer->id }}">
                                                    0
                                                </span>
                                            </td>
                                            <td>
                                                <a href="{{ route('chat.show', $customer->id) }}" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-comment"></i> 开始聊天
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-users-slash fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">暂无客户</h5>
                            <p class="text-muted">当有客户注册后，他们将显示在这里。</p>
                        </div>
                    @endif

                    <div class="d-flex justify-content-between mt-4">
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> 返回仪表板
                        </a>
                        <button class="btn btn-outline-primary" onclick="refreshUnreadCounts()">
                            <i class="fas fa-sync-alt"></i> 刷新未读消息
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function refreshUnreadCounts() {
    const unreadElements = document.querySelectorAll('.unread-count');
    
    unreadElements.forEach(element => {
        const userId = element.dataset.userId;
        fetch(`/chat/unread-count/${userId}`)
            .then(response => response.json())
            .then(data => {
                element.textContent = data.count;
                if (data.count > 0) {
                    element.classList.add('bg-danger');
                    element.classList.remove('bg-secondary');
                } else {
                    element.classList.remove('bg-danger');
                    element.classList.add('bg-secondary');
                }
            });
    });
}

// 页面加载时刷新未读消息数量
document.addEventListener('DOMContentLoaded', function() {
    refreshUnreadCounts();
    
    // 每30秒自动刷新一次
    setInterval(refreshUnreadCounts, 30000);
});
</script>
@endpush
@endsection 