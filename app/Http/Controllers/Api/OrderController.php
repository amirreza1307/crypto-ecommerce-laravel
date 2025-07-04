<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\CartItem;
use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $orders = Order::with(['items.product.images'])
                      ->where('user_id', $request->user()->id)
                      ->when($request->status, function ($query, $status) {
                          return $query->where('status', $status);
                      })
                      ->orderBy('created_at', 'desc')
                      ->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $orders,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'shipping_address' => 'required|array',
            'shipping_address.name' => 'required|string',
            'shipping_address.phone' => 'required|string',
            'shipping_address.street' => 'required|string',
            'shipping_address.city' => 'required|string',
            'shipping_address.state' => 'required|string',
            'shipping_address.postal_code' => 'required|string',
            'shipping_address.country' => 'required|string',
            'billing_address' => 'required|array',
            'payment_method' => 'required|in:wallet,bank_transfer,crypto',
            'coupon_code' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $user = $request->user();
        $cartItems = CartItem::with('product')->where('user_id', $user->id)->get();

        if ($cartItems->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Cart is empty',
            ], 422);
        }

        // Validate stock availability
        foreach ($cartItems as $item) {
            if ($item->product->manage_stock && $item->product->stock_quantity < $item->quantity) {
                return response()->json([
                    'success' => false,
                    'message' => "Insufficient stock for {$item->product->name}",
                    'product_id' => $item->product_id,
                    'available_stock' => $item->product->stock_quantity,
                ], 422);
            }
        }

        DB::transaction(function () use ($request, $user, $cartItems, &$order) {
            // Calculate totals
            $subtotal = $cartItems->sum('total_price');
            $discountAmount = 0;
            $coupon = null;

            // Apply coupon if provided
            if ($request->coupon_code) {
                $coupon = Coupon::where('code', $request->coupon_code)->first();
                if ($coupon && $coupon->isValid()) {
                    $discountAmount = $coupon->calculateDiscount($subtotal);
                    $coupon->increment('used_count');
                }
            }

            $shippingCost = 0; // You can implement shipping calculation here
            $taxAmount = 0; // You can implement tax calculation here
            $totalAmount = $subtotal - $discountAmount + $shippingCost + $taxAmount;

            // Check wallet balance if payment method is wallet
            if ($request->payment_method === 'wallet') {
                $wallet = $user->getOrCreateWallet();
                if (!$wallet->canAfford($totalAmount)) {
                    throw new \Exception('Insufficient wallet balance');
                }
            }

            // Create order
            $order = Order::create([
                'user_id' => $user->id,
                'status' => 'pending',
                'subtotal' => $subtotal,
                'discount_amount' => $discountAmount,
                'shipping_cost' => $shippingCost,
                'tax_amount' => $taxAmount,
                'total_amount' => $totalAmount,
                'payment_method' => $request->payment_method,
                'payment_status' => $request->payment_method === 'wallet' ? 'paid' : 'pending',
                'shipping_address' => $request->shipping_address,
                'billing_address' => $request->billing_address,
                'coupon_code' => $request->coupon_code,
                'notes' => $request->notes,
            ]);

            // Create order items
            foreach ($cartItems as $cartItem) {
                $order->items()->create([
                    'product_id' => $cartItem->product_id,
                    'product_name' => $cartItem->product->name,
                    'product_sku' => $cartItem->product->sku,
                    'unit_price' => $cartItem->product->final_price,
                    'quantity' => $cartItem->quantity,
                    'total_price' => $cartItem->total_price,
                    'product_attributes' => $cartItem->selected_attributes,
                ]);

                // Update product stock
                if ($cartItem->product->manage_stock) {
                    $cartItem->product->decrement('stock_quantity', $cartItem->quantity);
                    if ($cartItem->product->stock_quantity <= 0) {
                        $cartItem->product->update(['in_stock' => false]);
                    }
                }
            }

            // Process wallet payment
            if ($request->payment_method === 'wallet') {
                $wallet = $user->getOrCreateWallet();
                $wallet->debit(
                    $totalAmount,
                    'purchase',
                    "Payment for order #{$order->order_number}",
                    $order->id
                );
            }

            // Clear cart
            CartItem::where('user_id', $user->id)->delete();
        });

        return response()->json([
            'success' => true,
            'message' => 'Order created successfully',
            'data' => $order->load(['items.product', 'user']),
        ], 201);
    }

    public function show(Order $order, Request $request)
    {
        if ($order->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => $order->load(['items.product.images', 'reviews']),
        ]);
    }

    public function cancel(Order $order, Request $request)
    {
        if ($order->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        if (!in_array($order->status, ['pending', 'processing'])) {
            return response()->json([
                'success' => false,
                'message' => 'Order cannot be cancelled at this stage',
            ], 422);
        }

        DB::transaction(function () use ($order) {
            // Restore product stock
            foreach ($order->items as $item) {
                if ($item->product->manage_stock) {
                    $item->product->increment('stock_quantity', $item->quantity);
                    $item->product->update(['in_stock' => true]);
                }
            }

            // Refund to wallet if paid
            if ($order->payment_status === 'paid' && $order->payment_method === 'wallet') {
                $wallet = $order->user->getOrCreateWallet();
                $wallet->credit(
                    $order->total_amount,
                    'refund',
                    "Refund for cancelled order #{$order->order_number}",
                    $order->id
                );
            }

            $order->update([
                'status' => 'cancelled',
                'payment_status' => 'refunded',
            ]);
        });

        return response()->json([
            'success' => true,
            'message' => 'Order cancelled successfully',
            'data' => $order,
        ]);
    }

    public function validateCoupon(Request $request)
    {
        $request->validate([
            'coupon_code' => 'required|string',
            'order_amount' => 'required|numeric|min:0',
        ]);

        $coupon = Coupon::where('code', $request->coupon_code)->first();

        if (!$coupon) {
            return response()->json([
                'success' => false,
                'message' => 'Coupon not found',
            ], 404);
        }

        if (!$coupon->isValid()) {
            return response()->json([
                'success' => false,
                'message' => 'Coupon is not valid or has expired',
            ], 422);
        }

        $discountAmount = $coupon->calculateDiscount($request->order_amount);

        if ($discountAmount <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Coupon does not apply to this order amount',
            ], 422);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'valid' => true,
                'discount_amount' => $discountAmount,
                'coupon' => [
                    'code' => $coupon->code,
                    'name' => $coupon->name,
                    'type' => $coupon->type,
                    'value' => $coupon->value,
                ],
            ],
        ]);
    }
}
