# **Themabinti Services Hub - Laravel API**

![Themabinti Services Hub Logo/Banner](https://via.placeholder.com/1200x300/8A2BE2/FFFFFF?text=Themabinti+Services+Hub+-+Laravel+API)

## **Project Overview**

The Themabinti Services Hub Laravel API is the robust, scalable backend powering an inclusive beauty, health, and lifestyle services marketplace for Kenya. It facilitates connections between service providers (sellers) and customers, offering tiered seller packages, secure M-Pesa payment integration, comprehensive service management, and an extensive admin management system.

This backend is designed as a RESTful API, providing endpoints for authentication, user and service management, appointments, payments, admin oversight, and more, all while adhering to modern Laravel best practices.

## **Features**

*   **User Management:**
    *   Flexible user roles: `customer`, `seller`, `admin`.
    *   Secure registration, login, and logout with Laravel Sanctum API tokens.
    *   Email verification for account activation.
    *   Profile management: update personal details, upload profile image.
    *   Admin controls to activate/deactivate, soft delete, restore, or permanently delete user accounts.
*   **Seller Packages & Monetization:**
    *   Tiered seller packages (`basic`, `standard`, `premium`) with varying service and media upload limits.
    *   M-Pesa STK Push integration for package payments (registration, upgrade, renewal).
    *   Robust payment status polling to ensure account activation/upgrades only occur upon verified payment.
    *   Dynamic package pricing, configurable by administrators.
*   **Service Management:**
    *   CRUD operations for services by authenticated sellers (within package limits).
    *   Media file uploads (images, videos) per service.
    *   Public listing with category, location, price, and keyword filtering.
    *   Admin moderation of all services (activate/deactivate).
*   **Appointment System:**
    *   Real-time booking of services by customers.
    *   Appointment status management (`pending`, `confirmed`, `completed`, `cancelled`) by customers and sellers.
    *   Automated email notifications for new bookings and status changes.
    *   Public endpoint for general appointment inquiries (admin-handled).
*   **Admin Management System:**
    *   Dedicated API endpoints for comprehensive platform oversight.
    *   Manage users, services, appointments, payments, contacts, and blog content.
    *   Configurable global settings (site name, support email, package prices, M-Pesa credentials).
    *   Ability to create/manage admin-owned services.
*   **Content Management (Blog):**
    *   CRUD operations for blog posts by administrators.
    *   Public access to published blog content.
*   **Contact & Support:**
    *   Public contact form for general inquiries.
    *   Admin interface to view and respond to contact messages.
*   **Robust & Scalable Architecture:**
    *   Laravel Queues (Redis-backed) for asynchronous tasks (emails, M-Pesa callbacks, media processing).
    *   Redis Caching for performance.
    *   Laravel Policies for granular authorization.
    *   Middleware for authentication, role-based access, and API throttling.

## **Tech Stack**

*   **Backend Framework:** Laravel 11.x
*   **Database:** MySQL 8.0+
*   **Authentication:** Laravel Sanctum (API Tokens)
*   **Frontend (Decoupled):** React.js (This API serves a separate React frontend)
*   **Payment Gateway:** M-Pesa STK Push API (Safaricom Daraja)
*   **File Storage:** Laravel Storage (local disk)
*   **Queue System:** Laravel Queues (Redis)
*   **Caching:** Redis Cache
*   **HTTP Client:** GuzzleHttp
*   **PHP Version:** PHP 8.2+ (PHP 8.3 recommended)

## **Installation & Setup**

Follow these steps to get the Themabinti Services Hub Laravel API up and running on your local machine or server.

### **1. Clone the Repository**

```bash
git clone https://github.com/your-username/themabinti-laravel-api.git # Replace with actual repo URL
cd themabinti-laravel-api
```

### **2. Install PHP Dependencies**

```bash
composer install
```

### **3. Environment Configuration**

Copy the example environment file and configure your settings:

```bash
cp .env.example .env
```

Open `.env` and update the following variables:

*   **`APP_URL`**: Set to the absolute URL of your Laravel API backend (e.g., `http://localhost:8000` for local `php artisan serve`, or `http://themabinti.test` for Laragon, or `https://api.themabinti.com` for production). **Crucial for correct image URLs.**
*   **Database Credentials:**
    ```env
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=themabinti_hub
    DB_USERNAME=root
    DB_PASSWORD=
    ```
*   **M-Pesa API Credentials:** Obtain your **Live (or Sandbox for testing)** credentials from the [Safaricom Daraja Portal](https://developer.safaricom.co.ke/).
    ```env
    MPESA_CONSUMER_KEY=your_live_or_sandbox_consumer_key
    MPESA_CONSUMER_SECRET=your_live_or_sandbox_consumer_secret
    MPESA_PASSKEY=your_live_or_sandbox_passkey
    MPESA_SHORTCODE=your_live_or_sandbox_shortcode # Your Pay Bill/Till Number
    MPESA_ENVIRONMENT=sandbox # or production
    MPESA_CALLBACK_URL=https://your_public_url/api/mpesa/callback # Crucial: Must be a publicly accessible HTTPS URL
    ```
    *   **For local M-Pesa testing:** Use `ngrok http 8000` (or your Laravel port) to get a public URL for `MPESA_CALLBACK_URL`. Remember to update this in `.env` and run `php artisan optimize:clear`.
*   **Mailer Configuration:**
    ```env
    MAIL_MAILER=smtp
    MAIL_HOST=127.0.0.1 # For local Mailpit; for production, use your SMTP host (e.g., smtp.sendgrid.net, mail.yourdomain.com)
    MAIL_PORT=1025     # For local Mailpit; for production, typically 587 (TLS) or 465 (SSL)
    MAIL_USERNAME=null # Your SMTP username (e.g., 'apikey' for SendGrid, or your email address)
    MAIL_PASSWORD=null # Your SMTP password (e.g., SendGrid API key, or email password)
    MAIL_ENCRYPTION=null # For local Mailpit; for production, 'tls' or 'ssl'
    MAIL_FROM_ADDRESS="no-reply@themabinti.com" # Use a valid email
    MAIL_FROM_NAME="${APP_NAME}"
    ```
*   **Redis Configuration:**
    ```env
    REDIS_CLIENT=phpredis # Requires PHP Redis extension
    REDIS_HOST=127.0.0.1
    REDIS_PASSWORD=null
    REDIS_PORT=6379
    QUEUE_CONNECTION=redis
    CACHE_STORE=redis
    ```
    *   **Ensure PHP Redis Extension is Enabled:** This is critical. For Laragon, right-click tray icon > PHP > Extensions > `redis.dll`. For CPanel, check "Select PHP Version" for `redis` extension.

### **4. Generate Application Key**

```bash
php artisan key:generate
```

### **5. Database Migrations & Seeding**

Run migrations to create database tables and seed with initial data:

```bash
php artisan migrate:fresh --seed
```
*   This command will reset your database and populate it with test users, services, etc.
*   **Default Admin Credentials:** `admin@themabinti.com` / `password`
*   **Default Seller Credentials:** `basic.seller@themabinti.com` / `password`, `standard.seller@themabinti.com` / `password`, `premium.seller@themabinti.com` / `password`

### **6. Create Storage Symlink**

This makes uploaded media files publicly accessible.

```bash
php artisan storage:link
```
*   **On CPanel without SSH:** This might fail. You'll need to use a temporary admin API route or contact hosting support to create the symlink from `public/storage` to `../storage/app/public`.

### **7. Clear Laravel Caches**

Always run this after `.env` changes, code updates, or `composer install`.

```bash
php artisan optimize:clear
```
*   **On CPanel without SSH:** Use the temporary admin API route: `POST /api/admin/clear-cache` (remember to delete the route after use!).

### **8. Start Queue Worker**

Laravel queues handle emails, M-Pesa callbacks, etc.

```bash
php artisan queue:work
```
*   **For Production (CPanel):** You typically set up a cron job to run the scheduler, which then executes queued jobs.
    `* * * * * /path/to/your/php83/bin/php /path/to/your/laravel/project/artisan schedule:run >> /dev/null 2>&1`
    *(Confirm the exact PHP 8.3 CLI path with your hosting provider.)*

## **API Endpoints**

The Themabinti Services Hub API is a comprehensive RESTful interface. Refer to the **Frontend API Consumption Guide** documentation for detailed requests, responses, and error handling for each endpoint.

### **Key API Route Groups:**

*   `/api/register`, `/api/login`, `/api/logout`, `/api/user`, `/api/user/password`, `/api/user/account`
*   `/api/services`, `/api/services/{id}`, `/api/services/{id}/media`
*   `/api/appointments`, `/api/appointments/{id}`
*   `/api/payments/initiate`, `/api/payments/status/{id}`, `/api/payments/history`
*   `/api/public/settings/package-prices`
*   `/api/settings` (User-specific settings)
*   `/api/general-bookings` (Public general appointment requests)
*   `/api/admin/*` (All admin-specific routes for user/service/payment/settings management)

## **CORS Configuration**

CORS is handled by Laravel's built-in `Illuminate\Http\Middleware\HandleCors` middleware.

*   **Configuration File:** `config/cors.php`
*   **Crucial Setting:** Ensure `'allowed_origins'` in `config/cors.php` includes the exact URL(s) of your React frontend development server (e.g., `http://localhost:32100`) and your production frontend domain (e.g., `https://themabinti.com`).

## **Frontend Integration**

This API is designed to be consumed by a separate React.js frontend application. The frontend should:

*   Store `access_token` in `localStorage`.
*   Include `Authorization: Bearer [token]` header for authenticated requests.
*   Handle `401 Unauthorized` responses by redirecting to `/login`.
*   Manage `X-CSRF-TOKEN` for non-GET requests (Axios generally handles this with `withCredentials: true` after a `sanctum/csrf-cookie` call).
*   Parse API responses, manage loading states, display errors/success messages (toasts).
*   Implement polling for M-Pesa payment status.

## **Security Considerations**

*   **API Tokens:** Laravel Sanctum provides token-based authentication. Handle tokens securely on the frontend.
*   **Input Validation:** All incoming data is rigorously validated using Laravel Form Requests.
*   **Authorization:** Laravel Policies (`ServicePolicy`, `AppointmentPolicy`, etc.) and middleware (`is_admin`, `is_seller`, `email_verified`) enforce granular access control.
*   **Soft Deletes:** User and other critical data are soft-deleted by default, preserving data integrity.
*   **Sensitive Settings:** M-Pesa API keys are designed to be managed by the `AdminSettingsService` but reside in the `settings` table. For extremely high-security production environments, direct `.env` management is often preferred for such keys.
*   **Temporary Routes:** Remember to **remove** any temporary routes (like `/admin/clear-cache`) from `routes/api.php` after deployment and use.

## **Contribution**

Feel free to contribute to the Themabinti Services Hub Laravel API by submitting bug reports or feature requests!

## **License**

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT). This project extends that with specific features.

---