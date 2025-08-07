@extends('layouts.app')

@section('title', '与 ' . $otherUser->name . ' 聊天')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-user-circle fa-2x text-primary me-3"></i>
                            <div>
                                <h5 class="mb-0">{{ $otherUser->name }}</h5>
                                <small class="text-muted">
                                    @if($otherUser->role === 'admin')
                                        在线客服
                                    @else
                                        客户
                                    @endif
                                </small>
                            </div>
                        </div>
                        <div>
                            <a href="{{ route('chat.index') }}" class="btn btn-outline-secondary btn-sm">
                                <i class="fas fa-arrow-left"></i> 返回列表
                            </a>
                        </div>
                    </div>
                </div>
                
                <div class="card-body p-0">
                    <!-- 聊天消息区域 -->
                    <div id="chat-messages" class="chat-container" style="height: 400px; overflow-y: auto; padding: 20px;">
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
                                                      class="img-fluid rounded" 
                                                      style="max-width: 200px; max-height: 200px; cursor: pointer;"
                                                      onclick="openFileModal('/storage/{{ $message->file_path }}', '{{ $message->file_name }}')">
                                             @elseif($message->file_type === 'video')
                                                 <video controls style="max-width: 200px; max-height: 200px;" class="rounded">
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
                    <div class="chat-input-container p-3 border-top">
                        <form id="chat-form" class="d-flex">
                            <div class="flex-grow-1 me-2">
                                <input 
                                    type="text" 
                                    id="message-input" 
                                    class="form-control" 
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
                            <button type="button" class="btn btn-outline-secondary me-2" onclick="document.getElementById('file-input').click()">
                                <i class="fas fa-paperclip"></i>
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane"></i>
                            </button>
                        </form>
                                                 <div class="mt-2">
                             <small class="text-muted">
                                 <i class="fas fa-info-circle"></i>
                                 按 Enter 发送消息，Shift + Enter 换行，支持图片、视频和文档（最大500MB）
                             </small>
                         </div>
                        <div id="file-preview" class="mt-2 d-none">
                            <div class="alert alert-info">
                                <i class="fas fa-file"></i>
                                <span id="file-name"></span>
                                <button type="button" class="btn btn-sm btn-outline-danger ms-2" onclick="removeFile()">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
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
.chat-container {
    background-color: #f8f9fa;
}

.message {
    margin-bottom: 15px;
    display: flex;
}

.message-sent {
    justify-content: flex-end;
}

.message-received {
    justify-content: flex-start;
}

.message-content {
    max-width: 70%;
    padding: 10px 15px;
    border-radius: 18px;
    position: relative;
}

.message-sent .message-content {
    background-color: #007bff;
    color: white;
    border-bottom-right-radius: 5px;
}

.message-received .message-content {
    background-color: white;
    border: 1px solid #dee2e6;
    border-bottom-left-radius: 5px;
}

.message-text {
    word-wrap: break-word;
    margin-bottom: 5px;
}

.message-time {
    text-align: right;
    font-size: 0.75rem;
}

.message-sent .message-time {
    color: rgba(255, 255, 255, 0.8);
}

.chat-input-container {
    background-color: white;
}

#message-input {
    border-radius: 20px;
    border: 1px solid #dee2e6;
}

#message-input:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

.btn-primary {
    border-radius: 50%;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.message-file {
    margin-top: 10px;
}

.file-attachment {
    background-color: rgba(0, 0, 0, 0.05);
    padding: 10px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.file-attachment i {
    font-size: 1.5rem;
    color: #6c757d;
}

.flex-grow-1 {
    flex-grow: 1;
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
                         class="img-fluid rounded" 
                         style="max-width: 200px; max-height: 200px; cursor: pointer;"
                         onclick="openFileModal('/storage/${messageData.file_path}', '${messageData.file_name}')">
                </div>
            `;
        } else if (messageData.file_type === 'video') {
            fileHtml = `
                <div class="message-file">
                    <video controls style="max-width: 200px; max-height: 200px;" class="rounded">
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
        .then(response => response.json())
        .then(messages => {
            const currentMessageCount = chatMessages.querySelectorAll('.message').length;
            if (messages.length > currentMessageCount) {
                // 有新消息，刷新整个聊天记录
                location.reload();
            }
        })
        .catch(error => {
            console.error('Error checking messages:', error);
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
        filePreview.classList.remove('d-none');
    }
});

// 移除文件
function removeFile() {
    selectedFile = null;
    fileInput.value = '';
    filePreview.classList.add('d-none');
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
setInterval(checkNewMessages, 5000);
</script>
@endpush
@endsection 