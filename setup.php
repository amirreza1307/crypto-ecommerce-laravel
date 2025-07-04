#!/usr/bin/env php
<?php

/*
|--------------------------------------------------------------------------
| Crypto E-commerce Setup Script
|--------------------------------------------------------------------------
|
| This script helps set up the cryptocurrency e-commerce Laravel application.
| It will check requirements, run migrations, seed data, and provide setup info.
|
*/

echo "\n";
echo "🚀 Crypto E-commerce Setup Script\n";
echo "==================================\n\n";

// Check if we're in a Laravel project
if (!file_exists('artisan')) {
    echo "❌ Error: This script must be run from the Laravel project root directory.\n";
    exit(1);
}

echo "✅ Laravel project detected\n";

// Check PHP version
$phpVersion = phpversion();
echo "📋 PHP Version: $phpVersion\n";

if (version_compare($phpVersion, '8.1.0', '<')) {
    echo "❌ Error: PHP 8.1 or higher is required\n";
    exit(1);
}

echo "✅ PHP version is compatible\n";

// Check if .env file exists
if (!file_exists('.env')) {
    echo "📋 Creating .env file from .env.example...\n";
    if (file_exists('.env.example')) {
        copy('.env.example', '.env');
        echo "✅ .env file created\n";
    } else {
        echo "❌ Error: .env.example file not found\n";
        exit(1);
    }
} else {
    echo "✅ .env file exists\n";
}

// Check if APP_KEY is set
$envContent = file_get_contents('.env');
if (strpos($envContent, 'APP_KEY=') === false || strpos($envContent, 'APP_KEY=base64:') === false) {
    echo "📋 Generating application key...\n";
    exec('php artisan key:generate', $output, $returnCode);
    if ($returnCode === 0) {
        echo "✅ Application key generated\n";
    } else {
        echo "❌ Error generating application key\n";
        exit(1);
    }
} else {
    echo "✅ Application key is set\n";
}

// Check database connection
echo "📋 Checking database connection...\n";
exec('php artisan migrate:status 2>&1', $output, $returnCode);
if ($returnCode !== 0) {
    echo "❌ Database connection failed. Please check your .env database configuration.\n";
    echo "Current database configuration:\n";
    echo "- DB_CONNECTION: " . (getenv('DB_CONNECTION') ?: 'Not set') . "\n";
    echo "- DB_DATABASE: " . (getenv('DB_DATABASE') ?: 'Not set') . "\n";
    exit(1);
}

echo "✅ Database connection successful\n";

// Run migrations
echo "📋 Running database migrations...\n";
exec('php artisan migrate --force', $output, $returnCode);
if ($returnCode === 0) {
    echo "✅ Migrations completed\n";
} else {
    echo "❌ Error running migrations\n";
    exit(1);
}

// Seed database
echo "📋 Seeding database with sample data...\n";
exec('php artisan db:seed --class=CryptoEcommerceSeeder --force', $output, $returnCode);
if ($returnCode === 0) {
    echo "✅ Database seeded successfully\n";
} else {
    echo "❌ Error seeding database\n";
    exit(1);
}

// Clear cache
echo "📋 Clearing application cache...\n";
exec('php artisan config:clear');
exec('php artisan cache:clear');
exec('php artisan route:clear');
echo "✅ Cache cleared\n";

echo "\n";
echo "🎉 Setup completed successfully!\n";
echo "================================\n\n";

echo "📋 Default Admin Account:\n";
echo "   Email: admin@crypto4.com\n";
echo "   Password: admin123\n\n";

echo "📋 Default Customer Account:\n";
echo "   Email: customer@crypto4.com\n";
echo "   Password: customer123\n\n";

echo "🌐 API Endpoints:\n";
echo "   Base URL: http://localhost:8000/api\n";
echo "   Admin Panel: http://localhost:8000/admin\n";
echo "   Documentation: See API_DOCUMENTATION.md\n\n";

echo "🚀 To start the development server:\n";
echo "   php artisan serve\n\n";

echo "🧪 To run tests:\n";
echo "   php artisan test\n\n";

echo "📚 For complete documentation, check:\n";
echo "   - README.md (Project setup and overview)\n";
echo "   - API_DOCUMENTATION.md (API documentation)\n\n";

echo "Happy coding! 🚀\n\n";
