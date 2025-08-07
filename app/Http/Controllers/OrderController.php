<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\CartItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index()
    {
        if (Auth::user()->isAdmin()) {
            $orders = Order::with(['user', 'orderItems.product', 'adminResponder'])->latest()->get();
        } else {
            $orders = Auth::user()->orders()->with('orderItems.product')->latest()->get();
        }

        return view('orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        if (!Auth::user()->isAdmin() && $order->user_id !== Auth::id()) {
            return redirect()->route('home')->with('error', '无权限查看此订单');
        }

        return view('orders.show', compact('order'));
    }

    public function checkout()
    {
        if (!Auth::user()->isUser()) {
            return redirect()->route('home')->with('error', '只有普通用户可以下单');
        }

        $cartItems = Auth::user()->cartItems()->with('product')->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', '购物车为空');
        }

        // 检查库存
        foreach ($cartItems as $item) {
            if ($item->product->stock < $item->quantity) {
                return redirect()->route('cart.index')
                    ->with('error', "商品 {$item->product->name} 库存不足");
            }
        }

        $total = $cartItems->sum(function ($item) {
            return $item->product->price * $item->quantity;
        });

        return view('orders.checkout', compact('cartItems', 'total'));
    }

    public function store(Request $request)
    {
        if (!Auth::user()->isUser()) {
            return redirect()->route('home')->with('error', '只有普通用户可以下单');
        }

        $request->validate([
            'receiver_name' => 'required|string|max:255',
            'receiver_phone' => 'required|string|max:20',
            'shipping_address' => 'required|string|max:500',
            'city' => 'required|string|max:100',
            'postal_code' => 'required|string|max:10',
            'notes' => 'nullable|string|max:500',
        ]);

        $cartItems = Auth::user()->cartItems()->with('product')->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', '购物车为空');
        }

        // 再次检查库存
        foreach ($cartItems as $item) {
            if ($item->product->stock < $item->quantity) {
                return redirect()->route('cart.index')
                    ->with('error', "商品 {$item->product->name} 库存不足");
            }
        }

        try {
            DB::beginTransaction();

            $total = $cartItems->sum(function ($item) {
                return $item->product->price * $item->quantity;
            });

            $order = Order::create([
                'user_id' => Auth::id(),
                'total_amount' => $total,
                'status' => 'pending',
                'receiver_name' => $request->receiver_name,
                'receiver_phone' => $request->receiver_phone,
                'shipping_address' => $request->shipping_address,
                'city' => $request->city,
                'postal_code' => $request->postal_code,
                'notes' => $request->notes,
            ]);

            foreach ($cartItems as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'price' => $item->product->price,
                ]);

                // 更新库存
                $item->product->decrement('stock', $item->quantity);
            }

            // 清空购物车
            $cartItems->each->delete();

            DB::commit();

            return redirect()->route('orders.show', $order)
                ->with('success', '订单提交成功！');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('cart.index')
                ->with('error', '订单提交失败，请重试');
        }
    }

    public function updateStatus(Request $request, Order $order)
    {
        if (!Auth::user()->isAdmin()) {
            return redirect()->route('home')->with('error', '只有管理员可以更新订单状态');
        }

        $request->validate([
            'status' => 'required|in:pending,processing,completed,cancelled,cancellation_requested',
        ]);

        $order->update(['status' => $request->status]);

        return redirect()->route('orders.show', $order)
            ->with('success', '订单状态已更新');
    }

    // 客户申请取消订单
    public function requestCancellation(Request $request, Order $order)
    {
        if (!Auth::user()->isUser() || $order->user_id !== Auth::id()) {
            return redirect()->route('home')->with('error', '无权限操作此订单');
        }

        if (!$order->canRequestCancellation()) {
            return redirect()->route('orders.show', $order)
                ->with('error', '此订单无法申请取消');
        }

        $request->validate([
            'cancellation_reason' => 'required|string|max:500',
        ]);

        $order->update([
            'status' => 'cancellation_requested',
            'cancellation_reason' => $request->cancellation_reason,
            'cancellation_requested_at' => now(),
        ]);

        return redirect()->route('orders.show', $order)
            ->with('success', '取消申请已提交，等待商家审核');
    }

    // 商家回复取消申请
    public function respondToCancellation(Request $request, Order $order)
    {
        if (!Auth::user()->isAdmin()) {
            return redirect()->route('home')->with('error', '只有管理员可以回复取消申请');
        }

        if ($order->status !== 'cancellation_requested') {
            return redirect()->route('orders.show', $order)
                ->with('error', '此订单没有取消申请');
        }

        $request->validate([
            'admin_response' => 'required|string|max:500',
            'action' => 'required|in:approve,reject',
        ]);

        $newStatus = $request->action === 'approve' ? 'cancelled' : 'processing';
        
        // 如果批准取消，需要恢复库存
        if ($request->action === 'approve') {
            try {
                DB::beginTransaction();
                
                foreach ($order->orderItems as $item) {
                    $item->product->increment('stock', $item->quantity);
                }
                
                $order->update([
                    'status' => $newStatus,
                    'admin_response' => $request->admin_response,
                    'admin_responded_at' => now(),
                    'responded_by' => Auth::id(),
                ]);
                
                DB::commit();
                
                return redirect()->route('orders.show', $order)
                    ->with('success', '已批准取消申请，订单已取消');
                    
            } catch (\Exception $e) {
                DB::rollBack();
                return redirect()->route('orders.show', $order)
                    ->with('error', '操作失败，请重试');
            }
        } else {
            $order->update([
                'status' => $newStatus,
                'admin_response' => $request->admin_response,
                'admin_responded_at' => now(),
                'responded_by' => Auth::id(),
            ]);

            return redirect()->route('orders.show', $order)
                ->with('success', '已拒绝取消申请，订单继续处理');
        }
    }

    // 显示取消申请表单
    public function showCancellationForm(Order $order)
    {
        if (!Auth::user()->isUser() || $order->user_id !== Auth::id()) {
            return redirect()->route('home')->with('error', '无权限操作此订单');
        }

        if (!$order->canRequestCancellation()) {
            return redirect()->route('orders.show', $order)
                ->with('error', '此订单无法申请取消');
        }

        return view('orders.cancellation-form', compact('order'));
    }
} 