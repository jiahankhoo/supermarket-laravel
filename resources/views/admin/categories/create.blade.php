@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-0">
                                <i class="fas fa-plus me-2"></i>添加新分类
                            </h4>
                            <small>创建新的商品分类</small>
                        </div>
                        <div>
                            <a href="{{ route('admin.categories.index') }}" class="btn btn-light btn-sm">
                                <i class="fas fa-arrow-left me-1"></i>返回列表
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.categories.store') }}">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="name" class="form-label">
                                <i class="fas fa-tag me-1"></i>分类名称 <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name') }}" 
                                   placeholder="请输入分类名称" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">
                                <i class="fas fa-align-left me-1"></i>分类描述
                            </label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="3" 
                                      placeholder="请输入分类描述（可选）">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="icon" class="form-label">
                                <i class="fas fa-icons me-1"></i>分类图标
                            </label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                                <input type="text" class="form-control @error('icon') is-invalid @enderror" 
                                       id="icon" name="icon" value="{{ old('icon') }}" 
                                       placeholder="例如：fas fa-apple-alt">
                            </div>
                            <div class="form-text">
                                <i class="fas fa-info-circle me-1"></i>
                                使用 Font Awesome 图标类名，如：fas fa-apple-alt, fas fa-milk, fas fa-bread-slice
                            </div>
                            @error('icon')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="sort_order" class="form-label">
                                <i class="fas fa-sort me-1"></i>排序
                            </label>
                            <input type="number" class="form-control @error('sort_order') is-invalid @enderror" 
                                   id="sort_order" name="sort_order" value="{{ old('sort_order', 0) }}" 
                                   min="0" placeholder="0">
                            <div class="form-text">
                                <i class="fas fa-info-circle me-1"></i>
                                数字越小排序越靠前，默认为0
                            </div>
                            @error('sort_order')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary me-md-2">
                                <i class="fas fa-times me-1"></i>取消
                            </a>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save me-1"></i>保存分类
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- 图标预览 -->
            <div class="card mt-4">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-eye me-1"></i>常用图标预览
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-2">
                            <div class="text-center p-2 border rounded">
                                <i class="fas fa-apple-alt fa-2x text-success mb-2"></i>
                                <div class="small">fas fa-apple-alt</div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-2">
                            <div class="text-center p-2 border rounded">
                                <i class="fas fa-milk fa-2x text-info mb-2"></i>
                                <div class="small">fas fa-milk</div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-2">
                            <div class="text-center p-2 border rounded">
                                <i class="fas fa-bread-slice fa-2x text-warning mb-2"></i>
                                <div class="small">fas fa-bread-slice</div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-2">
                            <div class="text-center p-2 border rounded">
                                <i class="fas fa-carrot fa-2x text-danger mb-2"></i>
                                <div class="small">fas fa-carrot</div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-2">
                            <div class="text-center p-2 border rounded">
                                <i class="fas fa-tshirt fa-2x text-primary mb-2"></i>
                                <div class="small">fas fa-tshirt</div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-2">
                            <div class="text-center p-2 border rounded">
                                <i class="fas fa-mobile-alt fa-2x text-secondary mb-2"></i>
                                <div class="small">fas fa-mobile-alt</div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-2">
                            <div class="text-center p-2 border rounded">
                                <i class="fas fa-home fa-2x text-dark mb-2"></i>
                                <div class="small">fas fa-home</div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-2">
                            <div class="text-center p-2 border rounded">
                                <i class="fas fa-gift fa-2x text-danger mb-2"></i>
                                <div class="small">fas fa-gift</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 