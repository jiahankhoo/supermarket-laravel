@extends('layouts.app')

@section('title', '与 ' . $otherUser->name . ' 聊天')

@section('content')
<div class="container-fluid py-4">
    <!-- 页面标题和欢迎信息 -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex align-items-center">
                <i class="fas fa-comments fa-2x text-primary me-3"></i>
                <div>
                    <h2 class="mb-0">聊天对话</h2>
                    <p class="text-muted mb-0">与 {{ $otherUser->name }} 的实时对话</p>
                </div>
            </div>
        </div>
    </div>

    <!-- 聊天统计卡片 -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $messages->count() }}</h4>
                            <p class="mb-0">总消息数</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-comment fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $messages->where('sender_id', Auth::id())->count() }}</h4>
                            <p class="mb-0">我发送的</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-paper-plane fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $messages->where('sender_id', $otherUser->id)->count() }}</h4>
                            <p class="mb-0">对方发送的</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-user fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $messages->where('file_path', '!=', null)->count() }}</h4>
                            <p class="mb-0">文件消息</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-file fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 聊天主界面 -->
    <div class="row">
        <div class="col-md-9">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <div class="avatar-circle me-3">
                                <i class="fas fa-user"></i>
                            </div>
                            <div>
                                <h5 class="mb-0">{{ $otherUser->name }}</h5>
                                <small class="text-muted">
                                    @if($otherUser->role === 'admin')
                                        <i class="fas fa-shield-alt text-primary"></i> 在线客服
                                    @else
                                        <i class="fas fa-user text-success"></i> 客户
                                    @endif
                                    <span class="ms-2">
                                        <i class="fas fa-circle text-success"></i> 在线
                                    </span>
                                </small>
                            </div>
                        </div>
                        <div>
                            <a href="{{ route('chat.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left"></i> 返回列表
                            </a>
                        </div>
                    </div>
                </div>
                
                <div class="card-body p-0">
                    <!-- 聊天消息区域 -->
                    <div id="chat-messages" class="chat-container" style="height: 500px; overflow-y: auto; padding: 20px; background: #f8f9fa;">
                        @foreach($messages as $message)
                            <div class="message {{ $message->sender_id == Auth::id() ? 'message-sent' : 'message-received' }}">
                                <div class="message-content">
                                    @if($message->message)
                                        <div class="message-text">{{ $message->message }}</div>
                                    @endif
                                    
                                    @if($message->file_path)
                                        <div class="message-file">
                                            @if($message->file_type === 'image')
                                                <img src="/storage/{{ $message->file_path }}" 
                                                     alt="{{ $message->file_name }}" 
                                                     class="img-fluid rounded shadow-sm" 
                                                     style="max-width: 250px; max-height: 250px; cursor: pointer;"
                                                     onclick="openFileModal('/storage/{{ $message->file_path }}', '{{ $message->file_name }}')">
                                            @elseif($message->file_type === 'video')
                                                <video controls class="rounded shadow-sm" style="max-width: 250px; max-height: 250px;">
                                                    <source src="/storage/{{ $message->file_path }}" type="video/mp4">
                                                    您的浏览器不支持视频播放。
                                                </video>
                                            @else
                                                <div class="file-attachment">
                                                    <i class="fas fa-file"></i>
                                                    <span>{{ $message->file_name }}</span>
                                                    <small class="text-muted">({{ $message->file_size }})</small>
                                                    <a href="/storage/{{ $message->file_path }}" 
                                                       target="_blank" 
                                                       class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-download"></i> 下载
                                                    </a>
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                    
                                    <div class="message-time">
                                        <small class="text-muted">
                                            {{ $message->created_at->format('H:i') }}
                                            @if($message->sender_id == Auth::id())
                                                @if($message->is_read)
                                                    <i class="fas fa-check-double text-primary"></i>
                                                @else
                                                    <i class="fas fa-check text-muted"></i>
                                                @endif
                                            @endif
                                        </small>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    <!-- 输入区域 -->
                    <div class="chat-input-container p-4 border-top bg-white">
                        <form id="chat-form" class="d-flex">
                            <div class="flex-grow-1 me-3">
                                <input 
                                    type="text" 
                                    id="message-input" 
                                    class="form-control form-control-lg" 
                                    placeholder="输入消息..." 
                                    maxlength="1000"
                                >
                                <input 
                                    type="file" 
                                    id="file-input" 
                                    class="d-none" 
                                    accept="image/*,video/*,.pdf,.doc,.docx,.txt"
                                >
                            </div>
                            <button type="button" class="btn btn-outline-secondary btn-lg me-2" onclick="document.getElementById('file-input').click()">
                                <i class="fas fa-paperclip"></i>
                            </button>
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-paper-plane"></i>
                            </button>
                        </form>
                        
                        <div class="mt-3">
                            <small class="text-muted">
                                <i class="fas fa-info-circle"></i>
                                按 Enter 发送消息，Shift + Enter 换行，支持图片、视频和文档（最大500MB）
                            </small>
                        </div>
                        
                        <!-- 文件预览区域 -->
                        <div id="file-preview" class="mt-3" style="display: none;">
                            <div class="alert alert-info">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <i class="fas fa-file"></i>
                                        <span id="file-name"></span>
                                        <small class="text-muted" id="file-size"></small>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeFile()">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 侧边栏 -->
        <div class="col-md-3">
            <!-- 快速操作 -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h6 class="mb-0">
                        <i class="fas fa-bolt text-warning"></i> 快速操作
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button class="btn btn-outline-primary btn-sm" onclick="sendQuickMessage('您好，有什么可以帮助您的吗？')">
                            <i class="fas fa-handshake"></i> 问候语
                        </button>
                        <button class="btn btn-outline-success btn-sm" onclick="sendQuickMessage('感谢您的咨询，我们会尽快处理。')">
                            <i class="fas fa-thumbs-up"></i> 感谢语
                        </button>
                        <button class="btn btn-outline-info btn-sm" onclick="sendQuickMessage('请稍等，我正在为您查询相关信息。')">
                            <i class="fas fa-clock"></i> 稍等语
                        </button>
                    </div>
                </div>
            </div>

            <!-- 聊天信息 -->
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h6 class="mb-0">
                        <i class="fas fa-info-circle text-info"></i> 聊天信息
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <small class="text-muted">开始时间</small>
                        <div>{{ $messages->first() ? $messages->first()->created_at->format('Y-m-d H:i') : '暂无消息' }}</div>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted">最后消息</small>
                        <div>{{ $messages->last() ? $messages->last()->created_at->format('Y-m-d H:i') : '暂无消息' }}</div>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted">消息频率</small>
                        <div>
                            @if($messages->count() > 1)
                                @php
                                    $firstMessage = $messages->first();
                                    $lastMessage = $messages->last();
                                    $duration = $lastMessage->created_at->diffInMinutes($firstMessage->created_at);
                                    $frequency = $duration > 0 ? round($messages->count() / $duration, 2) : 0;
                                @endphp
                                {{ $frequency }} 条/分钟
                            @else
                                暂无数据
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 文件预览模态框 -->
<div class="modal fade" id="fileModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="fileModalTitle"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <img id="fileModalImage" src="" alt="" class="img-fluid d-none">
                <video id="fileModalVideo" controls class="img-fluid d-none">
                    <source src="" type="video/mp4">
                </video>
            </div>
        </div>
    </div>
</div>

<style>
.avatar-circle {
    width: 50px;
    height: 50px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.2rem;
}

.chat-container {
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
}

.message {
    margin-bottom: 20px;
    display: flex;
    animation: fadeInUp 0.3s ease-out;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.message-sent {
    justify-content: flex-end;
}

.message-received {
    justify-content: flex-start;
}

.message-content {
    max-width: 70%;
    padding: 15px 20px;
    border-radius: 20px;
    position: relative;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.message-sent .message-content {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-bottom-right-radius: 8px;
}

.message-received .message-content {
    background: white;
    border: 1px solid #e9ecef;
    border-bottom-left-radius: 8px;
}

.message-text {
    word-wrap: break-word;
    margin-bottom: 8px;
    line-height: 1.5;
}

.message-time {
    text-align: right;
    font-size: 0.75rem;
    opacity: 0.8;
}

.message-sent .message-time {
    color: rgba(255, 255, 255, 0.9);
}

.chat-input-container {
    background: white;
    border-top: 1px solid #e9ecef;
}

#message-input {
    border-radius: 25px;
    border: 2px solid #e9ecef;
    padding: 12px 20px;
    transition: all 0.3s ease;
}

#message-input:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
}

.btn-primary {
    border-radius: 50%;
    width: 50px;
    height: 50px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    transition: all 0.3s ease;
}

.btn-primary:hover {
    transform: scale(1.05);
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
}

.message-file {
    margin-top: 12px;
}

.file-attachment {
    background: rgba(255, 255, 255, 0.9);
    padding: 15px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    gap: 12px;
    border: 1px solid #e9ecef;
}

.file-attachment i {
    font-size: 1.8rem;
    color: #667eea;
}

.card {
    border: none;
    border-radius: 15px;
    overflow: hidden;
    transition: all 0.3s ease;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.1);
}

.card-header {
    border-bottom: 1px solid #e9ecef;
    padding: 1.25rem;
}

.btn-outline-secondary {
    border-radius: 25px;
    padding: 8px 20px;
    transition: all 0.3s ease;
}

.btn-outline-secondary:hover {
    transform: translateX(-2px);
}

.flex-grow-1 {
    flex-grow: 1;
}

/* 统计卡片样式 */
.bg-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
}

.bg-success {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%) !important;
}

.bg-info {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
}

.bg-warning {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%) !important;
}

/* 快速操作按钮样式 */
.btn-outline-primary {
    border-radius: 8px;
    transition: all 0.3s ease;
}

.btn-outline-primary:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
}

.btn-outline-success {
    border-radius: 8px;
    transition: all 0.3s ease;
}

.btn-outline-success:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(17, 153, 142, 0.3);
}

.btn-outline-info {
    border-radius: 8px;
    transition: all 0.3s ease;
}

.btn-outline-info:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
}
</style>

@push('scripts')
<script>
const currentUserId = {{ Auth::id() }};
const otherUserId = {{ $otherUser->id }};
const chatMessages = document.getElementById('chat-messages');
const chatForm = document.getElementById('chat-form');
const messageInput = document.getElementById('message-input');
const fileInput = document.getElementById('file-input');
const filePreview = document.getElementById('file-preview');
const fileName = document.getElementById('file-name');
let selectedFile = null;

// 滚动到底部
function scrollToBottom() {
    chatMessages.scrollTop = chatMessages.scrollHeight;
}

// 页面加载时滚动到底部
document.addEventListener('DOMContentLoaded', function() {
    scrollToBottom();
});

// 发送快速消息
function sendQuickMessage(message) {
    messageInput.value = message;
    chatForm.dispatchEvent(new Event('submit'));
}

// 发送消息
chatForm.addEventListener('submit', function(e) {
    e.preventDefault();
    
    const message = messageInput.value.trim();
    if (!message && !selectedFile) return;
    
    const formData = new FormData();
    if (message) {
        formData.append('message', message);
    }
    if (selectedFile) {
        formData.append('file', selectedFile);
    }
    
    // 发送消息到服务器
    fetch(`/chat/send/${otherUserId}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // 添加消息到聊天界面
            addMessage(data.message);
            messageInput.value = '';
            removeFile();
            scrollToBottom();
        } else {
            alert('发送失败：' + data.error);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('发送失败，请重试');
    });
});

// 添加消息到界面
function addMessage(messageData) {
    const messageDiv = document.createElement('div');
    messageDiv.className = `message ${messageData.sender_id == currentUserId ? 'message-sent' : 'message-received'}`;
    
    const isSent = messageData.sender_id == currentUserId;
    const time = new Date(messageData.created_at).toLocaleTimeString('zh-CN', { 
        hour: '2-digit', 
        minute: '2-digit' 
    });
    
    let fileHtml = '';
    if (messageData.file_path) {
        if (messageData.file_type === 'image') {
            fileHtml = `
                <div class="message-file">
                    <img src="/storage/${messageData.file_path}" 
                         alt="${messageData.file_name}" 
                         class="img-fluid rounded shadow-sm" 
                         style="max-width: 250px; max-height: 250px; cursor: pointer;"
                         onclick="openFileModal('/storage/${messageData.file_path}', '${messageData.file_name}')">
                </div>
            `;
        } else if (messageData.file_type === 'video') {
            fileHtml = `
                <div class="message-file">
                    <video controls class="rounded shadow-sm" style="max-width: 250px; max-height: 250px;">
                        <source src="/storage/${messageData.file_path}" type="video/mp4">
                        您的浏览器不支持视频播放。
                    </video>
                </div>
            `;
        } else {
            fileHtml = `
                <div class="message-file">
                    <div class="file-attachment">
                        <i class="fas fa-file"></i>
                        <span>${messageData.file_name}</span>
                        <small class="text-muted">(${messageData.file_size})</small>
                        <a href="/storage/${messageData.file_path}" 
                           target="_blank" 
                           class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-download"></i> 下载
                        </a>
                    </div>
                </div>
            `;
        }
    }
    
    messageDiv.innerHTML = `
        <div class="message-content">
            ${messageData.message ? `<div class="message-text">${escapeHtml(messageData.message)}</div>` : ''}
            ${fileHtml}
            <div class="message-time">
                <small class="text-muted">
                    ${time}
                    ${isSent ? '<i class="fas fa-check text-muted"></i>' : ''}
                </small>
            </div>
        </div>
    `;
    
    chatMessages.appendChild(messageDiv);
}

// HTML转义
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// 处理Enter键
messageInput.addEventListener('keydown', function(e) {
    if (e.key === 'Enter' && !e.shiftKey) {
        e.preventDefault();
        chatForm.dispatchEvent(new Event('submit'));
    }
});

// 定期检查新消息
function checkNewMessages() {
    fetch(`/chat/messages/${otherUserId}`)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(messages => {
            const currentMessageCount = chatMessages.querySelectorAll('.message').length;
            if (messages.length > currentMessageCount) {
                // 有新消息，刷新整个聊天记录
                location.reload();
            }
        })
        .catch(error => {
            console.error('Error checking messages:', error);
            // 如果连续失败多次，停止检查
            if (window.messageCheckFailures === undefined) {
                window.messageCheckFailures = 0;
            }
            window.messageCheckFailures++;
            
            if (window.messageCheckFailures > 5) {
                console.log('停止检查新消息，因为连续失败次数过多');
                clearInterval(window.messageCheckInterval);
            }
        });
}

// 文件选择处理
fileInput.addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        // 检查文件大小（500MB限制）
        if (file.size > 500 * 1024 * 1024) {
            alert('文件大小不能超过500MB');
            return;
        }
        
        selectedFile = file;
        fileName.textContent = file.name;
        document.getElementById('file-size').textContent = `(${formatFileSize(file.size)})`;
        filePreview.style.display = 'block';
    }
});

// 格式化文件大小
function formatFileSize(bytes) {
    if (bytes >= 1073741824) {
        return (bytes / 1073741824).toFixed(2) + ' GB';
    } else if (bytes >= 1048576) {
        return (bytes / 1048576).toFixed(2) + ' MB';
    } else if (bytes >= 1024) {
        return (bytes / 1024).toFixed(2) + ' KB';
    } else {
        return bytes + ' bytes';
    }
}

// 移除文件
function removeFile() {
    selectedFile = null;
    fileInput.value = '';
    filePreview.style.display = 'none';
}

// 打开文件模态框
function openFileModal(fileUrl, fileName) {
    const modal = new bootstrap.Modal(document.getElementById('fileModal'));
    const modalTitle = document.getElementById('fileModalTitle');
    const modalImage = document.getElementById('fileModalImage');
    const modalVideo = document.getElementById('fileModalVideo');
    
    modalTitle.textContent = fileName;
    
    if (fileUrl.match(/\.(jpg|jpeg|png|gif|webp)$/i)) {
        modalImage.src = fileUrl;
        modalImage.classList.remove('d-none');
        modalVideo.classList.add('d-none');
    } else if (fileUrl.match(/\.(mp4|avi|mov|wmv|flv|webm)$/i)) {
        modalVideo.src = fileUrl;
        modalVideo.classList.remove('d-none');
        modalImage.classList.add('d-none');
    }
    
    modal.show();
}

// 每5秒检查一次新消息
window.messageCheckInterval = setInterval(checkNewMessages, 5000);
</script>
@endpush
@endsection 