<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['category', 'images', 'reviews'])
                       ->where('status', 'active')
                       ->where('in_stock', true);

        // Search functionality
        if ($request->has('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%')
                  ->orWhere('short_description', 'like', '%' . $request->search . '%');
            });
        }

        // Category filter
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Price range filter
        if ($request->has('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }

        if ($request->has('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        // Featured products filter
        if ($request->has('featured') && $request->featured) {
            $query->where('is_featured', true);
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');

        switch ($sortBy) {
            case 'price':
                $query->orderBy('price', $sortOrder);
                break;
            case 'name':
                $query->orderBy('name', $sortOrder);
                break;
            case 'rating':
                $query->withAvg('reviews', 'rating')->orderBy('reviews_avg_rating', $sortOrder);
                break;
            default:
                $query->orderBy('created_at', $sortOrder);
        }

        $perPage = min($request->get('per_page', 15), 50); // Max 50 items per page
        $products = $query->paginate($perPage);

        // Add computed attributes
        $products->getCollection()->transform(function ($product) {
            $product->primary_image_url = $product->primary_image ? $product->primary_image->image_url : null;
            $product->average_rating = $product->average_rating;
            $product->reviews_count = $product->reviews_count;
            $product->discount_percentage = $product->discount_percentage;
            $product->final_price = $product->final_price;
            return $product;
        });

        return response()->json([
            'success' => true,
            'data' => $products,
        ]);
    }

    public function show($id)
    {
        $product = Product::with(['category', 'images', 'attributes', 'tags', 'reviews.user'])
                         ->where('status', 'active')
                         ->findOrFail($id);

        // Add computed attributes
        $product->primary_image_url = $product->primary_image ? $product->primary_image->image_url : null;
        $product->average_rating = $product->average_rating;
        $product->reviews_count = $product->reviews_count;
        $product->discount_percentage = $product->discount_percentage;
        $product->final_price = $product->final_price;

        // Get related products from same category
        $relatedProducts = Product::with(['images'])
                                ->where('category_id', $product->category_id)
                                ->where('id', '!=', $product->id)
                                ->where('status', 'active')
                                ->where('in_stock', true)
                                ->limit(8)
                                ->get()
                                ->transform(function ($item) {
                                    $item->primary_image_url = $item->primary_image ? $item->primary_image->image_url : null;
                                    $item->final_price = $item->final_price;
                                    return $item;
                                });

        return response()->json([
            'success' => true,
            'data' => [
                'product' => $product,
                'related_products' => $relatedProducts,
            ],
        ]);
    }

    public function featured()
    {
        $products = Product::with(['category', 'images'])
                          ->where('status', 'active')
                          ->where('is_featured', true)
                          ->where('in_stock', true)
                          ->limit(10)
                          ->get()
                          ->transform(function ($product) {
                              $product->primary_image_url = $product->primary_image ? $product->primary_image->image_url : null;
                              $product->final_price = $product->final_price;
                              $product->discount_percentage = $product->discount_percentage;
                              return $product;
                          });

        return response()->json([
            'success' => true,
            'data' => $products,
        ]);
    }

    public function newProducts()
    {
        $products = Product::with(['category', 'images'])
                          ->where('status', 'active')
                          ->where('in_stock', true)
                          ->orderBy('created_at', 'desc')
                          ->limit(10)
                          ->get()
                          ->transform(function ($product) {
                              $product->primary_image_url = $product->primary_image ? $product->primary_image->image_url : null;
                              $product->final_price = $product->final_price;
                              $product->discount_percentage = $product->discount_percentage;
                              return $product;
                          });

        return response()->json([
            'success' => true,
            'data' => $products,
        ]);
    }

    public function bestSelling()
    {
        $products = Product::with(['category', 'images'])
                          ->where('status', 'active')
                          ->where('in_stock', true)
                          ->withCount('orderItems')
                          ->orderBy('order_items_count', 'desc')
                          ->limit(10)
                          ->get()
                          ->transform(function ($product) {
                              $product->primary_image_url = $product->primary_image ? $product->primary_image->image_url : null;
                              $product->final_price = $product->final_price;
                              $product->discount_percentage = $product->discount_percentage;
                              return $product;
                          });

        return response()->json([
            'success' => true,
            'data' => $products,
        ]);
    }

    public function categories()
    {
        $categories = Category::with(['children'])
                            ->whereNull('parent_id')
                            ->where('is_active', true)
                            ->orderBy('sort_order')
                            ->get()
                            ->transform(function ($category) {
                                $category->image_url = $category->image_url;
                                $category->products_count = $category->products()->where('status', 'active')->count();
                                return $category;
                            });

        return response()->json([
            'success' => true,
            'data' => $categories,
        ]);
    }

    public function categoryProducts($categoryId, Request $request)
    {
        $category = Category::where('is_active', true)->findOrFail($categoryId);

        $query = Product::with(['category', 'images'])
                       ->where('category_id', $categoryId)
                       ->where('status', 'active')
                       ->where('in_stock', true);

        // Price range filter
        if ($request->has('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }

        if ($request->has('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');

        switch ($sortBy) {
            case 'price':
                $query->orderBy('price', $sortOrder);
                break;
            case 'name':
                $query->orderBy('name', $sortOrder);
                break;
            case 'rating':
                $query->withAvg('reviews', 'rating')->orderBy('reviews_avg_rating', $sortOrder);
                break;
            default:
                $query->orderBy('created_at', $sortOrder);
        }

        $perPage = min($request->get('per_page', 15), 50);
        $products = $query->paginate($perPage);

        $products->getCollection()->transform(function ($product) {
            $product->primary_image_url = $product->primary_image ? $product->primary_image->image_url : null;
            $product->final_price = $product->final_price;
            $product->discount_percentage = $product->discount_percentage;
            return $product;
        });

        return response()->json([
            'success' => true,
            'data' => [
                'category' => $category,
                'products' => $products,
            ],
        ]);
    }

    public function search(Request $request)
    {
        $request->validate([
            'q' => 'required|string|min:2',
        ]);

        $query = Product::with(['category', 'images'])
                       ->where('status', 'active')
                       ->where('in_stock', true)
                       ->where(function ($q) use ($request) {
                           $q->where('name', 'like', '%' . $request->q . '%')
                             ->orWhere('description', 'like', '%' . $request->q . '%')
                             ->orWhere('short_description', 'like', '%' . $request->q . '%')
                             ->orWhere('sku', 'like', '%' . $request->q . '%');
                       });

        $perPage = min($request->get('per_page', 15), 50);
        $products = $query->paginate($perPage);

        $products->getCollection()->transform(function ($product) {
            $product->primary_image_url = $product->primary_image ? $product->primary_image->image_url : null;
            $product->final_price = $product->final_price;
            $product->discount_percentage = $product->discount_percentage;
            return $product;
        });

        return response()->json([
            'success' => true,
            'data' => $products,
        ]);
    }
}
