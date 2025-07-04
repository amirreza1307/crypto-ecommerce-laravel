<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with(['user', 'items.product']);

        if ($request->has('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('order_number', 'like', '%' . $request->search . '%')
                  ->orWhereHas('user', function ($userQuery) use ($request) {
                      $userQuery->where('name', 'like', '%' . $request->search . '%')
                               ->orWhere('email', 'like', '%' . $request->search . '%');
                  });
            });
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        if ($request->has('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $orders = $query->orderBy('created_at', 'desc')->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $orders,
        ]);
    }

    public function show(Order $order)
    {
        return response()->json([
            'success' => true,
            'data' => $order->load(['user', 'items.product.images', 'reviews']),
        ]);
    }

    public function update(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled,refunded',
            'notes' => 'nullable|string',
            'shipping_cost' => 'nullable|numeric|min:0',
        ]);

        $data = $request->only(['status', 'notes', 'shipping_cost']);

        // Set timestamps based on status
        if ($request->status === 'shipped' && $order->status !== 'shipped') {
            $data['shipped_at'] = now();
        }

        if ($request->status === 'delivered' && $order->status !== 'delivered') {
            $data['delivered_at'] = now();
        }

        // Recalculate total if shipping cost changed
        if (isset($data['shipping_cost'])) {
            $data['total_amount'] = $order->subtotal - $order->discount_amount + $data['shipping_cost'] + $order->tax_amount;
        }

        $order->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Order updated successfully',
            'data' => $order->load(['user', 'items.product']),
        ]);
    }

    public function updatePaymentStatus(Request $request, Order $order)
    {
        $request->validate([
            'payment_status' => 'required|in:pending,paid,failed,refunded',
            'transaction_id' => 'nullable|string',
        ]);

        $order->update([
            'payment_status' => $request->payment_status,
            'transaction_id' => $request->transaction_id,
        ]);

        // If refunded, add money back to user's wallet
        if ($request->payment_status === 'refunded' && $order->payment_method === 'wallet') {
            $wallet = $order->user->getOrCreateWallet();
            $wallet->credit(
                $order->total_amount,
                'refund',
                "Refund for order #{$order->order_number}",
                $order->id
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'Payment status updated successfully',
            'data' => $order,
        ]);
    }

    public function getStatistics(Request $request)
    {
        $dateFrom = $request->date_from ? Carbon::parse($request->date_from) : Carbon::now()->startOfMonth();
        $dateTo = $request->date_to ? Carbon::parse($request->date_to) : Carbon::now()->endOfMonth();

        $totalOrders = Order::whereBetween('created_at', [$dateFrom, $dateTo])->count();
        $totalRevenue = Order::whereBetween('created_at', [$dateFrom, $dateTo])
                           ->where('payment_status', 'paid')
                           ->sum('total_amount');

        $ordersByStatus = Order::whereBetween('created_at', [$dateFrom, $dateTo])
                              ->selectRaw('status, COUNT(*) as count')
                              ->groupBy('status')
                              ->pluck('count', 'status');

        $dailySales = Order::whereBetween('created_at', [$dateFrom, $dateTo])
                          ->where('payment_status', 'paid')
                          ->selectRaw('DATE(created_at) as date, SUM(total_amount) as total')
                          ->groupBy('date')
                          ->orderBy('date')
                          ->get();

        $topProducts = Order::whereBetween('created_at', [$dateFrom, $dateTo])
                           ->join('order_items', 'orders.id', '=', 'order_items.order_id')
                           ->join('products', 'order_items.product_id', '=', 'products.id')
                           ->selectRaw('products.name, SUM(order_items.quantity) as total_sold')
                           ->groupBy('products.id', 'products.name')
                           ->orderByDesc('total_sold')
                           ->limit(10)
                           ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'total_orders' => $totalOrders,
                'total_revenue' => $totalRevenue,
                'orders_by_status' => $ordersByStatus,
                'daily_sales' => $dailySales,
                'top_products' => $topProducts,
            ],
        ]);
    }

    public function exportOrders(Request $request)
    {
        $query = Order::with(['user', 'items.product']);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $orders = $query->get();

        $csvData = [];
        $csvData[] = [
            'Order Number', 'Customer Name', 'Customer Email', 'Status', 
            'Payment Status', 'Total Amount', 'Created At'
        ];

        foreach ($orders as $order) {
            $csvData[] = [
                $order->order_number,
                $order->user->name,
                $order->user->email,
                $order->status,
                $order->payment_status,
                $order->total_amount,
                $order->created_at->format('Y-m-d H:i:s'),
            ];
        }

        $filename = 'orders_' . date('Y_m_d_H_i_s') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($csvData) {
            $file = fopen('php://output', 'w');
            foreach ($csvData as $row) {
                fputcsv($file, $row);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
