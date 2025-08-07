@extends('layouts.app')

@section('title', '在线客服')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">
                        <i class="fas fa-comments text-primary"></i>
                        在线客服
                    </h4>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <h6><i class="fas fa-info-circle"></i> 客服说明</h6>
                        <p class="mb-0">
                            如果您在使用过程中遇到任何问题，可以点击下方的客服进行在线咨询。
                            我们的客服会尽快为您解答问题。
                        </p>
                    </div>

                    @if($admins->count() > 0)
                        <div class="row">
                            @foreach($admins as $admin)
                                <div class="col-md-6 mb-3">
                                    <div class="card h-100 border-primary">
                                        <div class="card-body text-center">
                                            <div class="mb-3">
                                                <i class="fas fa-user-tie fa-3x text-primary"></i>
                                            </div>
                                            <h5 class="card-title">{{ $admin->name }}</h5>
                                            <p class="card-text text-muted">在线客服</p>
                                            <a href="{{ route('chat.show', $admin->id) }}" class="btn btn-primary">
                                                <i class="fas fa-comment"></i> 开始聊天
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-user-slash fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">暂无在线客服</h5>
                            <p class="text-muted">请稍后再试或通过其他方式联系我们。</p>
                        </div>
                    @endif

                    <div class="mt-4">
                        <h6><i class="fas fa-phone text-success"></i> 其他联系方式</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>客服电话：</strong> 400-123-4567</p>
                                <small class="text-muted">工作日 9:00-18:00</small>
                            </div>
                            <div class="col-md-6">
                                <p><strong>客服邮箱：</strong> service@supermarket.com</p>
                                <small class="text-muted">24小时内回复</small>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-center mt-4">
                        <a href="{{ route('home') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> 返回首页
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 