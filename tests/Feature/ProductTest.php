<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create a test category
        $this->category = Category::factory()->create([
            'name' => 'Bitcoin',
            'slug' => 'bitcoin',
            'is_active' => true,
        ]);
    }

    public function test_can_get_products_list(): void
    {
        Product::factory()->count(5)->create([
            'category_id' => $this->category->id,
            'status' => 'active',
            'in_stock' => true,
        ]);

        $response = $this->getJson('/api/v1/products');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'data' => [
                        'data' => [
                            '*' => [
                                'id',
                                'name',
                                'slug',
                                'description',
                                'price',
                                'sale_price',
                                'stock_quantity',
                                'status',
                                'category',
                                'images',
                            ]
                        ],
                        'current_page',
                        'total',
                        'per_page',
                    ]
                ]);
    }

    public function test_can_get_single_product(): void
    {
        $product = Product::factory()->create([
            'category_id' => $this->category->id,
            'status' => 'active',
            'in_stock' => true,
        ]);

        $response = $this->getJson("/api/v1/products/{$product->id}");

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'data' => [
                        'product' => [
                            'id',
                            'name',
                            'slug',
                            'description',
                            'price',
                            'sale_price',
                            'stock_quantity',
                            'status',
                            'category',
                            'images',
                            'attributes',
                            'tags',
                            'reviews',
                        ],
                        'related_products'
                    ]
                ]);
    }

    public function test_can_search_products(): void
    {
        // Clear existing products first
        Product::query()->delete();
        
        Product::factory()->create([
            'name' => 'Bitcoin BTC',
            'category_id' => $this->category->id,
            'status' => 'active',
            'in_stock' => true,
        ]);

        Product::factory()->create([
            'name' => 'Ethereum ETH',
            'category_id' => $this->category->id,
            'status' => 'active',
            'in_stock' => true,
        ]);

        $response = $this->getJson('/api/v1/products?search=Bitcoin');

        $response->assertStatus(200);
        $products = $response->json('data.data');
        
        $this->assertCount(1, $products);
        $this->assertStringContainsString('Bitcoin', $products[0]['name']);
    }

    public function test_can_filter_products_by_category(): void
    {
        // Clear existing products first
        Product::query()->delete();
        
        $otherCategory = Category::factory()->create([
            'name' => 'Ethereum',
            'slug' => 'ethereum',
        ]);

        Product::factory()->create([
            'category_id' => $this->category->id,
            'status' => 'active',
            'in_stock' => true,
        ]);

        Product::factory()->create([
            'category_id' => $otherCategory->id,
            'status' => 'active',
            'in_stock' => true,
        ]);

        $response = $this->getJson("/api/v1/products?category_id={$this->category->id}");

        $response->assertStatus(200);
        $products = $response->json('data.data');
        
        $this->assertCount(1, $products);
        $this->assertEquals($this->category->id, $products[0]['category']['id']);
    }

    public function test_admin_can_create_product(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $token = $admin->createToken('test-token')->plainTextToken;

        $productData = [
            'name' => 'New Bitcoin Package',
            'description' => 'Premium Bitcoin package',
            'price' => 50000,
            'sale_price' => 45000,
            'stock_quantity' => 100,
            'category_id' => $this->category->id,
            'sku' => 'BTC123456',
            'status' => 'active',
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/admin/products', $productData);

        $response->assertStatus(201)
                ->assertJsonStructure([
                    'success',
                    'message',
                    'data' => [
                        'id',
                        'name',
                        'description',
                        'price',
                        'category_id',
                    ]
                ]);

        $this->assertDatabaseHas('products', [
            'name' => 'New Bitcoin Package',
            'price' => 50000,
        ]);
    }

    public function test_customer_cannot_create_product(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $token = $customer->createToken('test-token')->plainTextToken;

        $productData = [
            'name' => 'New Bitcoin Package',
            'description' => 'Premium Bitcoin package',
            'price' => 50000,
            'category_id' => $this->category->id,
            'sku' => 'BTC123456',
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/admin/products', $productData);

        $response->assertStatus(403);
    }
}
