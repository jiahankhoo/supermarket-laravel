<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function showForm()
    {
        return view('contact.form');
    }

    public function submit(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:1000',
            'order_id' => 'nullable|exists:orders,id',
        ]);

        // 验证订单权限（如果提供了订单ID）
        if ($request->order_id) {
            $order = \App\Models\Order::find($request->order_id);
            if (!$order || ($order->user_id !== Auth::id() && !Auth::user()->isAdmin())) {
                return redirect()->back()->with('error', '无权限访问此订单');
            }
        }

        // 这里可以添加发送邮件或保存到数据库的逻辑
        // 目前我们只是显示成功消息
        
        return redirect()->route('contact.form')
            ->with('success', '您的消息已发送，我们会尽快回复您！');
    }

    public function showAdminMessages()
    {
        if (!Auth::user()->isAdmin()) {
            return redirect()->route('home')->with('error', '只有管理员可以查看联系消息');
        }

        // 这里可以显示所有联系消息
        // 目前返回空视图
        return view('contact.admin-messages');
    }
}
