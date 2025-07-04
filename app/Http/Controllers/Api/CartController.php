<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function index(Request $request)
    {
        $cartItems = CartItem::with(['product.images', 'product.category'])
                           ->where('user_id', $request->user()->id)
                           ->get()
                           ->transform(function ($item) {
                               $item->product->primary_image_url = $item->product->primary_image ? $item->product->primary_image->image_url : null;
                               $item->product->final_price = $item->product->final_price;
                               $item->total_price = $item->total_price;
                               return $item;
                           });

        $cartSummary = [
            'total_items' => $cartItems->sum('quantity'),
            'subtotal' => $cartItems->sum('total_price'),
            'shipping_cost' => 0, // You can implement shipping calculation logic here
            'total' => $cartItems->sum('total_price'),
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'items' => $cartItems,
                'summary' => $cartSummary,
            ],
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1|max:100',
            'selected_attributes' => 'nullable|array',
        ]);

        $product = Product::where('status', 'active')
                         ->where('in_stock', true)
                         ->findOrFail($request->product_id);

        // Check stock availability
        if ($product->manage_stock && $product->stock_quantity < $request->quantity) {
            return response()->json([
                'success' => false,
                'message' => 'Insufficient stock available',
                'available_stock' => $product->stock_quantity,
            ], 422);
        }

        $cartItem = CartItem::where('user_id', $request->user()->id)
                          ->where('product_id', $request->product_id)
                          ->first();

        if ($cartItem) {
            // Update existing cart item
            $newQuantity = $cartItem->quantity + $request->quantity;
            
            if ($product->manage_stock && $product->stock_quantity < $newQuantity) {
                return response()->json([
                    'success' => false,
                    'message' => 'Insufficient stock available',
                    'available_stock' => $product->stock_quantity,
                    'current_in_cart' => $cartItem->quantity,
                ], 422);
            }

            $cartItem->update([
                'quantity' => $newQuantity,
                'selected_attributes' => $request->selected_attributes,
            ]);
        } else {
            // Create new cart item
            $cartItem = CartItem::create([
                'user_id' => $request->user()->id,
                'product_id' => $request->product_id,
                'quantity' => $request->quantity,
                'selected_attributes' => $request->selected_attributes,
            ]);
        }

        $cartItem->load(['product.images']);
        $cartItem->product->primary_image_url = $cartItem->product->primary_image ? $cartItem->product->primary_image->image_url : null;
        $cartItem->product->final_price = $cartItem->product->final_price;
        $cartItem->total_price = $cartItem->total_price;

        return response()->json([
            'success' => true,
            'message' => 'Product added to cart successfully',
            'data' => $cartItem,
        ]);
    }

    public function update(Request $request, CartItem $cartItem)
    {
        // Check if cart item belongs to authenticated user
        if ($cartItem->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        $request->validate([
            'quantity' => 'required|integer|min:1|max:100',
            'selected_attributes' => 'nullable|array',
        ]);

        $product = $cartItem->product;

        // Check stock availability
        if ($product->manage_stock && $product->stock_quantity < $request->quantity) {
            return response()->json([
                'success' => false,
                'message' => 'Insufficient stock available',
                'available_stock' => $product->stock_quantity,
            ], 422);
        }

        $cartItem->update([
            'quantity' => $request->quantity,
            'selected_attributes' => $request->selected_attributes,
        ]);

        $cartItem->load(['product.images']);
        $cartItem->product->primary_image_url = $cartItem->product->primary_image ? $cartItem->product->primary_image->image_url : null;
        $cartItem->product->final_price = $cartItem->product->final_price;
        $cartItem->total_price = $cartItem->total_price;

        return response()->json([
            'success' => true,
            'message' => 'Cart item updated successfully',
            'data' => $cartItem,
        ]);
    }

    public function destroy(Request $request, CartItem $cartItem)
    {
        // Check if cart item belongs to authenticated user
        if ($cartItem->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        $cartItem->delete();

        return response()->json([
            'success' => true,
            'message' => 'Product removed from cart successfully',
        ]);
    }

    public function clear(Request $request)
    {
        CartItem::where('user_id', $request->user()->id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Cart cleared successfully',
        ]);
    }

    public function count(Request $request)
    {
        $count = CartItem::where('user_id', $request->user()->id)->sum('quantity');

        return response()->json([
            'success' => true,
            'data' => ['count' => $count],
        ]);
    }
}
