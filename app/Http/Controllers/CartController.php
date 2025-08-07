<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function index()
    {
        if (!Auth::user()->isUser()) {
            return redirect()->route('home')->with('error', '只有普通用户可以查看购物车');
        }

        $cartItems = Auth::user()->cartItems()->with('product')->get();
        $total = $cartItems->sum(function ($item) {
            return $item->product->price * $item->quantity;
        });

        return view('cart.index', compact('cartItems', 'total'));
    }

    public function add(Request $request, Product $product)
    {
        if (!Auth::user()->isUser()) {
            return redirect()->route('home')->with('error', '只有普通用户可以添加商品到购物车');
        }

        $request->validate([
            'quantity' => 'required|integer|min:1|max:' . $product->stock,
        ]);

        if ($product->stock < $request->quantity) {
            return back()->with('error', '库存不足');
        }

        $cartItem = CartItem::where('user_id', Auth::id())
            ->where('product_id', $product->id)
            ->first();

        if ($cartItem) {
            $cartItem->update([
                'quantity' => $cartItem->quantity + $request->quantity
            ]);
        } else {
            CartItem::create([
                'user_id' => Auth::id(),
                'product_id' => $product->id,
                'quantity' => $request->quantity,
            ]);
        }

        return redirect()->route('cart.index')->with('success', '已添加到购物车');
    }

    public function remove(CartItem $cartItem)
    {
        if ($cartItem->user_id !== Auth::id()) {
            return redirect()->route('cart.index')->with('error', '无权限');
        }

        $cartItem->delete();

        return redirect()->route('cart.index')->with('success', '已从购物车移除');
    }

    public function update(Request $request, CartItem $cartItem)
    {
        if ($cartItem->user_id !== Auth::id()) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => '无权限'], 403);
            }
            return redirect()->route('cart.index')->with('error', '无权限');
        }

        $request->validate([
            'quantity' => 'required|integer|min:1|max:' . $cartItem->product->stock,
        ]);

        $cartItem->update(['quantity' => $request->quantity]);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => '购物车已更新']);
        }

        return redirect()->route('cart.index')->with('success', '购物车已更新');
    }
} 