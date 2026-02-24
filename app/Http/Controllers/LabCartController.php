<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LabCartController extends Controller
{
    public function index()
    {
        $cart = Cart::firstOrCreate([
            'user_id' => auth()->id(),
            'status' => 'open',
        ]);

        $cart->load('items.product');

        return view('lab.cart', compact('cart'));
    }

    public function products()
    {
        $products = Product::with('requester')->paginate(12);
        return view('lab.products.index', compact('products'));
    }

    public function add(Request $request, Product $product)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        DB::transaction(function () use ($product, $request) {
            $quantity = (int) $request->quantity;

            $product->refresh();
            if ($product->quantity < $quantity) {
                abort(422, 'Insufficient stock for this product.');
            }

            $cart = Cart::firstOrCreate([
                'user_id' => auth()->id(),
                'status' => 'open',
            ]);

            $item = CartItem::firstOrNew([
                'cart_id' => $cart->id,
                'product_id' => $product->id,
            ]);

            $item->unit_price = $product->price;
            $item->quantity = ($item->exists ? $item->quantity : 0) + $quantity;
            $item->save();

            $product->decrement('quantity', $quantity);
        });

        return back()->with('success', 'Product added to cart.');
    }

    public function update(Request $request, CartItem $item)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        DB::transaction(function () use ($item, $request) {
            $newQty = (int) $request->quantity;
            $delta = $newQty - $item->quantity;

            if ($delta === 0) {
                return;
            }

            $product = Product::lockForUpdate()->find($item->product_id);
            if ($delta > 0) {
                if ($product->quantity < $delta) {
                    abort(422, 'Insufficient stock for this product.');
                }
                $product->decrement('quantity', $delta);
            } else {
                $product->increment('quantity', abs($delta));
            }

            $item->update(['quantity' => $newQty]);
        });

        return back()->with('success', 'Cart updated.');
    }

    public function delete(CartItem $item)
    {
        DB::transaction(function () use ($item) {
            $product = Product::lockForUpdate()->find($item->product_id);
            $product->increment('quantity', $item->quantity);
            $item->delete();
        });

        return back()->with('success', 'Item removed from cart.');
    }

    public function confirm()
    {
        $cart = Cart::with('items.product')
            ->where('user_id', auth()->id())
            ->where('status', 'open')
            ->firstOrFail();

        if ($cart->items->count() === 0) {
            return back()->with('success', 'Cart is empty.');
        }

        $cart->update([
            'status' => 'checked_out',
            'confirmed_at' => now(),
        ]);

        return redirect()->route('lab.cart.index')->with('success', 'Cart confirmed successfully.');
    }

    public function confirmedList()
    {
        $carts = Cart::confirmed()
            ->with(['user', 'items.product', 'items'])
            ->latest('confirmed_at')
            ->paginate(10);

        return view('lab.confirmed', compact('carts'));
    }
}
