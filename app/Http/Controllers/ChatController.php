<?php

namespace App\Http\Controllers;

use App\Models\ChatMessage;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        if ($user->isAdmin()) {
            // 管理员看到所有客户列表
            $customers = User::where('role', 'user')
                           ->where('id', '!=', $user->id)
                           ->orderBy('name')
                           ->get();
            
            return view('chat.admin-index', compact('customers'));
        } else {
            // 普通用户看到管理员列表
            $admins = User::where('role', 'admin')
                         ->where('id', '!=', $user->id)
                         ->orderBy('name')
                         ->get();
            
            return view('chat.customer-index', compact('admins'));
        }
    }

    public function show($userId)
    {
        $currentUser = Auth::user();
        $otherUser = User::findOrFail($userId);
        
        // 检查权限
        if ($currentUser->isAdmin()) {
            // 管理员只能与客户聊天
            if ($otherUser->isAdmin()) {
                return redirect()->route('chat.index')->with('error', '无法与管理员聊天');
            }
        } else {
            // 客户只能与管理员聊天
            if (!$otherUser->isAdmin()) {
                return redirect()->route('chat.index')->with('error', '无法与其他客户聊天');
            }
        }
        
        // 获取聊天记录
        $messages = ChatMessage::getConversation($currentUser->id, $otherUser->id);
        
        // 标记消息为已读
        ChatMessage::where('sender_id', $otherUser->id)
                   ->where('receiver_id', $currentUser->id)
                   ->where('is_read', false)
                   ->update(['is_read' => true, 'read_at' => now()]);
        
        return view('chat.show', compact('otherUser', 'messages'));
    }

    public function send(Request $request, $userId)
    {
        $request->validate([
            'message' => 'nullable|string|max:1000',
            'file' => 'nullable|file|max:512000', // 最大500MB
        ]);
        
        $currentUser = Auth::user();
        $otherUser = User::findOrFail($userId);
        
        // 检查权限
        if ($currentUser->isAdmin()) {
            if ($otherUser->isAdmin()) {
                return response()->json(['error' => '无法与管理员聊天'], 403);
            }
        } else {
            if (!$otherUser->isAdmin()) {
                return response()->json(['error' => '无法与其他客户聊天'], 403);
            }
        }
        
        $messageData = [
            'sender_id' => $currentUser->id,
            'receiver_id' => $otherUser->id,
            'message' => $request->message,
        ];
        
        // 处理文件上传
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('chat_files', $fileName, 'public');
            
            // 确定文件类型
            $fileType = 'document';
            $mimeType = $file->getMimeType();
            if (str_starts_with($mimeType, 'image/')) {
                $fileType = 'image';
            } elseif (str_starts_with($mimeType, 'video/')) {
                $fileType = 'video';
            }
            
            $messageData['file_path'] = $filePath;
            $messageData['file_name'] = $file->getClientOriginalName();
            $messageData['file_type'] = $fileType;
            $messageData['file_size'] = $this->formatFileSize($file->getSize());
            
            // 同步文件到public目录
            $this->syncFileToPublic($filePath);
        }
        
        // 创建消息
        $message = ChatMessage::create($messageData);
        $message->load('sender');
        
        return response()->json([
            'success' => true,
            'message' => $message,
        ]);
    }
    
    private function formatFileSize($bytes)
    {
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' bytes';
        }
    }
    
    private function syncFileToPublic($filePath)
    {
        $sourceFile = storage_path('app/public/' . $filePath);
        $targetFile = public_path('storage/' . $filePath);
        
        // 确保目标目录存在
        $targetDir = dirname($targetFile);
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }
        
        // 复制文件
        if (file_exists($sourceFile)) {
            copy($sourceFile, $targetFile);
        }
    }

    public function getMessages($userId)
    {
        $currentUser = Auth::user();
        $otherUser = User::findOrFail($userId);
        
        // 获取聊天记录
        $messages = ChatMessage::getConversation($currentUser->id, $otherUser->id);
        
        // 标记消息为已读
        ChatMessage::where('sender_id', $otherUser->id)
                   ->where('receiver_id', $currentUser->id)
                   ->where('is_read', false)
                   ->update(['is_read' => true, 'read_at' => now()]);
        
        return response()->json($messages);
    }

    public function getUnreadCount($userId)
    {
        $currentUser = Auth::user();
        $otherUser = User::findOrFail($userId);
        
        // 检查权限
        if ($currentUser->isAdmin()) {
            if ($otherUser->isAdmin()) {
                return response()->json(['count' => 0]);
            }
        } else {
            if (!$otherUser->isAdmin()) {
                return response()->json(['count' => 0]);
            }
        }
        
        // 获取两个用户之间的未读消息数量
        $count = ChatMessage::where('sender_id', $otherUser->id)
                           ->where('receiver_id', $currentUser->id)
                           ->where('is_read', false)
                           ->count();
        
        return response()->json(['count' => $count]);
    }
} 