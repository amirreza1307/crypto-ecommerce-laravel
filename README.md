# فروشگاه رمزارز - Crypto E-commerce

یک پلتفرم تک‌فروشنده برای خرید و فروش رمزارزها که با Laravel (Backend) و Flutter (Frontend) توسعه یافته است.

## ویژگی‌های اصلی

### پنل مدیریت (Admin Panel)
- 📊 **داشبورد مدیریت**: نمایش آمار فروش، سفارشات، کاربران و درآمد
- 🏷️ **مدیریت دسته‌بندی**: ایجاد و مدیریت دسته‌بندی‌های چندسطحی
- 💰 **مدیریت محصولات**: افزودن، ویرایش و مدیریت محصولات رمزارز
- 📦 **مدیریت سفارشات**: بررسی، تأیید و مدیریت سفارشات
- 👥 **مدیریت کاربران**: مدیریت حساب‌های کاربری و نقش‌ها
- 🎟️ **سیستم تخفیف**: ایجاد و مدیریت کدهای تخفیف
- 🏷️ **مدیریت برچسب‌ها**: ایجاد و مدیریت برچسب‌های محصولات
- 📈 **گزارش‌گیری**: گزارش‌های مالی و فروش

### پنل کاربری (Customer Panel)
- 🔐 **احراز هویت**: ثبت‌نام، ورود و مدیریت حساب کاربری
- 👤 **مدیریت پروفایل**: ویرایش اطلاعات شخصی
- 🛍️ **مرور محصولات**: نمایش، جستجو و فیلتر محصولات
- 🛒 **سبد خرید**: افزودن محصولات به سبد و مدیریت آن
- 📋 **سفارشات**: مشاهده تاریخچه و وضعیت سفارشات
- 💳 **کیف پول**: مدیریت موجودی و تراکنش‌ها
- ⭐ **نظرات و امتیازدهی**: ثبت نظر و امتیازدهی به محصولات

## پیش‌نیازها

- PHP >= 8.1
- Composer
- Laravel 11.x
- SQLite/MySQL/PostgreSQL
- Node.js & NPM (برای assets)

## نصب و راه‌اندازی

### 1. کلون کردن پروژه
```bash
git clone [repository-url]
cd crypto4
```

### 2. نصب dependencies
```bash
composer install
npm install
```

### 3. تنظیمات environment
```bash
cp .env.example .env
php artisan key:generate
```

### 4. تنظیم دیتابیس
فایل `.env` را ویرایش کنید:
```env
DB_CONNECTION=sqlite
DB_DATABASE=/absolute/path/to/database/database.sqlite
```

### 5. اجرای migrations و seeders
```bash
php artisan migrate
php artisan db:seed --class=CryptoEcommerceSeeder
```

### 6. نصب Laravel Sanctum
```bash
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
```

### 7. راه‌اندازی assets
```bash
npm run build
```

### 8. اجرای سرور
```bash
php artisan serve
```

## اطلاعات حساب‌های پیش‌فرض

### حساب مدیر
- **ایمیل**: admin@crypto4.com
- **رمز عبور**: admin123

### حساب مشتری
- **ایمیل**: customer@crypto4.com
- **رمز عبور**: customer123

## API Documentation

مستندات کامل API در فایل `API_DOCUMENTATION.md` موجود است که شامل:
- تمام Endpoints
- نمونه درخواست‌ها و پاسخ‌ها
- راهنمای احراز هویت
- کدهای خطا

## ساختار پروژه

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Admin/          # کنترلرهای پنل مدیریت
│   │   └── Api/            # کنترلرهای API
│   └── Middleware/
│       └── AdminMiddleware.php
├── Models/                 # مدل‌های Eloquent
database/
├── migrations/             # Migration فایل‌ها
└── seeders/               # Seeder فایل‌ها
routes/
├── api.php                # Routes API
├── web.php                # Routes وب
└── admin.php              # Routes پنل مدیریت
```

## مدل‌های دیتابیس

- **User**: کاربران (Admin/Customer)
- **Category**: دسته‌بندی‌های چندسطحی
- **Product**: محصولات رمزارز
- **ProductImage**: تصاویر محصولات
- **ProductAttribute**: ویژگی‌های محصولات
- **Tag**: برچسب‌های محصولات
- **Order**: سفارشات
- **OrderItem**: آیتم‌های سفارش
- **Coupon**: کدهای تخفیف
- **Wallet**: کیف پول کاربران
- **WalletTransaction**: تراکنش‌های مالی
- **Review**: نظرات و امتیازها
- **CartItem**: آیتم‌های سبد خرید

## نمونه کد Flutter

### احراز هویت
```dart
// ثبت‌نام
final response = await http.post(
  Uri.parse('$baseUrl/api/register'),
  headers: {'Content-Type': 'application/json'},
  body: jsonEncode({
    'name': 'نام کاربر',
    'email': 'user@example.com',
    'password': 'password123',
    'password_confirmation': 'password123',
  }),
);

// ورود
final loginResponse = await http.post(
  Uri.parse('$baseUrl/api/login'),
  headers: {'Content-Type': 'application/json'},
  body: jsonEncode({
    'email': 'user@example.com',
    'password': 'password123',
  }),
);
```

### دریافت محصولات
```dart
final response = await http.get(
  Uri.parse('$baseUrl/api/products?page=1&limit=10'),
  headers: {
    'Authorization': 'Bearer $token',
    'Accept': 'application/json',
  },
);
```

### افزودن به سبد خرید
```dart
final response = await http.post(
  Uri.parse('$baseUrl/api/cart'),
  headers: {
    'Authorization': 'Bearer $token',
    'Content-Type': 'application/json',
  },
  body: jsonEncode({
    'product_id': 1,
    'quantity': 2,
  }),
);
```

## امنیت

- احراز هویت با Laravel Sanctum
- Validation کامل ورودی‌ها
- Rate Limiting برای API
- Middleware احراز هویت و نقش
- Hash رمزهای عبور
- CSRF Protection

## نکات مهم توسعه

1. **احراز هویت**: تمام API endpoints نیاز به token دارند (به جز register/login)
2. **Pagination**: لیست محصولات و سفارشات دارای pagination هستند
3. **File Upload**: برای آپلود تصاویر از فرم multipart استفاده کنید
4. **Error Handling**: همیشه response status code و message را بررسی کنید
5. **Rate Limiting**: API دارای محدودیت تعداد درخواست است

## مجوز

این پروژه تحت مجوز MIT منتشر شده است.
