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
                                <i class="fas fa-tags me-2"></i>分类管理
                            </h4>
                            <small>管理系统中的所有商品分类</small>
                        </div>
                        <div>
                            <a href="{{ route('admin.dashboard') }}" class="btn btn-light btn-sm me-2">
                                <i class="fas fa-arrow-left me-1"></i>返回仪表板
                            </a>
                            <a href="{{ route('admin.categories.create') }}" class="btn btn-success btn-sm">
                                <i class="fas fa-plus me-1"></i>添加分类
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

                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>图标</th>
                                    <th>分类名称</th>
                                    <th>描述</th>
                                    <th>商品数量</th>
                                    <th>排序</th>
                                    <th>状态</th>
                                    <th>创建时间</th>
                                    <th>操作</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($categories as $category)
                                    <tr>
                                        <td>{{ $category->id }}</td>
                                        <td>
                                            @if($category->icon)
                                                <i class="{{ $category->icon }} fa-lg text-primary"></i>
                                            @else
                                                <i class="fas fa-tag fa-lg text-muted"></i>
                                            @endif
                                        </td>
                                        <td>
                                            <strong>{{ $category->name }}</strong>
                                        </td>
                                        <td>
                                            <span class="text-muted">{{ Str::limit($category->description, 50) }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">{{ $category->products->count() }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary">{{ $category->sort_order }}</span>
                                        </td>
                                        <td>
                                            @if($category->is_active)
                                                <span class="badge bg-success">
                                                    <i class="fas fa-check me-1"></i>启用
                                                </span>
                                            @else
                                                <span class="badge bg-danger">
                                                    <i class="fas fa-times me-1"></i>禁用
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <small class="text-muted">
                                                {{ $category->created_at->format('Y-m-d H:i') }}
                                            </small>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('admin.categories.show', $category) }}" 
                                                   class="btn btn-outline-info btn-sm" 
                                                   title="查看详情">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.categories.edit', $category) }}" 
                                                   class="btn btn-outline-warning btn-sm" 
                                                   title="编辑分类">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button type="button" 
                                                        class="btn btn-outline-danger btn-sm" 
                                                        title="删除分类"
                                                        onclick="confirmDelete({{ $category->id }}, '{{ $category->name }}')"
                                                        {{ $category->products->count() > 0 ? 'disabled' : '' }}>
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="fas fa-tags fa-3x mb-3"></i>
                                                <p>暂无分类数据</p>
                                                <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">
                                                    <i class="fas fa-plus me-1"></i>添加第一个分类
                                                </a>
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
                            共 {{ $categories->count() }} 个分类
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
                <p>您确定要删除分类 <strong id="categoryName"></strong> 吗？</p>
                <p class="text-danger">
                    <i class="fas fa-warning me-1"></i>
                    此操作不可撤销！
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

<script>
function confirmDelete(categoryId, categoryName) {
    document.getElementById('categoryName').textContent = categoryName;
    document.getElementById('deleteForm').action = `/admin/categories/${categoryId}`;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}
</script>
@endsection 