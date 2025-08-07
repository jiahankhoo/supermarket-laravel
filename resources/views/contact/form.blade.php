@extends('layouts.app')

@section('title', '联系商家')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">
                        <i class="fas fa-envelope text-primary"></i>
                        联系商家
                    </h4>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle"></i> {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                        </div>
                    @endif

                    <div class="alert alert-info">
                        <h6><i class="fas fa-info-circle"></i> 联系说明</h6>
                        <p class="mb-0">
                            如果您有任何问题、建议或需要帮助，请填写下面的表单联系我们。
                            我们会尽快回复您的消息。
                        </p>
                    </div>

                    <form action="{{ route('contact.submit') }}" method="POST">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="subject" class="form-label">
                                <i class="fas fa-tag"></i> 主题 <span class="text-danger">*</span>
                            </label>
                            <input 
                                type="text" 
                                class="form-control @error('subject') is-invalid @enderror" 
                                id="subject" 
                                name="subject" 
                                value="{{ old('subject') }}"
                                placeholder="请输入消息主题..."
                                required
                            >
                            @error('subject')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="message" class="form-label">
                                <i class="fas fa-comment"></i> 消息内容 <span class="text-danger">*</span>
                            </label>
                            <textarea 
                                class="form-control @error('message') is-invalid @enderror" 
                                id="message" 
                                name="message" 
                                rows="6" 
                                placeholder="请详细描述您的问题或需求..."
                                required
                            >{{ old('message') }}</textarea>
                            @error('message')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                <i class="fas fa-lightbulb text-warning"></i>
                                请提供详细信息，这将帮助我们更好地为您服务。
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="order_id" class="form-label">
                                <i class="fas fa-shopping-bag"></i> 相关订单（可选）
                            </label>
                            <select class="form-select @error('order_id') is-invalid @enderror" id="order_id" name="order_id">
                                <option value="">选择相关订单（如果有）</option>
                                @if(Auth::check())
                                    @foreach(Auth::user()->orders()->latest()->get() as $order)
                                        <option value="{{ $order->id }}" {{ old('order_id') == $order->id ? 'selected' : '' }}>
                                            订单 #{{ $order->id }} - {{ $order->created_at->format('Y-m-d H:i') }} 
                                            ({{ $order->status_text }})
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                            @error('order_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                如果您的消息与特定订单相关，请选择该订单。
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('home') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> 返回首页
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane"></i> 发送消息
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- 联系信息 -->
            <div class="card mt-4">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-address-card"></i> 其他联系方式
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6><i class="fas fa-phone text-success"></i> 客服电话</h6>
                            <p class="mb-2">400-123-4567</p>
                            <small class="text-muted">工作日 9:00-18:00</small>
                        </div>
                        <div class="col-md-6">
                            <h6><i class="fas fa-envelope text-info"></i> 客服邮箱</h6>
                            <p class="mb-2">service@supermarket.com</p>
                            <small class="text-muted">24小时内回复</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 