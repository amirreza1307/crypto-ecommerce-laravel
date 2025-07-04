# Crypto E-commerce API Documentation

## Overview
This document provides comprehensive API documentation for the Crypto E-commerce Laravel backend. The application is a single-vendor crypto trading platform with complete admin panel and customer functionality.

## Base URL
```
http://your-domain.com/api/v1
```

## Authentication
The API uses Laravel Sanctum for authentication. Include the Bearer token in the Authorization header for protected endpoints.

```
Authorization: Bearer your_token_here
```

## Response Format
All API responses follow this format:

```json
{
    "success": true|false,
    "message": "Response message",
    "data": "Response data or null"
}
```

## Error Handling
Error responses include appropriate HTTP status codes and error messages:

```json
{
    "success": false,
    "message": "Error description",
    "errors": {
        "field": ["Error details"]
    }
}
```

---

## 🔐 Authentication Endpoints

### Register
Create a new customer account.

**POST** `/register`

**Request Body:**
```json
{
    "name": "John Doe",
    "email": "john@example.com",
    "phone": "+1234567890",
    "password": "password123",
    "password_confirmation": "password123"
}
```

**Response:**
```json
{
    "success": true,
    "message": "User registered successfully",
    "data": {
        "user": {
            "id": 1,
            "name": "John Doe",
            "email": "john@example.com",
            "phone": "+1234567890",
            "role": "customer",
            "is_active": true,
            "created_at": "2025-07-03T19:00:00.000000Z"
        },
        "token": "1|abc123...",
        "token_type": "Bearer"
    }
}
```

### Login
Authenticate existing user.

**POST** `/login`

**Request Body:**
```json
{
    "email": "john@example.com",
    "password": "password123"
}
```

**Response:**
```json
{
    "success": true,
    "message": "Login successful",
    "data": {
        "user": {
            "id": 1,
            "name": "John Doe",
            "email": "john@example.com",
            "role": "customer",
            "wallet": {
                "balance": "1000.00"
            }
        },
        "token": "1|abc123...",
        "token_type": "Bearer"
    }
}
```

### Logout
Logout current user (requires authentication).

**POST** `/logout`

**Headers:** `Authorization: Bearer token`

**Response:**
```json
{
    "success": true,
    "message": "Logged out successfully"
}
```

### Get Profile
Get current user profile (requires authentication).

**GET** `/profile`

**Headers:** `Authorization: Bearer token`

**Response:**
```json
{
    "success": true,
    "data": {
        "id": 1,
        "name": "John Doe",
        "email": "john@example.com",
        "phone": "+1234567890",
        "wallet": {
            "balance": "1000.00"
        }
    }
}
```

### Update Profile
Update user profile (requires authentication).

**PUT** `/profile`

**Headers:** `Authorization: Bearer token`

**Request Body:**
```json
{
    "name": "John Updated",
    "email": "john.updated@example.com",
    "phone": "+1234567891"
}
```

### Change Password
Change user password (requires authentication).

**PUT** `/change-password`

**Headers:** `Authorization: Bearer token`

**Request Body:**
```json
{
    "current_password": "oldpassword",
    "new_password": "newpassword123",
    "new_password_confirmation": "newpassword123"
}
```

---

## 📦 Product Endpoints

### Get All Products
Get paginated list of products with filters.

**GET** `/products`

**Query Parameters:**
- `search` (string): Search in name and description
- `category_id` (integer): Filter by category
- `min_price` (decimal): Minimum price filter
- `max_price` (decimal): Maximum price filter
- `featured` (boolean): Filter featured products
- `sort_by` (string): Sort by price|name|rating|created_at
- `sort_order` (string): asc|desc
- `per_page` (integer): Items per page (max 50)
- `page` (integer): Page number

**Response:**
```json
{
    "success": true,
    "data": {
        "current_page": 1,
        "data": [
            {
                "id": 1,
                "name": "Bitcoin Mining Hardware",
                "slug": "bitcoin-mining-hardware",
                "short_description": "High-performance mining equipment",
                "price": "2500.00",
                "sale_price": "2000.00",
                "final_price": "2000.00",
                "discount_percentage": 20,
                "stock_quantity": 10,
                "is_featured": true,
                "status": "active",
                "primary_image_url": "http://domain.com/storage/products/image.jpg",
                "average_rating": 4.5,
                "reviews_count": 25,
                "category": {
                    "id": 1,
                    "name": "Mining Equipment"
                }
            }
        ],
        "per_page": 15,
        "total": 100
    }
}
```

### Get Single Product
Get detailed product information.

**GET** `/products/{id}`

**Response:**
```json
{
    "success": true,
    "data": {
        "product": {
            "id": 1,
            "name": "Bitcoin Mining Hardware",
            "description": "Complete product description...",
            "price": "2500.00",
            "sale_price": "2000.00",
            "final_price": "2000.00",
            "discount_percentage": 20,
            "stock_quantity": 10,
            "weight": "5.50",
            "dimensions": "30x20x15",
            "images": [
                {
                    "id": 1,
                    "image_url": "http://domain.com/storage/products/image1.jpg",
                    "is_primary": true
                }
            ],
            "attributes": [
                {
                    "attribute_name": "Color",
                    "attribute_value": "Black",
                    "price_adjustment": "0.00"
                }
            ],
            "reviews": [
                {
                    "id": 1,
                    "rating": 5,
                    "title": "Great product",
                    "comment": "Excellent quality",
                    "user": {
                        "name": "Customer Name"
                    },
                    "created_at": "2025-07-03T19:00:00.000000Z"
                }
            ]
        },
        "related_products": [
            {
                "id": 2,
                "name": "Related Product",
                "final_price": "1500.00",
                "primary_image_url": "http://domain.com/storage/products/related.jpg"
            }
        ]
    }
}
```

### Get Featured Products
Get list of featured products.

**GET** `/products/featured`

### Get New Products
Get list of newest products.

**GET** `/products/new`

### Get Best Selling Products
Get list of best selling products.

**GET** `/products/best-selling`

---

## 🏷️ Category Endpoints

### Get All Categories
Get list of all active categories with subcategories.

**GET** `/categories`

**Response:**
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "name": "Mining Equipment",
            "slug": "mining-equipment",
            "description": "Category description",
            "image_url": "http://domain.com/storage/categories/image.jpg",
            "products_count": 25,
            "children": [
                {
                    "id": 2,
                    "name": "ASIC Miners",
                    "slug": "asic-miners"
                }
            ]
        }
    ]
}
```

### Get Category Products
Get products for a specific category.

**GET** `/categories/{categoryId}/products`

**Query Parameters:** Same as product listing

---

## 🛒 Cart Endpoints (Requires Authentication)

### Get Cart
Get current user's cart items.

**GET** `/cart`

**Headers:** `Authorization: Bearer token`

**Response:**
```json
{
    "success": true,
    "data": {
        "items": [
            {
                "id": 1,
                "quantity": 2,
                "selected_attributes": {"color": "black"},
                "total_price": "4000.00",
                "product": {
                    "id": 1,
                    "name": "Bitcoin Mining Hardware",
                    "final_price": "2000.00",
                    "primary_image_url": "http://domain.com/storage/products/image.jpg"
                }
            }
        ],
        "summary": {
            "total_items": 2,
            "subtotal": "4000.00",
            "shipping_cost": "0.00",
            "total": "4000.00"
        }
    }
}
```

### Add to Cart
Add product to cart.

**POST** `/cart`

**Headers:** `Authorization: Bearer token`

**Request Body:**
```json
{
    "product_id": 1,
    "quantity": 2,
    "selected_attributes": {
        "color": "black",
        "size": "large"
    }
}
```

### Update Cart Item
Update cart item quantity or attributes.

**PUT** `/cart/{cartItemId}`

**Headers:** `Authorization: Bearer token`

**Request Body:**
```json
{
    "quantity": 3,
    "selected_attributes": {
        "color": "blue"
    }
}
```

### Remove from Cart
Remove item from cart.

**DELETE** `/cart/{cartItemId}`

**Headers:** `Authorization: Bearer token`

### Clear Cart
Remove all items from cart.

**DELETE** `/cart`

**Headers:** `Authorization: Bearer token`

### Get Cart Count
Get total number of items in cart.

**GET** `/cart/count`

**Headers:** `Authorization: Bearer token`

**Response:**
```json
{
    "success": true,
    "data": {
        "count": 5
    }
}
```

---

## 🛍️ Order Endpoints (Requires Authentication)

### Get Orders
Get user's order history.

**GET** `/orders`

**Headers:** `Authorization: Bearer token`

**Query Parameters:**
- `status` (string): Filter by order status
- `page` (integer): Page number

**Response:**
```json
{
    "success": true,
    "data": {
        "data": [
            {
                "id": 1,
                "order_number": "ORD-1720024800-1234",
                "status": "delivered",
                "payment_status": "paid",
                "total_amount": "4000.00",
                "items_count": 2,
                "created_at": "2025-07-03T19:00:00.000000Z"
            }
        ]
    }
}
```

### Create Order
Create new order from cart items.

**POST** `/orders`

**Headers:** `Authorization: Bearer token`

**Request Body:**
```json
{
    "shipping_address": {
        "name": "John Doe",
        "phone": "+1234567890",
        "street": "123 Main St",
        "city": "New York",
        "state": "NY",
        "postal_code": "10001",
        "country": "USA"
    },
    "billing_address": {
        "name": "John Doe",
        "phone": "+1234567890",
        "street": "123 Main St",
        "city": "New York",
        "state": "NY",
        "postal_code": "10001",
        "country": "USA"
    },
    "payment_method": "wallet",
    "coupon_code": "SAVE10",
    "notes": "Please handle with care"
}
```

### Get Order Details
Get detailed order information.

**GET** `/orders/{orderId}`

**Headers:** `Authorization: Bearer token`

**Response:**
```json
{
    "success": true,
    "data": {
        "id": 1,
        "order_number": "ORD-1720024800-1234",
        "status": "delivered",
        "payment_status": "paid",
        "subtotal": "4000.00",
        "discount_amount": "400.00",
        "shipping_cost": "50.00",
        "total_amount": "3650.00",
        "shipping_address": {
            "name": "John Doe",
            "street": "123 Main St"
        },
        "items": [
            {
                "id": 1,
                "product_name": "Bitcoin Mining Hardware",
                "quantity": 2,
                "unit_price": "2000.00",
                "total_price": "4000.00",
                "product": {
                    "id": 1,
                    "name": "Bitcoin Mining Hardware",
                    "images": [...]
                }
            }
        ],
        "created_at": "2025-07-03T19:00:00.000000Z"
    }
}
```

### Cancel Order
Cancel an order (only if status is pending or processing).

**POST** `/orders/{orderId}/cancel`

**Headers:** `Authorization: Bearer token`

### Validate Coupon
Validate coupon code before applying to order.

**POST** `/coupons/validate`

**Headers:** `Authorization: Bearer token`

**Request Body:**
```json
{
    "coupon_code": "SAVE10",
    "order_amount": "1000.00"
}
```

**Response:**
```json
{
    "success": true,
    "data": {
        "valid": true,
        "discount_amount": "100.00",
        "coupon": {
            "code": "SAVE10",
            "type": "percentage",
            "value": "10.00"
        }
    }
}
```

---

## 💰 Wallet Endpoints (Requires Authentication)

### Get Wallet
Get wallet balance and information.

**GET** `/wallet`

**Headers:** `Authorization: Bearer token`

**Response:**
```json
{
    "success": true,
    "data": {
        "id": 1,
        "balance": "1500.50",
        "is_active": true
    }
}
```

### Get Wallet Transactions
Get wallet transaction history.

**GET** `/wallet/transactions`

**Headers:** `Authorization: Bearer token`

**Query Parameters:**
- `type` (string): credit|debit
- `transaction_type` (string): Filter by transaction type
- `page` (integer): Page number

**Response:**
```json
{
    "success": true,
    "data": {
        "data": [
            {
                "id": 1,
                "type": "credit",
                "amount": "500.00",
                "balance_before": "1000.00",
                "balance_after": "1500.00",
                "transaction_type": "deposit",
                "description": "Bank transfer deposit",
                "created_at": "2025-07-03T19:00:00.000000Z"
            }
        ]
    }
}
```

### Charge Wallet
Add money to wallet (in real app, this would integrate with payment gateway).

**POST** `/wallet/charge`

**Headers:** `Authorization: Bearer token`

**Request Body:**
```json
{
    "amount": "500.00",
    "payment_method": "bank_transfer",
    "reference": "TXN123456"
}
```

---

## ⭐ Review Endpoints (Requires Authentication)

### Get User Reviews
Get current user's reviews.

**GET** `/reviews`

**Headers:** `Authorization: Bearer token`

### Create Review
Create product review (only for purchased products).

**POST** `/reviews`

**Headers:** `Authorization: Bearer token`

**Request Body:**
```json
{
    "product_id": 1,
    "order_id": 1,
    "rating": 5,
    "title": "Excellent product",
    "comment": "Very satisfied with the quality and performance."
}
```

### Update Review
Update existing review.

**PUT** `/reviews/{reviewId}`

**Headers:** `Authorization: Bearer token`

**Request Body:** Same as create

### Delete Review
Delete review.

**DELETE** `/reviews/{reviewId}`

**Headers:** `Authorization: Bearer token`

### Get Product Reviews
Get reviews for a specific product.

**GET** `/products/{productId}/reviews`

**Query Parameters:**
- `page` (integer): Page number
- `rating` (integer): Filter by rating (1-5)

---

## 🔍 Search Endpoint

### Search Products
Search for products across name, description, and SKU.

**GET** `/search`

**Query Parameters:**
- `q` (string, required): Search query (minimum 2 characters)
- `per_page` (integer): Items per page
- `page` (integer): Page number

---

## 📊 Error Codes

### HTTP Status Codes
- `200` - Success
- `201` - Created
- `400` - Bad Request
- `401` - Unauthorized
- `403` - Forbidden
- `404` - Not Found
- `422` - Validation Error
- `500` - Server Error

### Common Error Responses

**Validation Error (422):**
```json
{
    "success": false,
    "message": "The given data was invalid.",
    "errors": {
        "email": ["The email field is required."],
        "password": ["The password must be at least 8 characters."]
    }
}
```

**Unauthorized (401):**
```json
{
    "success": false,
    "message": "Unauthenticated."
}
```

**Forbidden (403):**
```json
{
    "success": false,
    "message": "This action is unauthorized."
}
```

**Not Found (404):**
```json
{
    "success": false,
    "message": "Resource not found."
}
```

---

## 🎯 Usage Examples for Flutter

### Setting up HTTP Client

```dart
import 'package:http/http.dart' as http;
import 'dart:convert';

class ApiService {
  static const String baseUrl = 'http://your-domain.com/api/v1';
  static String? authToken;

  static Map<String, String> get headers => {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
    if (authToken != null) 'Authorization': 'Bearer $authToken',
  };

  static Future<Map<String, dynamic>> get(String endpoint) async {
    final response = await http.get(
      Uri.parse('$baseUrl$endpoint'),
      headers: headers,
    );
    return json.decode(response.body);
  }

  static Future<Map<String, dynamic>> post(
    String endpoint, 
    Map<String, dynamic> data
  ) async {
    final response = await http.post(
      Uri.parse('$baseUrl$endpoint'),
      headers: headers,
      body: json.encode(data),
    );
    return json.decode(response.body);
  }
}
```

### Login Example

```dart
Future<bool> login(String email, String password) async {
  try {
    final response = await ApiService.post('/login', {
      'email': email,
      'password': password,
    });

    if (response['success']) {
      ApiService.authToken = response['data']['token'];
      // Save token to secure storage
      return true;
    }
    return false;
  } catch (e) {
    print('Login error: $e');
    return false;
  }
}
```

### Get Products Example

```dart
Future<List<Product>> getProducts({
  String? search,
  int? categoryId,
  int page = 1,
}) async {
  String query = '?page=$page';
  if (search != null) query += '&search=$search';
  if (categoryId != null) query += '&category_id=$categoryId';

  final response = await ApiService.get('/products$query');
  
  if (response['success']) {
    return (response['data']['data'] as List)
        .map((item) => Product.fromJson(item))
        .toList();
  }
  return [];
}
```

### Add to Cart Example

```dart
Future<bool> addToCart(int productId, int quantity) async {
  final response = await ApiService.post('/cart', {
    'product_id': productId,
    'quantity': quantity,
  });

  return response['success'] == true;
}
```

---

## �️ Admin Panel APIs (Requires Admin Authentication)

### Base URL
```
http://your-domain.com/api/v1/admin
```

**Note:** All admin endpoints require authentication with admin role.

**Headers:** `Authorization: Bearer admin_token`

---

## 📊 Dashboard & Statistics

### Get Dashboard
Get admin dashboard overview statistics.

**GET** `/admin/dashboard`

**Response:**
```json
{
    "success": true,
    "data": {
        "total_users": 150,
        "total_products": 75,
        "total_orders": 320,
        "total_revenue": "125000.00",
        "pending_orders": 8,
        "delivered_orders": 280,
        "active_users": 95,
        "low_stock_products": 12
    }
}
```

### Get Detailed Statistics
Get detailed statistics for reporting.

**GET** `/admin/stats`

**Query Parameters:**
- `period` (string): daily|weekly|monthly|yearly
- `start_date` (date): Start date
- `end_date` (date): End date

---

## 🏷️ Categories Management (Admin)

### Get All Categories
Get all categories including inactive ones.

**GET** `/admin/categories`

**Query Parameters:**
- `search` (string): Search in name
- `status` (string): active|inactive
- `parent_id` (integer): Filter by parent
- `page` (integer): Page number

### Create Category
**POST** `/admin/categories`

### Update Category
**PUT** `/admin/categories/{id}`

### Delete Category
**DELETE** `/admin/categories/{id}`

---

## 📦 Products Management (Admin)

### Get All Products
Get complete product list including deleted ones.

**GET** `/admin/products`

**Query Parameters:**
- `search` (string): Search in name, SKU
- `category_id` (integer): Filter by category
- `status` (string): active|inactive|draft
- `stock_status` (string): in_stock|low_stock|out_of_stock
- `featured` (boolean): Filter featured products
- `trashed` (boolean): Include deleted products
- `sort_by` (string): name|price|stock|created_at
- `sort_order` (string): asc|desc
- `page` (integer): Page number

### Create Product
**POST** `/admin/products`

### Update Product
**PUT** `/admin/products/{id}`

### Soft Delete Product
**DELETE** `/admin/products/{id}`

### Restore Product
**PATCH** `/admin/products/{id}/restore`

### Force Delete Product
**DELETE** `/admin/products/{id}/force-delete`

### Update Stock
**PATCH** `/admin/products/{id}/stock`

---

## 🛍️ Orders Management (Admin)

### Get All Orders
Get complete orders list with details.

**GET** `/admin/orders`

**Query Parameters:**
- `search` (string): Search in order number, customer name
- `status` (string): pending|processing|shipped|delivered|cancelled
- `payment_status` (string): pending|paid|failed|refunded
- `start_date` (date): Start date
- `end_date` (date): End date
- `customer_id` (integer): Filter by customer
- `min_amount` (decimal): Minimum order amount
- `max_amount` (decimal): Maximum order amount
- `sort_by` (string): created_at|total_amount|customer_name
- `sort_order` (string): asc|desc
- `page` (integer): Page number

### Get Order Details
**GET** `/admin/orders/{id}`

### Update Order
**PUT** `/admin/orders/{id}`

### Update Payment Status
**PATCH** `/admin/orders/{id}/payment-status`

### Get Order Statistics
**GET** `/admin/orders/statistics`

### Export Orders
**GET** `/admin/orders/export`

---

## 💰 Coupons Management (Admin)

### Get All Coupons
**GET** `/admin/coupons`

### Create Coupon
**POST** `/admin/coupons`

### Update Coupon
**PUT** `/admin/coupons/{id}`

### Delete Coupon
**DELETE** `/admin/coupons/{id}`

---

## 👥 Users Management (Admin)

### Get All Users
**GET** `/admin/users`

**Query Parameters:**
- `search` (string): Search in name, email, phone
- `role` (string): admin|customer
- `status` (string): active|inactive
- `registration_date_from` (date): Registration date from
- `registration_date_to` (date): Registration date to
- `sort_by` (string): name|email|created_at|total_orders
- `sort_order` (string): asc|desc
- `page` (integer): Page number

### Get User Details
**GET** `/admin/users/{id}`

### Create User
**POST** `/admin/users`

### Update User
**PUT** `/admin/users/{id}`

### Toggle User Status
**PATCH** `/admin/users/{id}/toggle-status`

### Delete User
**DELETE** `/admin/users/{id}`

---

## 🏷️ Tags Management (Admin)

### Get All Tags
**GET** `/admin/tags`

### Create Tag
**POST** `/admin/tags`

### Update Tag
**PUT** `/admin/tags/{id}`

### Delete Tag
**DELETE** `/admin/tags/{id}`

---

## 📋 Flutter Examples (Admin Panel)

### Admin Login Example

```dart
Future<bool> adminLogin(String email, String password) async {
  try {
    final response = await ApiService.post('/login', {
      'email': email,
      'password': password,
    });

    if (response['success'] && response['data']['user']['role'] == 'admin') {
      ApiService.authToken = response['data']['token'];
      return true;
    }
    return false;
  } catch (e) {
    print('Admin login error: $e');
    return false;
  }
}
```

### Dashboard Stats Example

```dart
Future<DashboardStats?> getDashboardStats() async {
  try {
    final response = await ApiService.get('/admin/dashboard');
    
    if (response['success']) {
      return DashboardStats.fromJson(response['data']);
    }
    return null;
  } catch (e) {
    print('Error fetching stats: $e');
    return null;
  }
}
```

### Product Management Example

```dart
// Get admin products
Future<List<AdminProduct>> getAdminProducts({
  String? search,
  String? status,
  int page = 1,
}) async {
  String query = '?page=$page';
  if (search != null) query += '&search=$search';
  if (status != null) query += '&status=$status';

  final response = await ApiService.get('/admin/products$query');
  
  if (response['success']) {
    return (response['data']['data'] as List)
        .map((item) => AdminProduct.fromJson(item))
        .toList();
  }
  return [];
}

// Create new product
Future<bool> createProduct(Map<String, dynamic> productData) async {
  try {
    final response = await ApiService.post('/admin/products', productData);
    return response['success'] == true;
  } catch (e) {
    print('Error creating product: $e');
    return false;
  }
}

// Update order status
Future<bool> updateOrderStatus(int orderId, String status) async {
  try {
    final response = await ApiService.put('/admin/orders/$orderId', {
      'status': status,
    });
    return response['success'] == true;
  } catch (e) {
    print('Error updating order: $e');
    return false;
  }
}
```

### User Management Example

```dart
Future<bool> toggleUserStatus(int userId, bool isActive) async {
  try {
    final response = await ApiService.patch(
      '/admin/users/$userId/toggle-status',
      {'is_active': isActive}
    );
    return response['success'] == true;
  } catch (e) {
    print('Error toggling user status: $e');
    return false;
  }
}
```

---

## �🔧 Testing

### Testing with Postman

1. Import the API endpoints into Postman
2. Set up environment variables:
   - `base_url`: Your API base URL
   - `auth_token`: Bearer token after login
   - `admin_token`: Admin Bearer token for admin APIs

### Example Postman Collection Structure

```
Crypto E-commerce API/
├── Authentication/
│   ├── Register
│   ├── Login
│   ├── Logout
│   └── Get Profile
├── Products/
│   ├── Get All Products
│   ├── Get Product Details
│   ├── Get Featured Products
│   └── Search Products
├── Cart/
│   ├── Get Cart
│   ├── Add to Cart
│   ├── Update Cart Item
│   └── Remove from Cart
├── Orders/
│   ├── Get Orders
│   ├── Create Order
│   └── Get Order Details
├── Wallet/
│   ├── Get Wallet
│   ├── Get Transactions
│   └── Charge Wallet
└── Admin Panel/
    ├── Dashboard/
    │   ├── Get Dashboard Stats
    │   └── Get Detailed Statistics
    ├── Products Management/
    │   ├── Get All Products
    │   ├── Create Product
    │   ├── Update Product
    │   ├── Delete Product
    │   ├── Restore Product
    │   └── Update Stock
    ├── Categories Management/
    │   ├── Get All Categories
    │   ├── Create Category
    │   ├── Update Category
    │   └── Delete Category
    ├── Orders Management/
    │   ├── Get All Orders
    │   ├── Get Order Details
    │   ├── Update Order Status
    │   ├── Update Payment Status
    │   ├── Get Order Statistics
    │   └── Export Orders
    ├── Users Management/
    │   ├── Get All Users
    │   ├── Get User Details
    │   ├── Create User
    │   ├── Update User
    │   ├── Toggle User Status
    │   └── Delete User
    ├── Coupons Management/
    │   ├── Get All Coupons
    │   ├── Create Coupon
    │   ├── Update Coupon
    │   └── Delete Coupon
    └── Tags Management/
        ├── Get All Tags
        ├── Create Tag
        ├── Update Tag
        └── Delete Tag
```

---

## 🚀 Next Steps

1. **Setup Database**: Ensure all migrations are run
2. **Configure Storage**: Set up file storage for product images
3. **Payment Integration**: Implement payment gateway for wallet charging
4. **Email Service**: Configure email service for notifications
5. **Push Notifications**: Set up FCM for order updates
6. **Rate Limiting**: Implement API rate limiting for production
7. **Logging**: Set up comprehensive logging
8. **Backup**: Configure database backups
9. **SSL**: Enable HTTPS for production
10. **CDN**: Use CDN for static assets
11. **Admin Web Panel**: Develop web UI for admin panel
12. **Role Management**: Extend role system for different admin types
13. **Activity Logs**: Implement admin activity logging system
14. **Automated Backups**: Set up automated database backups
15. **Monitoring**: Set up monitoring and alerting system

---

## 📞 Support

For any questions or issues with the API implementation, please refer to this documentation or contact the development team.

### Quick Start Guide:

1. **For Flutter Developers:**
   - Use "Customer API" section for customer application
   - Flutter code examples provided in each section

2. **For Administrators:**
   - Use "Admin API" section for management panel
   - Access to statistics, reports, and complete system management

3. **For Testing:**
   - Complete Postman collection for testing all endpoints
   - Postman environment variables for easy testing

**API Version:** 1.0  
**Last Updated:** July 4, 2025  
**Documentation Version:** 2.0

---

## 📋 Complete Endpoints List

### Customer APIs:
- **Authentication:** 6 endpoints
- **Products:** 8 endpoints  
- **Categories:** 3 endpoints
- **Cart:** 6 endpoints
- **Orders:** 5 endpoints
- **Wallet:** 3 endpoints
- **Reviews:** 6 endpoints
- **Search:** 1 endpoint

### Admin APIs:
- **Dashboard:** 2 endpoints
- **Products Management:** 7 endpoints
- **Categories Management:** 5 endpoints
- **Orders Management:** 7 endpoints
- **Users Management:** 6 endpoints
- **Coupons Management:** 5 endpoints
- **Tags Management:** 4 endpoints

**Total:** 69+ endpoints
