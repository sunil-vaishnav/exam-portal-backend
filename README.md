# Exam Form & Payment Portal – Backend (Laravel)

## Features
- User Registration & Login (JWT)
- Role-based Access (User/Admin)
- Exam Form CRUD
- Submissions
- Payment Integration (Stripe)
- PDF Receipt Generation

## Setup
1. Clone repo
2. Run `composer install`
2. Run `php artisan storage:link`
3. Setup `.env`
4. Database Connection in env file
5. Run migrations: `php artisan migrate`
6. Set Razorpay key in env : `RAZORPAY_KEY` and `RAZORPAY_SECRET`

## Package install

1. Run `composer require tymon/jwt-auth` 
2. Run `composer require razorpay/razorpay` 
3. Run `composer require barryvdh/laravel-dompdf` 

# Publish vendor files where required

1.`php artisan vendor:publish --provider="Tymon\JWTAuth\Providers\LaravelServiceProvider"` 
2. `php artisan vendor:publish --provider="Barryvdh\DomPDF\ServiceProvider"` 
3.`php artisan jwt:secret"` 

## API Docs
Postman collection available in `/docs/Exam Portal.postman_collection.json`

## Sample Receipt 
Sample Receipt Pdf file available in `/public/storage/receipts/receipt_3.pdf`
