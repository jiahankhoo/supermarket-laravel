@extends('layouts.app')

@section('title', '联系消息管理')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">
                        <i class="fas fa-envelope-open text-primary"></i>
                        联系消息管理
                    </h4>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <h6><i class="fas fa-info-circle"></i> 功能说明</h6>
                        <p class="mb-0">
                            这里将显示客户发送的所有联系消息。目前这是一个占位页面，
                            您可以在这里查看和管理客户的联系请求。
                        </p>
                    </div>

                    <div class="text-center py-5">
                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">暂无联系消息</h5>
                        <p class="text-muted">当客户发送联系消息时，它们将显示在这里。</p>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> 返回仪表板
                        </a>
                        <a href="{{ route('contact.form') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> 测试联系表单
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 