@extends('layouts.app')

@section('title', '申请取消订单')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">
                        <i class="fas fa-times-circle text-warning"></i>
                        申请取消订单 #{{ $order->id }}
                    </h4>
                </div>
                <div class="card-body">
                    <!-- 订单信息 -->
                    <div class="alert alert-info">
                        <h6><i class="fas fa-info-circle"></i> 订单信息</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <strong>订单号：</strong> #{{ $order->id }}<br>
                                <strong>下单时间：</strong> {{ $order->created_at->format('Y-m-d H:i:s') }}<br>
                                <strong>订单状态：</strong> 
                                <span class="badge {{ $order->status_badge }}">{{ $order->status_text }}</span>
                            </div>
                            <div class="col-md-6">
                                <strong>收货人：</strong> {{ $order->receiver_name }}<br>
                                <strong>联系电话：</strong> {{ $order->receiver_phone }}<br>
                                <strong>订单金额：</strong> ¥{{ number_format($order->total_amount, 2) }}
                            </div>
                        </div>
                    </div>

                    <!-- 订单商品 -->
                    <div class="mb-4">
                        <h6><i class="fas fa-shopping-cart"></i> 订单商品</h6>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>商品</th>
                                        <th>单价</th>
                                        <th>数量</th>
                                        <th>小计</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($order->orderItems as $item)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                @if($item->product->image)
                                                    <img src="{{ asset('storage/' . $item->product->image) }}" 
                                                         alt="{{ $item->product->name }}" 
                                                         class="me-2" style="width: 40px; height: 40px; object-fit: cover;">
                                                @endif
                                                <div>
                                                    <strong>{{ $item->product->name }}</strong><br>
                                                    <small class="text-muted">{{ $item->product->productCategory->name ?? '未分类' }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>¥{{ number_format($item->price, 2) }}</td>
                                        <td>{{ $item->quantity }}</td>
                                        <td>¥{{ number_format($item->price * $item->quantity, 2) }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- 取消申请表单 -->
                    <form action="{{ route('orders.request-cancellation', $order) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="cancellation_reason" class="form-label">
                                <i class="fas fa-comment"></i> 取消原因 <span class="text-danger">*</span>
                            </label>
                            <textarea 
                                class="form-control @error('cancellation_reason') is-invalid @enderror" 
                                id="cancellation_reason" 
                                name="cancellation_reason" 
                                rows="4" 
                                placeholder="请详细说明取消订单的原因..."
                                required
                            >{{ old('cancellation_reason') }}</textarea>
                            @error('cancellation_reason')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                <i class="fas fa-lightbulb text-warning"></i>
                                请提供详细的取消原因，这将帮助商家更好地处理您的申请。
                            </div>
                        </div>

                        <!-- 注意事项 -->
                        <div class="alert alert-warning">
                            <h6><i class="fas fa-exclamation-triangle"></i> 重要提醒</h6>
                            <ul class="mb-0">
                                <li>取消申请需要商家审核，审核结果将通过系统通知您</li>
                                <li>如果订单已经开始处理或发货，取消申请可能被拒绝</li>
                                <li>取消申请一旦提交，无法修改，请仔细填写</li>
                                <li>如果申请被批准，商品库存将自动恢复</li>
                            </ul>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('orders.show', $order) }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> 返回订单详情
                            </a>
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-paper-plane"></i> 提交取消申请
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 