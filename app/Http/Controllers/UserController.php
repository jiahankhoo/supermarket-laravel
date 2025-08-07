<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index(Request $request)
    {
        if (!Auth::user()->isAdmin()) {
            return redirect()->route('home')->with('error', '只有管理员可以查看用户列表');
        }

        $query = User::where('id', '!=', Auth::id()); // 排除当前管理员

        // 搜索功能
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // 角色筛选 - 只显示普通用户，因为管理员只有一个
        $query->where('role', 'user');

        // 注册时间筛选
        if ($request->filled('date_filter')) {
            switch ($request->date_filter) {
                case 'today':
                    $query->whereDate('created_at', today());
                    break;
                case 'week':
                    $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                    break;
                case 'month':
                    $query->whereMonth('created_at', now()->month);
                    break;
            }
        }

        $users = $query->latest()->get();

        return view('admin.users.index', compact('users'));
    }

    public function show(User $user)
    {
        if (!Auth::user()->isAdmin()) {
            return redirect()->route('home')->with('error', '只有管理员可以查看用户信息');
        }

        return view('admin.users.show', compact('user'));
    }

    public function edit(User $user)
    {
        if (!Auth::user()->isAdmin()) {
            return redirect()->route('home')->with('error', '只有管理员可以编辑用户信息');
        }

        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        if (!Auth::user()->isAdmin()) {
            return redirect()->route('home')->with('error', '只有管理员可以编辑用户信息');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role' => 'required|in:admin,user',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
        ];

        // 如果提供了新密码，则更新密码
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('admin.users.index')->with('success', '用户信息已更新');
    }

    public function destroy(User $user)
    {
        if (!Auth::user()->isAdmin()) {
            return redirect()->route('home')->with('error', '只有管理员可以删除用户');
        }

        if ($user->id === Auth::id()) {
            return redirect()->route('admin.users.index')->with('error', '不能删除自己的账号');
        }

        // 删除用户相关的购物车项目
        $user->cartItems()->delete();
        
        // 删除用户的订单
        $user->orders()->delete();
        
        // 删除用户
        $user->delete();

        return redirect()->route('admin.users.index')->with('success', '用户已删除');
    }
} 