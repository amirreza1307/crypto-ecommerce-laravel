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

## 🔧 تست

### تست با Postman

1. Endpointهای API را در Postman وارد کنید
2. متغیرهای محیطی را تنظیم کنید:
   - `base_url`: آدرس پایه API شما
   - `auth_token`: توکن Bearer بعد از ورود

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
└── Wallet/
    ├── Get Wallet (دریافت کیف پول)
    ├── Get Transactions (دریافت تراکنش‌ها)
    └── Charge Wallet (شارژ کیف پول)
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

---

## 📞 پشتیبانی

برای هرگونه سوال یا مشکل در پیاده‌سازی API، لطفاً به این مستندات مراجعه کنید یا با تیم توسعه تماس بگیرید.

**نسخه API:** 1.0  
**آخرین بروزرسانی:** 3 جولای 2025  
**نسخه مستندات:** 1.0
