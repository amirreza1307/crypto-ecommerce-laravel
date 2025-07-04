<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductAttribute;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['category', 'images', 'attributes']);

        if ($request->has('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('sku', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('stock_status')) {
            if ($request->stock_status === 'in_stock') {
                $query->where('in_stock', true);
            } else {
                $query->where('in_stock', false);
            }
        }

        $products = $query->orderBy('created_at', 'desc')->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $products,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'short_description' => 'nullable|string',
            'sku' => 'required|string|unique:products,sku',
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0|lt:price',
            'stock_quantity' => 'integer|min:0',
            'weight' => 'nullable|numeric|min:0',
            'dimensions' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'manage_stock' => 'boolean',
            'in_stock' => 'boolean',
            'is_featured' => 'boolean',
            'status' => 'in:active,inactive,draft',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'attributes' => 'nullable|array',
            'attributes.*.name' => 'required|string',
            'attributes.*.value' => 'required|string',
            'attributes.*.price_adjustment' => 'nullable|numeric',
            'attributes.*.stock_adjustment' => 'nullable|integer',
        ]);

        DB::transaction(function () use ($request, &$product) {
            $data = $request->only([
                'name', 'description', 'short_description', 'sku', 'price', 'sale_price',
                'stock_quantity', 'weight', 'dimensions', 'category_id', 'manage_stock',
                'in_stock', 'is_featured', 'status'
            ]);
            
            $data['slug'] = Str::slug($request->name);

            $product = Product::create($data);

            // Handle images
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $index => $image) {
                    $imagePath = $image->store('products', 'public');
                    ProductImage::create([
                        'product_id' => $product->id,
                        'image_path' => $imagePath,
                        'is_primary' => $index === 0,
                        'sort_order' => $index,
                    ]);
                }
            }

            // Handle attributes
            if ($request->has('attributes')) {
                foreach ($request->attributes as $attribute) {
                    ProductAttribute::create([
                        'product_id' => $product->id,
                        'attribute_name' => $attribute['name'],
                        'attribute_value' => $attribute['value'],
                        'price_adjustment' => $attribute['price_adjustment'] ?? 0,
                        'stock_adjustment' => $attribute['stock_adjustment'] ?? 0,
                    ]);
                }
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Product created successfully',
            'data' => $product->load(['category', 'images', 'attributes']),
        ], 201);
    }

    public function show(Product $product)
    {
        return response()->json([
            'success' => true,
            'data' => $product->load(['category', 'images', 'attributes', 'tags', 'reviews.user']),
        ]);
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'short_description' => 'nullable|string',
            'sku' => 'required|string|unique:products,sku,' . $product->id,
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0|lt:price',
            'stock_quantity' => 'integer|min:0',
            'weight' => 'nullable|numeric|min:0',
            'dimensions' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'manage_stock' => 'boolean',
            'in_stock' => 'boolean',
            'is_featured' => 'boolean',
            'status' => 'in:active,inactive,draft',
            'new_images' => 'nullable|array',
            'new_images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'remove_images' => 'nullable|array',
            'remove_images.*' => 'exists:product_images,id',
            'attributes' => 'nullable|array',
            'attributes.*.name' => 'required|string',
            'attributes.*.value' => 'required|string',
            'attributes.*.price_adjustment' => 'nullable|numeric',
            'attributes.*.stock_adjustment' => 'nullable|integer',
        ]);

        DB::transaction(function () use ($request, $product) {
            $data = $request->only([
                'name', 'description', 'short_description', 'sku', 'price', 'sale_price',
                'stock_quantity', 'weight', 'dimensions', 'category_id', 'manage_stock',
                'in_stock', 'is_featured', 'status'
            ]);
            
            $data['slug'] = Str::slug($request->name);

            $product->update($data);

            // Remove selected images
            if ($request->has('remove_images')) {
                $imagesToRemove = ProductImage::whereIn('id', $request->remove_images)->get();
                foreach ($imagesToRemove as $image) {
                    \Storage::disk('public')->delete($image->image_path);
                    $image->delete();
                }
            }

            // Add new images
            if ($request->hasFile('new_images')) {
                $currentImagesCount = $product->images()->count();
                foreach ($request->file('new_images') as $index => $image) {
                    $imagePath = $image->store('products', 'public');
                    ProductImage::create([
                        'product_id' => $product->id,
                        'image_path' => $imagePath,
                        'is_primary' => $currentImagesCount === 0 && $index === 0,
                        'sort_order' => $currentImagesCount + $index,
                    ]);
                }
            }

            // Update attributes
            if ($request->has('attributes')) {
                $product->attributes()->delete();
                foreach ($request->attributes as $attribute) {
                    ProductAttribute::create([
                        'product_id' => $product->id,
                        'attribute_name' => $attribute['name'],
                        'attribute_value' => $attribute['value'],
                        'price_adjustment' => $attribute['price_adjustment'] ?? 0,
                        'stock_adjustment' => $attribute['stock_adjustment'] ?? 0,
                    ]);
                }
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Product updated successfully',
            'data' => $product->fresh()->load(['category', 'images', 'attributes']),
        ]);
    }

    public function destroy(Product $product)
    {
        DB::transaction(function () use ($product) {
            // Delete all product images
            foreach ($product->images as $image) {
                \Storage::disk('public')->delete($image->image_path);
            }

            $product->delete(); // Soft delete
        });

        return response()->json([
            'success' => true,
            'message' => 'Product deleted successfully',
        ]);
    }

    public function restore($id)
    {
        $product = Product::withTrashed()->findOrFail($id);
        $product->restore();

        return response()->json([
            'success' => true,
            'message' => 'Product restored successfully',
            'data' => $product->load(['category', 'images', 'attributes']),
        ]);
    }

    public function forceDelete($id)
    {
        $product = Product::withTrashed()->findOrFail($id);
        
        // Delete all product images
        foreach ($product->images as $image) {
            \Storage::disk('public')->delete($image->image_path);
        }

        $product->forceDelete();

        return response()->json([
            'success' => true,
            'message' => 'Product permanently deleted',
        ]);
    }

    public function updateStock(Request $request, Product $product)
    {
        $request->validate([
            'stock_quantity' => 'required|integer|min:0',
            'in_stock' => 'boolean',
        ]);

        $product->update([
            'stock_quantity' => $request->stock_quantity,
            'in_stock' => $request->in_stock ?? ($request->stock_quantity > 0),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Stock updated successfully',
            'data' => $product,
        ]);
    }
}
