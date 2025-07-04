<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductAttribute;
use App\Models\Tag;
use App\Models\Coupon;
use App\Models\Wallet;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CryptoEcommerceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Admin User
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@crypto4.com',
            'phone' => '+1234567890',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        // Create Customer User
        $customer = User::create([
            'name' => 'John Doe',
            'email' => 'customer@crypto4.com',
            'phone' => '+1234567891',
            'password' => Hash::make('password123'),
            'role' => 'customer',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        // Create Wallet for customer
        Wallet::create([
            'user_id' => $customer->id,
            'balance' => 5000.00,
            'is_active' => true,
        ]);

        // Create Categories
        $miningCategory = Category::create([
            'name' => 'Mining Equipment',
            'slug' => 'mining-equipment',
            'description' => 'Professional cryptocurrency mining hardware and equipment',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        $asicCategory = Category::create([
            'name' => 'ASIC Miners',
            'slug' => 'asic-miners',
            'description' => 'Application-Specific Integrated Circuit miners for Bitcoin and other cryptocurrencies',
            'parent_id' => $miningCategory->id,
            'sort_order' => 1,
            'is_active' => true,
        ]);

        $gpuCategory = Category::create([
            'name' => 'GPU Miners',
            'slug' => 'gpu-miners',
            'description' => 'Graphics Processing Unit miners for Ethereum and other altcoins',
            'parent_id' => $miningCategory->id,
            'sort_order' => 2,
            'is_active' => true,
        ]);

        $hardwareCategory = Category::create([
            'name' => 'Hardware Wallets',
            'slug' => 'hardware-wallets',
            'description' => 'Secure hardware wallets for cryptocurrency storage',
            'sort_order' => 2,
            'is_active' => true,
        ]);

        $accessoriesCategory = Category::create([
            'name' => 'Mining Accessories',
            'slug' => 'mining-accessories',
            'description' => 'Cooling systems, power supplies, and other mining accessories',
            'sort_order' => 3,
            'is_active' => true,
        ]);

        // Create Tags
        $tags = [
            ['name' => 'Bitcoin', 'slug' => 'bitcoin', 'color' => '#f7931a'],
            ['name' => 'Ethereum', 'slug' => 'ethereum', 'color' => '#627eea'],
            ['name' => 'High Performance', 'slug' => 'high-performance', 'color' => '#28a745'],
            ['name' => 'Energy Efficient', 'slug' => 'energy-efficient', 'color' => '#17a2b8'],
            ['name' => 'Professional', 'slug' => 'professional', 'color' => '#6f42c1'],
            ['name' => 'Beginner Friendly', 'slug' => 'beginner-friendly', 'color' => '#fd7e14'],
        ];

        foreach ($tags as $tagData) {
            Tag::create($tagData);
        }

        // Create Products
        $products = [
            [
                'name' => 'Antminer S19 Pro',
                'slug' => 'antminer-s19-pro',
                'description' => 'The Antminer S19 Pro is the latest Bitcoin mining hardware from Bitmain. With a hash rate of 110 TH/s and power consumption of 3250W, it offers excellent mining efficiency for professional miners.',
                'short_description' => 'Professional Bitcoin miner with 110 TH/s hash rate',
                'sku' => 'ANT-S19-PRO',
                'price' => 8999.00,
                'sale_price' => 7999.00,
                'stock_quantity' => 15,
                'weight' => 15.5,
                'dimensions' => '400x195x290mm',
                'category_id' => $asicCategory->id,
                'is_featured' => true,
                'status' => 'active',
                'manage_stock' => true,
                'in_stock' => true,
            ],
            [
                'name' => 'Nvidia RTX 4090 Mining Rig',
                'slug' => 'nvidia-rtx-4090-mining-rig',
                'description' => 'Complete 8-GPU mining rig featuring NVIDIA RTX 4090 graphics cards. Perfect for Ethereum and other altcoin mining with exceptional hash rates and efficiency.',
                'short_description' => '8x RTX 4090 GPU mining rig for maximum performance',
                'sku' => 'RTX-4090-RIG',
                'price' => 25999.00,
                'sale_price' => null,
                'stock_quantity' => 5,
                'weight' => 25.0,
                'dimensions' => '600x400x300mm',
                'category_id' => $gpuCategory->id,
                'is_featured' => true,
                'status' => 'active',
                'manage_stock' => true,
                'in_stock' => true,
            ],
            [
                'name' => 'Ledger Nano X',
                'slug' => 'ledger-nano-x',
                'description' => 'The Ledger Nano X is a secure hardware wallet that stores your private keys offline. It supports over 1,800 cryptocurrencies and features Bluetooth connectivity for mobile use.',
                'short_description' => 'Secure hardware wallet with Bluetooth connectivity',
                'sku' => 'LEDGER-NANO-X',
                'price' => 149.00,
                'sale_price' => 129.00,
                'stock_quantity' => 50,
                'weight' => 0.034,
                'dimensions' => '72x18.6x11.75mm',
                'category_id' => $hardwareCategory->id,
                'is_featured' => false,
                'status' => 'active',
                'manage_stock' => true,
                'in_stock' => true,
            ],
            [
                'name' => 'Antminer T19',
                'slug' => 'antminer-t19',
                'description' => 'The Antminer T19 delivers 84 TH/s of Bitcoin mining power with improved energy efficiency. Ideal for both home and commercial mining operations.',
                'short_description' => 'Efficient Bitcoin miner with 84 TH/s hash rate',
                'sku' => 'ANT-T19',
                'price' => 5999.00,
                'sale_price' => null,
                'stock_quantity' => 8,
                'weight' => 13.2,
                'dimensions' => '370x195x290mm',
                'category_id' => $asicCategory->id,
                'is_featured' => false,
                'status' => 'active',
                'manage_stock' => true,
                'in_stock' => true,
            ],
            [
                'name' => 'Trezor Model T',
                'slug' => 'trezor-model-t',
                'description' => 'The Trezor Model T is the next-generation hardware wallet with a color touchscreen. It provides ultimate security for your cryptocurrency investments.',
                'short_description' => 'Next-gen hardware wallet with color touchscreen',
                'sku' => 'TREZOR-MODEL-T',
                'price' => 219.00,
                'sale_price' => null,
                'stock_quantity' => 30,
                'weight' => 0.022,
                'dimensions' => '64x39x10mm',
                'category_id' => $hardwareCategory->id,
                'is_featured' => true,
                'status' => 'active',
                'manage_stock' => true,
                'in_stock' => true,
            ],
            [
                'name' => 'Mining Cooling Fan System',
                'slug' => 'mining-cooling-fan-system',
                'description' => 'Professional cooling system designed for cryptocurrency mining rigs. Includes 6 high-performance fans with temperature monitoring.',
                'short_description' => 'Professional cooling system for mining rigs',
                'sku' => 'COOL-FAN-SYS',
                'price' => 299.00,
                'sale_price' => 249.00,
                'stock_quantity' => 20,
                'weight' => 3.5,
                'dimensions' => '300x200x150mm',
                'category_id' => $accessoriesCategory->id,
                'is_featured' => false,
                'status' => 'active',
                'manage_stock' => true,
                'in_stock' => true,
            ],
        ];

        foreach ($products as $productData) {
            Product::create($productData);
        }

        // Create Product Attributes
        $product1 = Product::where('sku', 'ANT-S19-PRO')->first();
        ProductAttribute::create([
            'product_id' => $product1->id,
            'attribute_name' => 'Color',
            'attribute_value' => 'Black',
            'price_adjustment' => 0,
        ]);
        ProductAttribute::create([
            'product_id' => $product1->id,
            'attribute_name' => 'Warranty',
            'attribute_value' => '12 Months',
            'price_adjustment' => 0,
        ]);

        $product2 = Product::where('sku', 'RTX-4090-RIG')->first();
        ProductAttribute::create([
            'product_id' => $product2->id,
            'attribute_name' => 'GPU Count',
            'attribute_value' => '8',
            'price_adjustment' => 0,
        ]);
        ProductAttribute::create([
            'product_id' => $product2->id,
            'attribute_name' => 'Frame Material',
            'attribute_value' => 'Aluminum',
            'price_adjustment' => 0,
        ]);

        // Assign tags to products
        $bitcoinTag = Tag::where('slug', 'bitcoin')->first();
        $ethereumTag = Tag::where('slug', 'ethereum')->first();
        $highPerformanceTag = Tag::where('slug', 'high-performance')->first();
        $professionalTag = Tag::where('slug', 'professional')->first();

        $product1->tags()->attach([$bitcoinTag->id, $highPerformanceTag->id, $professionalTag->id]);
        $product2->tags()->attach([$ethereumTag->id, $highPerformanceTag->id, $professionalTag->id]);

        // Create Coupons
        Coupon::create([
            'code' => 'WELCOME10',
            'name' => 'Welcome Discount',
            'description' => '10% discount for new customers',
            'type' => 'percentage',
            'value' => 10.00,
            'minimum_amount' => 100.00,
            'maximum_discount' => 500.00,
            'usage_limit' => 100,
            'usage_limit_per_user' => 1,
            'is_active' => true,
            'starts_at' => now(),
            'expires_at' => now()->addMonths(3),
        ]);

        Coupon::create([
            'code' => 'SAVE50',
            'name' => 'Fixed Discount',
            'description' => '$50 off on orders over $500',
            'type' => 'fixed',
            'value' => 50.00,
            'minimum_amount' => 500.00,
            'usage_limit' => 50,
            'usage_limit_per_user' => 2,
            'is_active' => true,
            'starts_at' => now(),
            'expires_at' => now()->addMonths(6),
        ]);

        echo "✅ Crypto E-commerce data seeded successfully!\n";
        echo "👤 Admin: admin@crypto4.com (password: password123)\n";
        echo "🛒 Customer: customer@crypto4.com (password: password123)\n";
        echo "💳 Customer wallet balance: $5,000.00\n";
        echo "🏷️ Coupons: WELCOME10, SAVE50\n";
    }
}
