# مستندات API فروشگاه رمزارز

## مقدمه
این مستند راهنمای کاملی برای API بک‌اند لاراول فروشگاه رمزارز ارائه می‌دهد. این اپلیکیشن یک پلتفرم تک‌فروشنده تجارت رمزارز با پنل مدیریت کامل و عملکردهای مشتری است.

## آدرس پایه
```
http://your-domain.com/api/v1
```

## احراز هویت
API از Laravel Sanctum برای احراز هویت استفاده می‌کند. توکن Bearer را در هدر Authorization برای endpointهای محافظت‌شده قرار دهید.

```
Authorization: Bearer your_token_here
```

## فرمت پاسخ
تمام پاسخ‌های API از این فرمت پیروی می‌کنند:

```json
{
    "success": true|false,
    "message": "پیام پاسخ",
    "data": "داده پاسخ یا null"
}
```

## مدیریت خطا
پاسخ‌های خطا شامل کدهای وضعیت HTTP مناسب و پیام‌های خطا هستند:

```json
{
    "success": false,
    "message": "شرح خطا",
    "errors": {
        "field": ["جزئیات خطا"]
    }
}
```

---

## 🔐 Endpointهای احراز هویت

### ثبت‌نام
ایجاد حساب کاربری جدید برای مشتری.

**POST** `/register`

**بدنه درخواست:**
```json
{
    "name": "احمد احمدی",
    "email": "ahmad@example.com",
    "phone": "+989123456789",
    "password": "password123",
    "password_confirmation": "password123"
}
```

**پاسخ:**
```json
{
    "success": true,
    "message": "کاربر با موفقیت ثبت‌نام شد",
    "data": {
        "user": {
            "id": 1,
            "name": "احمد احمدی",
            "email": "ahmad@example.com",
            "phone": "+989123456789",
            "role": "customer",
            "is_active": true,
            "created_at": "2025-07-03T19:00:00.000000Z"
        },
        "token": "1|abc123...",
        "token_type": "Bearer"
    }
}
```

### ورود
احراز هویت کاربر موجود.

**POST** `/login`

**بدنه درخواست:**
```json
{
    "email": "ahmad@example.com",
    "password": "password123"
}
```

**پاسخ:**
```json
{
    "success": true,
    "message": "ورود موفقیت‌آمیز",
    "data": {
        "user": {
            "id": 1,
            "name": "احمد احمدی",
            "email": "ahmad@example.com",
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

### خروج
خروج کاربر فعلی (نیاز به احراز هویت).

**POST** `/logout`

**هدرها:** `Authorization: Bearer token`

**پاسخ:**
```json
{
    "success": true,
    "message": "خروج موفقیت‌آمیز"
}
```

### دریافت پروفایل
دریافت پروفایل کاربر فعلی (نیاز به احراز هویت).

**GET** `/profile`

**هدرها:** `Authorization: Bearer token`

**پاسخ:**
```json
{
    "success": true,
    "data": {
        "id": 1,
        "name": "احمد احمدی",
        "email": "ahmad@example.com",
        "phone": "+989123456789",
        "wallet": {
            "balance": "1000.00"
        }
    }
}
```

### بروزرسانی پروفایل
بروزرسانی پروفایل کاربر (نیاز به احراز هویت).

**PUT** `/profile`

**هدرها:** `Authorization: Bearer token`

**بدنه درخواست:**
```json
{
    "name": "احمد بروزشده",
    "email": "ahmad.updated@example.com",
    "phone": "+989123456790"
}
```

### تغییر رمز عبور
تغییر رمز عبور کاربر (نیاز به احراز هویت).

**PUT** `/change-password`

**هدرها:** `Authorization: Bearer token`

**بدنه درخواست:**
```json
{
    "current_password": "oldpassword",
    "new_password": "newpassword123",
    "new_password_confirmation": "newpassword123"
}
```

---

## 📦 Endpointهای محصولات

### دریافت همه محصولات
دریافت لیست صفحه‌بندی شده محصولات با فیلترها.

**GET** `/products`

**پارامترهای Query:**
- `search` (string): جستجو در نام و توضیحات
- `category_id` (integer): فیلتر بر اساس دسته‌بندی
- `min_price` (decimal): فیلتر حداقل قیمت
- `max_price` (decimal): فیلتر حداکثر قیمت
- `featured` (boolean): فیلتر محصولات ویژه
- `sort_by` (string): مرتب‌سازی بر اساس price|name|rating|created_at
- `sort_order` (string): asc|desc
- `per_page` (integer): تعداد آیتم در هر صفحه (حداکثر 50)
- `page` (integer): شماره صفحه

**پاسخ:**
```json
{
    "success": true,
    "data": {
        "current_page": 1,
        "data": [
            {
                "id": 1,
                "name": "تجهیزات استخراج بیت‌کوین",
                "slug": "bitcoin-mining-hardware",
                "short_description": "تجهیزات استخراج با کارایی بالا",
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
                    "name": "تجهیزات استخراج"
                }
            }
        ],
        "per_page": 15,
        "total": 100
    }
}
```

### دریافت محصول منفرد
دریافت اطلاعات کامل محصول.

**GET** `/products/{id}`

**پاسخ:**
```json
{
    "success": true,
    "data": {
        "product": {
            "id": 1,
            "name": "تجهیزات استخراج بیت‌کوین",
            "description": "توضیحات کامل محصول...",
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
                    "attribute_name": "رنگ",
                    "attribute_value": "مشکی",
                    "price_adjustment": "0.00"
                }
            ],
            "reviews": [
                {
                    "id": 1,
                    "rating": 5,
                    "title": "محصول عالی",
                    "comment": "کیفیت فوق‌العاده",
                    "user": {
                        "name": "نام مشتری"
                    },
                    "created_at": "2025-07-03T19:00:00.000000Z"
                }
            ]
        },
        "related_products": [
            {
                "id": 2,
                "name": "محصول مرتبط",
                "final_price": "1500.00",
                "primary_image_url": "http://domain.com/storage/products/related.jpg"
            }
        ]
    }
}
```

### دریافت محصولات ویژه
دریافت لیست محصولات ویژه.

**GET** `/products/featured`

### دریافت محصولات جدید
دریافت لیست جدیدترین محصولات.

**GET** `/products/new`

### دریافت پرفروش‌ترین محصولات
دریافت لیست پرفروش‌ترین محصولات.

**GET** `/products/best-selling`

---

## 🏷️ Endpointهای دسته‌بندی

### دریافت همه دسته‌بندی‌ها
دریافت لیست تمام دسته‌بندی‌های فعال با زیردسته‌ها.

**GET** `/categories`

**پاسخ:**
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "name": "تجهیزات استخراج",
            "slug": "mining-equipment",
            "description": "توضیحات دسته‌بندی",
            "image_url": "http://domain.com/storage/categories/image.jpg",
            "products_count": 25,
            "children": [
                {
                    "id": 2,
                    "name": "ماینرهای ASIC",
                    "slug": "asic-miners"
                }
            ]
        }
    ]
}
```

### دریافت محصولات دسته‌بندی
دریافت محصولات یک دسته‌بندی خاص.

**GET** `/categories/{categoryId}/products`

**پارامترهای Query:** مشابه لیست محصولات

---

## 🛒 Endpointهای سبد خرید (نیاز به احراز هویت)

### دریافت سبد خرید
دریافت آیتم‌های سبد خرید کاربر فعلی.

**GET** `/cart`

**هدرها:** `Authorization: Bearer token`

**پاسخ:**
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
                    "name": "تجهیزات استخراج بیت‌کوین",
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

### افزودن به سبد خرید
افزودن محصول به سبد خرید.

**POST** `/cart`

**هدرها:** `Authorization: Bearer token`

**بدنه درخواست:**
```json
{
    "product_id": 1,
    "quantity": 2,
    "selected_attributes": {
        "color": "مشکی",
        "size": "بزرگ"
    }
}
```

### بروزرسانی آیتم سبد خرید
بروزرسانی تعداد یا ویژگی‌های آیتم سبد خرید.

**PUT** `/cart/{cartItemId}`

**هدرها:** `Authorization: Bearer token`

**بدنه درخواست:**
```json
{
    "quantity": 3,
    "selected_attributes": {
        "color": "آبی"
    }
}
```

### حذف از سبد خرید
حذف آیتم از سبد خرید.

**DELETE** `/cart/{cartItemId}`

**هدرها:** `Authorization: Bearer token`

### پاک کردن سبد خرید
حذف تمام آیتم‌ها از سبد خرید.

**DELETE** `/cart`

**هدرها:** `Authorization: Bearer token`

### دریافت تعداد سبد خرید
دریافت تعداد کل آیتم‌ها در سبد خرید.

**GET** `/cart/count`

**هدرها:** `Authorization: Bearer token`

**پاسخ:**
```json
{
    "success": true,
    "data": {
        "count": 5
    }
}
```

---

## 🛍️ Endpointهای سفارش (نیاز به احراز هویت)

### دریافت سفارشات
دریافت تاریخچه سفارشات کاربر.

**GET** `/orders`

**هدرها:** `Authorization: Bearer token`

**پارامترهای Query:**
- `status` (string): فیلتر بر اساس وضعیت سفارش
- `page` (integer): شماره صفحه

**پاسخ:**
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

### ایجاد سفارش
ایجاد سفارش جدید از آیتم‌های سبد خرید.

**POST** `/orders`

**هدرها:** `Authorization: Bearer token`

**بدنه درخواست:**
```json
{
    "shipping_address": {
        "name": "احمد احمدی",
        "phone": "+989123456789",
        "street": "خیابان آزادی، پلاک 123",
        "city": "تهران",
        "state": "تهران",
        "postal_code": "1234567890",
        "country": "ایران"
    },
    "billing_address": {
        "name": "احمد احمدی",
        "phone": "+989123456789",
        "street": "خیابان آزادی، پلاک 123",
        "city": "تهران",
        "state": "تهران",
        "postal_code": "1234567890",
        "country": "ایران"
    },
    "payment_method": "wallet",
    "coupon_code": "SAVE10",
    "notes": "لطفاً با احتیاط حمل کنید"
}
```

### دریافت جزئیات سفارش
دریافت اطلاعات کامل سفارش.

**GET** `/orders/{orderId}`

**هدرها:** `Authorization: Bearer token`

**پاسخ:**
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
            "name": "احمد احمدی",
            "street": "خیابان آزادی، پلاک 123"
        },
        "items": [
            {
                "id": 1,
                "product_name": "تجهیزات استخراج بیت‌کوین",
                "quantity": 2,
                "unit_price": "2000.00",
                "total_price": "4000.00",
                "product": {
                    "id": 1,
                    "name": "تجهیزات استخراج بیت‌کوین",
                    "images": [...]
                }
            }
        ],
        "created_at": "2025-07-03T19:00:00.000000Z"
    }
}
```

### لغو سفارش
لغو سفارش (فقط اگر وضعیت pending یا processing باشد).

**POST** `/orders/{orderId}/cancel`

**هدرها:** `Authorization: Bearer token`

### اعتبارسنجی کوپن
اعتبارسنجی کد کوپن قبل از اعمال به سفارش.

**POST** `/coupons/validate`

**هدرها:** `Authorization: Bearer token`

**بدنه درخواست:**
```json
{
    "coupon_code": "SAVE10",
    "order_amount": "1000.00"
}
```

**پاسخ:**
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

## 💰 Endpointهای کیف پول (نیاز به احراز هویت)

### دریافت کیف پول
دریافت موجودی و اطلاعات کیف پول.

**GET** `/wallet`

**هدرها:** `Authorization: Bearer token`

**پاسخ:**
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

### دریافت تراکنش‌های کیف پول
دریافت تاریخچه تراکنش‌های کیف پول.

**GET** `/wallet/transactions`

**هدرها:** `Authorization: Bearer token`

**پارامترهای Query:**
- `type` (string): credit|debit
- `transaction_type` (string): فیلتر بر اساس نوع تراکنش
- `page` (integer): شماره صفحه

**پاسخ:**
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
                "description": "واریز انتقال بانکی",
                "created_at": "2025-07-03T19:00:00.000000Z"
            }
        ]
    }
}
```

### شارژ کیف پول
افزودن پول به کیف پول (در اپلیکیشن واقعی، این با درگاه پرداخت یکپارچه می‌شود).

**POST** `/wallet/charge`

**هدرها:** `Authorization: Bearer token`

**بدنه درخواست:**
```json
{
    "amount": "500.00",
    "payment_method": "bank_transfer",
    "reference": "TXN123456"
}
```

---

## ⭐ Endpointهای نظرات (نیاز به احراز هویت)

### دریافت نظرات کاربر
دریافت نظرات کاربر فعلی.

**GET** `/reviews`

**هدرها:** `Authorization: Bearer token`

### ایجاد نظر
ایجاد نظر محصول (فقط برای محصولات خریداری شده).

**POST** `/reviews`

**هدرها:** `Authorization: Bearer token`

**بدنه درخواست:**
```json
{
    "product_id": 1,
    "order_id": 1,
    "rating": 5,
    "title": "محصول عالی",
    "comment": "بسیار راضی از کیفیت و عملکرد هستم."
}
```

### بروزرسانی نظر
بروزرسانی نظر موجود.

**PUT** `/reviews/{reviewId}`

**هدرها:** `Authorization: Bearer token`

**بدنه درخواست:** مشابه ایجاد

### حذف نظر
حذف نظر.

**DELETE** `/reviews/{reviewId}`

**هدرها:** `Authorization: Bearer token`

### دریافت نظرات محصول
دریافت نظرات یک محصول خاص.

**GET** `/products/{productId}/reviews`

**پارامترهای Query:**
- `page` (integer): شماره صفحه
- `rating` (integer): فیلتر بر اساس امتیاز (1-5)

---

## 🔍 Endpoint جستجو

### جستجوی محصولات
جستجو برای محصولات در نام، توضیحات و SKU.

**GET** `/search`

**پارامترهای Query:**
- `q` (string, اجباری): عبارت جستجو (حداقل 2 کاراکتر)
- `per_page` (integer): تعداد آیتم در هر صفحه
- `page` (integer): شماره صفحه

---

## 📊 کدهای خطا

### کدهای وضعیت HTTP
- `200` - موفق
- `201` - ایجاد شده
- `400` - درخواست نامعتبر
- `401` - غیرمجاز
- `403` - ممنوع
- `404` - یافت نشد
- `422` - خطای اعتبارسنجی
- `500` - خطای سرور

### پاسخ‌های خطای رایج

**خطای اعتبارسنجی (422):**
```json
{
    "success": false,
    "message": "داده‌های ارسالی نامعتبر است.",
    "errors": {
        "email": ["فیلد ایمیل الزامی است."],
        "password": ["رمز عبور باید حداقل 8 کاراکتر باشد."]
    }
}
```

**غیرمجاز (401):**
```json
{
    "success": false,
    "message": "احراز هویت نشده."
}
```

**ممنوع (403):**
```json
{
    "success": false,
    "message": "این عمل مجاز نیست."
}
```

**یافت نشد (404):**
```json
{
    "success": false,
    "message": "منبع یافت نشد."
}
```

---

## 🎯 نمونه‌های استفاده برای Flutter

### راه‌اندازی کلاینت HTTP

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

### نمونه ورود

```dart
Future<bool> login(String email, String password) async {
  try {
    final response = await ApiService.post('/login', {
      'email': email,
      'password': password,
    });

    if (response['success']) {
      ApiService.authToken = response['data']['token'];
      // ذخیره توکن در حافظه امن
      return true;
    }
    return false;
  } catch (e) {
    print('خطای ورود: $e');
    return false;
  }
}
```

### نمونه دریافت محصولات

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

### نمونه افزودن به سبد خرید

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

## 🛡️ API های پنل ادمین (نیاز به احراز هویت و نقش ادمین)

### آدرس پایه
```
http://your-domain.com/api/v1/admin
```

**توجه:** تمام endpoint های ادمین نیاز به احراز هویت با نقش admin دارند.

**هدرها:** `Authorization: Bearer admin_token`

---

## 📊 Dashboard و آمار

### دریافت داشبورد
دریافت آمار کلی داشبورد ادمین.

**GET** `/admin/dashboard`

**پاسخ:**
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

### دریافت آمار تفصیلی
دریافت آمار تفصیلی برای گزارش‌گیری.

**GET** `/admin/stats`

**پارامترهای Query:**
- `period` (string): daily|weekly|monthly|yearly
- `start_date` (date): تاریخ شروع
- `end_date` (date): تاریخ پایان

**پاسخ:**
```json
{
    "success": true,
    "data": {
        "sales": {
            "total_amount": "15000.00",
            "total_orders": 45,
            "chart_data": [
                {"date": "2025-07-01", "amount": "2500.00", "orders": 8},
                {"date": "2025-07-02", "amount": "3200.00", "orders": 12}
            ]
        },
        "top_products": [
            {
                "id": 1,
                "name": "تجهیزات استخراج بیت‌کوین",
                "total_sold": 25,
                "revenue": "50000.00"
            }
        ],
        "user_registrations": 15,
        "revenue_by_category": [
            {
                "category": "تجهیزات استخراج",
                "revenue": "8500.00"
            }
        ]
    }
}
```

---

## 🏷️ مدیریت دسته‌بندی‌ها (ادمین)

### دریافت همه دسته‌بندی‌ها
دریافت لیست تمام دسته‌بندی‌ها شامل غیرفعال.

**GET** `/admin/categories`

**پارامترهای Query:**
- `search` (string): جستجو در نام
- `status` (string): active|inactive
- `parent_id` (integer): فیلتر بر اساس والد
- `page` (integer): شماره صفحه

**پاسخ:**
```json
{
    "success": true,
    "data": {
        "data": [
            {
                "id": 1,
                "name": "تجهیزات استخراج",
                "slug": "mining-equipment",
                "description": "توضیحات دسته‌بندی",
                "image_url": "http://domain.com/storage/categories/image.jpg",
                "parent_id": null,
                "status": "active",
                "sort_order": 1,
                "products_count": 25,
                "children": [
                    {
                        "id": 2,
                        "name": "ماینرهای ASIC",
                        "products_count": 12
                    }
                ],
                "created_at": "2025-07-03T19:00:00.000000Z",
                "updated_at": "2025-07-03T19:00:00.000000Z"
            }
        ],
        "total": 15
    }
}
```

### ایجاد دسته‌بندی جدید

**POST** `/admin/categories`

**بدنه درخواست:**
```json
{
    "name": "کیف پول سخت‌افزاری",
    "description": "کیف پول‌های سخت‌افزاری امن",
    "parent_id": 1,
    "image": "file_upload",
    "status": "active",
    "sort_order": 2,
    "meta_title": "کیف پول سخت‌افزاری",
    "meta_description": "بهترین کیف پول‌های سخت‌افزاری"
}
```

### ویرایش دسته‌بندی

**PUT** `/admin/categories/{id}`

**بدنه درخواست:** مشابه ایجاد

### حذف دسته‌بندی

**DELETE** `/admin/categories/{id}`

### دریافت دسته‌بندی‌های والد
دریافت لیست دسته‌بندی‌های سطح اول برای انتخاب والد.

**GET** `/admin/categories/parent/list`

---

## 📦 مدیریت محصولات (ادمین)

### دریافت همه محصولات
دریافت لیست کامل محصولات شامل حذف شده.

**GET** `/admin/products`

**پارامترهای Query:**
- `search` (string): جستجو در نام، SKU
- `category_id` (integer): فیلتر بر اساس دسته‌بندی
- `status` (string): active|inactive|draft
- `stock_status` (string): in_stock|low_stock|out_of_stock
- `featured` (boolean): فیلتر محصولات ویژه
- `trashed` (boolean): شامل محصولات حذف شده
- `sort_by` (string): name|price|stock|created_at
- `sort_order` (string): asc|desc
- `page` (integer): شماره صفحه

**پاسخ:**
```json
{
    "success": true,
    "data": {
        "data": [
            {
                "id": 1,
                "name": "تجهیزات استخراج بیت‌کوین",
                "slug": "bitcoin-mining-hardware",
                "sku": "BTC-MINER-001",
                "short_description": "تجهیزات استخراج با کارایی بالا",
                "price": "2500.00",
                "sale_price": "2000.00",
                "stock_quantity": 10,
                "weight": "5.50",
                "dimensions": "30x20x15",
                "status": "active",
                "is_featured": true,
                "category": {
                    "id": 1,
                    "name": "تجهیزات استخراج"
                },
                "images": [
                    {
                        "id": 1,
                        "image_url": "http://domain.com/storage/products/image1.jpg",
                        "is_primary": true
                    }
                ],
                "total_sold": 45,
                "total_revenue": "90000.00",
                "created_at": "2025-07-03T19:00:00.000000Z",
                "deleted_at": null
            }
        ],
        "total": 100
    }
}
```

### ایجاد محصول جدید

**POST** `/admin/products`

**Content-Type:** `multipart/form-data`

**بدنه درخواست:**
```json
{
    "name": "کیف پول سخت‌افزاری Ledger",
    "short_description": "کیف پول امن و قابل اعتماد",
    "description": "توضیحات کامل محصول...",
    "sku": "LEDGER-WALLET-001",
    "price": "150.00",
    "sale_price": "120.00",
    "stock_quantity": 50,
    "weight": "0.5",
    "dimensions": "10x5x2",
    "category_id": 2,
    "status": "active",
    "is_featured": false,
    "meta_title": "کیف پول Ledger",
    "meta_description": "بهترین کیف پول سخت‌افزاری",
    "images": ["file_upload_1", "file_upload_2"],
    "primary_image_index": 0,
    "attributes": [
        {
            "attribute_name": "رنگ",
            "attribute_value": "مشکی",
            "price_adjustment": "0.00"
        }
    ],
    "tags": ["کیف پول", "امن", "سخت‌افزاری"]
}
```

### ویرایش محصول

**PUT** `/admin/products/{id}`

**بدنه درخواست:** مشابه ایجاد

### حذف موقت محصول

**DELETE** `/admin/products/{id}`

### بازگردانی محصول حذف شده

**PATCH** `/admin/products/{id}/restore`

### حذف دائمی محصول

**DELETE** `/admin/products/{id}/force-delete`

### بروزرسانی موجودی
بروزرسانی سریع موجودی محصول.

**PATCH** `/admin/products/{id}/stock`

**بدنه درخواست:**
```json
{
    "stock_quantity": 25,
    "reason": "تامین موجودی جدید"
}
```

---

## 🛍️ مدیریت سفارشات (ادمین)

### دریافت همه سفارشات
دریافت لیست کامل سفارشات با جزئیات.

**GET** `/admin/orders`

**پارامترهای Query:**
- `search` (string): جستجو در شماره سفارش، نام مشتری
- `status` (string): pending|processing|shipped|delivered|cancelled
- `payment_status` (string): pending|paid|failed|refunded
- `start_date` (date): تاریخ شروع
- `end_date` (date): تاریخ پایان
- `customer_id` (integer): فیلتر بر اساس مشتری
- `min_amount` (decimal): حداقل مبلغ سفارش
- `max_amount` (decimal): حداکثر مبلغ سفارش
- `sort_by` (string): created_at|total_amount|customer_name
- `sort_order` (string): asc|desc
- `page` (integer): شماره صفحه

**پاسخ:**
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
                "subtotal": "4000.00",
                "discount_amount": "400.00",
                "shipping_cost": "50.00",
                "total_amount": "3650.00",
                "items_count": 2,
                "customer": {
                    "id": 1,
                    "name": "احمد احمدی",
                    "email": "ahmad@example.com",
                    "phone": "+989123456789"
                },
                "shipping_address": {
                    "name": "احمد احمدی",
                    "street": "خیابان آزادی، پلاک 123",
                    "city": "تهران",
                    "state": "تهران",
                    "postal_code": "1234567890"
                },
                "payment": {
                    "method": "wallet",
                    "transaction_id": "TXN123456",
                    "paid_at": "2025-07-03T19:00:00.000000Z"
                },
                "notes": "لطفاً با احتیاط حمل کنید",
                "created_at": "2025-07-03T19:00:00.000000Z",
                "updated_at": "2025-07-03T20:00:00.000000Z"
            }
        ],
        "total": 320
    }
}
```

### دریافت جزئیات سفارش

**GET** `/admin/orders/{id}`

**پاسخ:**
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
        "customer": {
            "id": 1,
            "name": "احمد احمدی",
            "email": "ahmad@example.com",
            "phone": "+989123456789",
            "total_orders": 5,
            "total_spent": "15000.00",
            "last_order_at": "2025-07-03T18:00:00.000000Z"
        },
        "items": [
            {
                "id": 1,
                "product_id": 1,
                "product_name": "تجهیزات استخراج بیت‌کوین",
                "product_sku": "BTC-MINER-001",
                "quantity": 2,
                "unit_price": "2000.00",
                "total_price": "4000.00",
                "selected_attributes": {
                    "color": "مشکی"
                },
                "product": {
                    "id": 1,
                    "current_price": "2000.00",
                    "current_stock": 8,
                    "images": [...]
                }
            }
        ],
        "shipping_address": {
            "name": "احمد احمدی",
            "phone": "+989123456789",
            "street": "خیابان آزادی، پلاک 123",
            "city": "تهران",
            "state": "تهران",
            "postal_code": "1234567890",
            "country": "ایران"
        },
        "billing_address": {
            "name": "احمد احمدی",
            "phone": "+989123456789",
            "street": "خیابان آزادی، پلاک 123",
            "city": "تهران",
            "state": "تهران",
            "postal_code": "1234567890",
            "country": "ایران"
        },
        "payment": {
            "method": "wallet",
            "transaction_id": "TXN123456",
            "paid_at": "2025-07-03T19:00:00.000000Z"
        },
        "coupon": {
            "code": "SAVE10",
            "discount_amount": "400.00"
        },
        "status_history": [
            {
                "status": "pending",
                "created_at": "2025-07-03T19:00:00.000000Z"
            },
            {
                "status": "processing",
                "created_at": "2025-07-03T19:15:00.000000Z"
            }
        ],
        "notes": "لطفاً با احتیاط حمل کنید",
        "admin_notes": "مشتری VIP",
        "created_at": "2025-07-03T19:00:00.000000Z"
    }
}
```

### ویرایش سفارش

**PUT** `/admin/orders/{id}`

**بدنه درخواست:**
```json
{
    "status": "shipped",
    "admin_notes": "ارسال شده با پست پیشتاز",
    "tracking_number": "POST123456789"
}
```

### تغییر وضعیت پرداخت

**PATCH** `/admin/orders/{id}/payment-status`

**بدنه درخواست:**
```json
{
    "payment_status": "refunded",
    "refund_reason": "درخواست مشتری",
    "refund_amount": "3650.00"
}
```

### آمار سفارشات

**GET** `/admin/orders/statistics`

**پارامترهای Query:**
- `period` (string): today|week|month|year
- `start_date` (date): تاریخ شروع
- `end_date` (date): تاریخ پایان

**پاسخ:**
```json
{
    "success": true,
    "data": {
        "total_orders": 320,
        "total_revenue": "125000.00",
        "average_order_value": "390.62",
        "orders_by_status": {
            "pending": 8,
            "processing": 15,
            "shipped": 25,
            "delivered": 280,
            "cancelled": 12
        },
        "daily_stats": [
            {
                "date": "2025-07-01",
                "orders": 12,
                "revenue": "4680.00"
            }
        ],
        "top_customers": [
            {
                "id": 1,
                "name": "احمد احمدی",
                "total_orders": 8,
                "total_spent": "15600.00"
            }
        ]
    }
}
```

### صادرات سفارشات

**GET** `/admin/orders/export`

**پارامترهای Query:**
- `format` (string): csv|excel|pdf
- `start_date` (date): تاریخ شروع
- `end_date` (date): تاریخ پایان
- `status` (string): فیلتر وضعیت

---

## 💰 مدیریت کوپن‌ها (ادمین)

### دریافت همه کوپن‌ها

**GET** `/admin/coupons`

**پارامترهای Query:**
- `search` (string): جستجو در کد کوپن
- `status` (string): active|inactive|expired
- `type` (string): percentage|fixed
- `page` (integer): شماره صفحه

**پاسخ:**
```json
{
    "success": true,
    "data": {
        "data": [
            {
                "id": 1,
                "code": "SAVE10",
                "type": "percentage",
                "value": "10.00",
                "minimum_amount": "100.00",
                "maximum_discount": "50.00",
                "usage_limit": 100,
                "used_count": 25,
                "starts_at": "2025-07-01T00:00:00.000000Z",
                "expires_at": "2025-07-31T23:59:59.000000Z",
                "is_active": true,
                "created_at": "2025-07-01T10:00:00.000000Z"
            }
        ],
        "total": 15
    }
}
```

### ایجاد کوپن جدید

**POST** `/admin/coupons`

**بدنه درخواست:**
```json
{
    "code": "SUMMER25",
    "description": "تخفیف تابستانی 25 درصد",
    "type": "percentage",
    "value": "25.00",
    "minimum_amount": "200.00",
    "maximum_discount": "100.00",
    "usage_limit": 50,
    "starts_at": "2025-07-01",
    "expires_at": "2025-08-31",
    "is_active": true
}
```

### ویرایش کوپن

**PUT** `/admin/coupons/{id}`

### حذف کوپن

**DELETE** `/admin/coupons/{id}`

### دریافت آمار استفاده کوپن

**GET** `/admin/coupons/{id}`

**پاسخ شامل آمار استفاده:**
```json
{
    "success": true,
    "data": {
        "id": 1,
        "code": "SAVE10",
        "type": "percentage",
        "value": "10.00",
        "usage_statistics": {
            "total_used": 25,
            "total_discount_given": "1250.00",
            "most_recent_usage": "2025-07-03T18:30:00.000000Z",
            "usage_by_date": [
                {
                    "date": "2025-07-01",
                    "usage_count": 8,
                    "discount_amount": "400.00"
                }
            ]
        }
    }
}
```

---

## 👥 مدیریت کاربران (ادمین)

### دریافت همه کاربران

**GET** `/admin/users`

**پارامترهای Query:**
- `search` (string): جستجو در نام، ایمیل، تلفن
- `role` (string): admin|customer
- `status` (string): active|inactive
- `registration_date_from` (date): تاریخ ثبت‌نام از
- `registration_date_to` (date): تاریخ ثبت‌نام تا
- `sort_by` (string): name|email|created_at|total_orders
- `sort_order` (string): asc|desc
- `page` (integer): شماره صفحه

**پاسخ:**
```json
{
    "success": true,
    "data": {
        "data": [
            {
                "id": 1,
                "name": "احمد احمدی",
                "email": "ahmad@example.com",
                "phone": "+989123456789",
                "role": "customer",
                "is_active": true,
                "email_verified_at": "2025-07-03T19:00:00.000000Z",
                "orders_count": 5,
                "total_spent": "15000.00",
                "last_order_at": "2025-07-03T18:00:00.000000Z",
                "wallet": {
                    "balance": "1500.50"
                },
                "created_at": "2025-06-15T10:00:00.000000Z",
                "last_login_at": "2025-07-03T19:00:00.000000Z"
            }
        ],
        "total": 150
    }
}
```

### دریافت جزئیات کاربر

**GET** `/admin/users/{id}`

**پاسخ:**
```json
{
    "success": true,
    "data": {
        "id": 1,
        "name": "احمد احمدی",
        "email": "ahmad@example.com",
        "phone": "+989123456789",
        "role": "customer",
        "is_active": true,
        "email_verified_at": "2025-07-03T19:00:00.000000Z",
        "profile": {
            "birthday": "1990-05-15",
            "gender": "male",
            "city": "تهران"
        },
        "wallet": {
            "balance": "1500.50",
            "total_deposits": "5000.00",
            "total_spent": "3500.00"
        },
        "orders_statistics": {
            "total_orders": 5,
            "total_spent": "15000.00",
            "average_order_value": "3000.00",
            "last_order_at": "2025-07-03T18:00:00.000000Z",
            "orders_by_status": {
                "delivered": 4,
                "processing": 1
            }
        },
        "recent_orders": [
            {
                "id": 1,
                "order_number": "ORD-1720024800-1234",
                "total_amount": "3650.00",
                "status": "delivered",
                "created_at": "2025-07-03T19:00:00.000000Z"
            }
        ],
        "wallet_transactions": [
            {
                "id": 1,
                "type": "credit",
                "amount": "500.00",
                "description": "شارژ کیف پول",
                "created_at": "2025-07-03T19:00:00.000000Z"
            }
        ],
        "created_at": "2025-06-15T10:00:00.000000Z",
        "last_login_at": "2025-07-03T19:00:00.000000Z"
    }
}
```

### ایجاد کاربر جدید

**POST** `/admin/users`

**بدنه درخواست:**
```json
{
    "name": "کاربر جدید",
    "email": "newuser@example.com",
    "phone": "+989123456790",
    "password": "password123",
    "role": "customer",
    "is_active": true,
    "email_verified": true
}
```

### ویرایش کاربر

**PUT** `/admin/users/{id}`

**بدنه درخواست:**
```json
{
    "name": "احمد احمدی بروزشده",
    "email": "ahmad.updated@example.com",
    "phone": "+989123456791",
    "role": "customer",
    "is_active": true
}
```

### تغییر وضعیت کاربر
فعال/غیرفعال کردن حساب کاربری.

**PATCH** `/admin/users/{id}/toggle-status`

**بدنه درخواست:**
```json
{
    "is_active": false,
    "reason": "نقض قوانین سایت"
}
```

### حذف کاربر

**DELETE** `/admin/users/{id}`

---

## 🏷️ مدیریت برچسب‌ها (ادمین)

### دریافت همه برچسب‌ها

**GET** `/admin/tags`

**پارامترهای Query:**
- `search` (string): جستجو در نام برچسب
- `sort_by` (string): name|products_count|created_at
- `page` (integer): شماره صفحه

**پاسخ:**
```json
{
    "success": true,
    "data": {
        "data": [
            {
                "id": 1,
                "name": "کیف پول",
                "slug": "wallet",
                "products_count": 15,
                "created_at": "2025-07-03T19:00:00.000000Z"
            }
        ],
        "total": 25
    }
}
```

### ایجاد برچسب جدید

**POST** `/admin/tags`

**بدنه درخواست:**
```json
{
    "name": "DeFi",
    "description": "محصولات مرتبط با فایننس غیرمتمرکز"
}
```

### ویرایش برچسب

**PUT** `/admin/tags/{id}`

### حذف برچسب

**DELETE** `/admin/tags/{id}`

---

## 📋 نمونه‌های کاربرد برای Flutter (پنل ادمین)

### نمونه ورود ادمین

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
    print('خطای ورود ادمین: $e');
    return false;
  }
}
```

### نمونه دریافت آمار داشبورد

```dart
Future<DashboardStats?> getDashboardStats() async {
  try {
    final response = await ApiService.get('/admin/dashboard');
    
    if (response['success']) {
      return DashboardStats.fromJson(response['data']);
    }
    return null;
  } catch (e) {
    print('خطای دریافت آمار: $e');
    return null;
  }
}
```

### نمونه مدیریت محصولات

```dart
// دریافت محصولات
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

// ایجاد محصول جدید
Future<bool> createProduct(Map<String, dynamic> productData) async {
  try {
    final response = await ApiService.post('/admin/products', productData);
    return response['success'] == true;
  } catch (e) {
    print('خطای ایجاد محصول: $e');
    return false;
  }
}

// بروزرسانی وضعیت سفارش
Future<bool> updateOrderStatus(int orderId, String status) async {
  try {
    final response = await ApiService.put('/admin/orders/$orderId', {
      'status': status,
    });
    return response['success'] == true;
  } catch (e) {
    print('خطای بروزرسانی سفارش: $e');
    return false;
  }
}
```

### نمونه مدیریت کاربران

```dart
Future<bool> toggleUserStatus(int userId, bool isActive) async {
  try {
    final response = await ApiService.patch(
      '/admin/users/$userId/toggle-status',
      {'is_active': isActive}
    );
    return response['success'] == true;
  } catch (e) {
    print('خطای تغییر وضعیت کاربر: $e');
    return false;
  }
}
```

---

## 🔧 تست

### تست با Postman

1. Endpointهای API را در Postman وارد کنید
2. متغیرهای محیطی را تنظیم کنید:
   - `base_url`: آدرس پایه API شما
   - `auth_token`: توکن Bearer بعد از ورود
   - `admin_token`: توکن Bearer ادمین برای API های ادمین

### نمونه ساختار مجموعه Postman

```
Crypto E-commerce API/
├── Authentication/
│   ├── Register (ثبت‌نام)
│   ├── Login (ورود)
│   ├── Logout (خروج)
│   └── Get Profile (دریافت پروفایل)
├── Products/
│   ├── Get All Products (دریافت همه محصولات)
│   ├── Get Product Details (جزئیات محصول)
│   ├── Get Featured Products (محصولات ویژه)
│   └── Search Products (جستجوی محصولات)
├── Cart/
│   ├── Get Cart (دریافت سبد خرید)
│   ├── Add to Cart (افزودن به سبد)
│   ├── Update Cart Item (بروزرسانی آیتم سبد)
│   └── Remove from Cart (حذف از سبد)
├── Orders/
│   ├── Get Orders (دریافت سفارشات)
│   ├── Create Order (ایجاد سفارش)
│   └── Get Order Details (جزئیات سفارش)
├── Wallet/
│   ├── Get Wallet (دریافت کیف پول)
│   ├── Get Transactions (دریافت تراکنش‌ها)
│   └── Charge Wallet (شارژ کیف پول)
└── Admin Panel/
    ├── Dashboard/
    │   ├── Get Dashboard Stats (آمار داشبورد)
    │   └── Get Detailed Statistics (آمار تفصیلی)
    ├── Products Management/
    │   ├── Get All Products (دریافت همه محصولات)
    │   ├── Create Product (ایجاد محصول)
    │   ├── Update Product (ویرایش محصول)
    │   ├── Delete Product (حذف محصول)
    │   ├── Restore Product (بازگردانی محصول)
    │   └── Update Stock (بروزرسانی موجودی)
    ├── Categories Management/
    │   ├── Get All Categories (دریافت دسته‌بندی‌ها)
    │   ├── Create Category (ایجاد دسته‌بندی)
    │   ├── Update Category (ویرایش دسته‌بندی)
    │   └── Delete Category (حذف دسته‌بندی)
    ├── Orders Management/
    │   ├── Get All Orders (دریافت همه سفارشات)
    │   ├── Get Order Details (جزئیات سفارش)
    │   ├── Update Order Status (تغییر وضعیت سفارش)
    │   ├── Update Payment Status (تغییر وضعیت پرداخت)
    │   ├── Get Order Statistics (آمار سفارشات)
    │   └── Export Orders (صادرات سفارشات)
    ├── Users Management/
    │   ├── Get All Users (دریافت همه کاربران)
    │   ├── Get User Details (جزئیات کاربر)
    │   ├── Create User (ایجاد کاربر)
    │   ├── Update User (ویرایش کاربر)
    │   ├── Toggle User Status (تغییر وضعیت کاربر)
    │   └── Delete User (حذف کاربر)
    ├── Coupons Management/
    │   ├── Get All Coupons (دریافت همه کوپن‌ها)
    │   ├── Create Coupon (ایجاد کوپن)
    │   ├── Update Coupon (ویرایش کوپن)
    │   └── Delete Coupon (حذف کوپن)
    └── Tags Management/
        ├── Get All Tags (دریافت همه برچسب‌ها)
        ├── Create Tag (ایجاد برچسب)
        ├── Update Tag (ویرایش برچسب)
        └── Delete Tag (حذف برچسب)
```

---

## 🚀 مراحل بعدی

1. **راه‌اندازی دیتابیس**: اطمینان از اجرای تمام migrationها
2. **پیکربندی ذخیره‌سازی**: راه‌اندازی ذخیره‌سازی فایل برای تصاویر محصولات
3. **یکپارچه‌سازی پرداخت**: پیاده‌سازی درگاه پرداخت برای شارژ کیف پول
4. **سرویس ایمیل**: پیکربندی سرویس ایمیل برای اعلان‌ها
5. **اعلان‌های Push**: راه‌اندازی FCM برای بروزرسانی‌های سفارش
6. **محدودیت نرخ**: پیاده‌سازی محدودیت نرخ API برای تولید
7. **گزارش‌گیری**: راه‌اندازی گزارش‌گیری جامع
8. **پشتیبان‌گیری**: پیکربندی پشتیبان‌گیری دیتابیس
9. **SSL**: فعال‌سازی HTTPS برای تولید
10. **CDN**: استفاده از CDN برای دارایی‌های استاتیک
11. **پنل ادمین وب**: توسعه رابط کاربری وب برای پنل ادمین
12. **مدیریت نقش‌ها**: گسترش سیستم نقش‌ها برای انواع مختلف ادمین
13. **لاگ فعالیت‌ها**: پیاده‌سازی سیستم ثبت فعالیت‌های ادمین
14. **پشتیبان‌گیری خودکار**: تنظیم پشتیبان‌گیری خودکار دیتابیس
15. **مانیتورینگ**: راه‌اندازی سیستم نظارت و هشدار

---

## 📞 پشتیبانی

برای هرگونه سوال یا مشکل در پیاده‌سازی API، لطفاً به این مستندات مراجعه کنید یا با تیم توسعه تماس بگیرید.

### راهنمای سریع شروع:

1. **برای توسعه‌دهندگان Flutter:**
   - از بخش "Customer API" برای اپلیکیشن مشتری استفاده کنید
   - نمونه کدهای Flutter در هر بخش ارائه شده است

2. **برای ادمین‌ها:**
   - از بخش "Admin API" برای پنل مدیریت استفاده کنید
   - دسترسی به آمار، گزارش‌ها و مدیریت کامل سیستم

3. **برای تست:**
   - Collection Postman کامل برای تست تمام endpoint ها
   - محیط متغیرهای Postman برای سهولت تست

**نسخه API:** 1.0  
**آخرین بروزرسانی:** 4 جولای 2025  
**نسخه مستندات:** 2.0

---

## 📋 فهرست کامل Endpoint ها

### مشتری (Customer APIs):
- **احراز هویت:** 6 endpoint
- **محصولات:** 8 endpoint  
- **دسته‌بندی:** 3 endpoint
- **سبد خرید:** 6 endpoint
- **سفارشات:** 5 endpoint
- **کیف پول:** 3 endpoint
- **نظرات:** 6 endpoint
- **جستجو:** 1 endpoint

### ادمین (Admin APIs):
- **داشبورد:** 2 endpoint
- **مدیریت محصولات:** 7 endpoint
- **مدیریت دسته‌بندی:** 5 endpoint
- **مدیریت سفارشات:** 7 endpoint
- **مدیریت کاربران:** 6 endpoint
- **مدیریت کوپن:** 5 endpoint
- **مدیریت برچسب:** 4 endpoint

**مجموع:** 69+ endpoint
