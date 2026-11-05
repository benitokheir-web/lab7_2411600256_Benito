# Lab6_2411600256_Benito — Real-Time Inventory Tracking System

Laravel 12 project extended from Lab 5. Includes product CRUD, authentication, inventory transaction audit trail, stock-in/out, physical stock adjustment, low-stock database notifications, queue processing, reports, and dashboard AJAX polling every 30 seconds.

## Requirements
- PHP 8.2+
- Composer
- XAMPP / MySQL
- VS Code
- Browser

## Setup on Windows / XAMPP

1. Extract this folder into `C:\xampp\htdocs\`.
2. Open `Lab6_2411600256_Benito` in VS Code.
3. Start Apache and MySQL in XAMPP.
4. Create a database named `hardware_inventory` in phpMyAdmin.
5. Copy `.env.example` to `.env` (do not use or upload a real `.env` file to GitHub). Confirm these settings:
   - `DB_DATABASE=hardware_inventory`
   - `DB_USERNAME=root`
   - `DB_PASSWORD=` (blank for default XAMPP, unless your MySQL has a password)
   - `QUEUE_CONNECTION=database`
6. In the VS Code terminal, run:
   ```bash
   composer install
   php artisan key:generate
   php artisan migrate:fresh --seed
   ```
   **Warning:** `migrate:fresh` deletes existing tables in the configured database. Use only for a new/test database or after backing up data. If preserving Lab 5 data, do not run this command; existing Lab 5 databases need a carefully planned schema upgrade because this project expands transaction types and columns.
7. Start the app:
   ```bash
   php artisan serve
   ```
8. In a second terminal, start the queue worker so low-stock notifications are processed:
   ```bash
   php artisan queue:work
   ```
9. Open `http://127.0.0.1:8000`.

## Demo login
- Email: `admin@example.com`
- Password: `password`

Use only for local class demonstration. Change demo credentials before deploying.

## Features
- Login / logout
- Product CRUD and stock status helpers
- Stock-in, stock-out, and physical-count adjustments
- InventoryTransaction model with Product and User relationships
- Observer updates product quantity inside a database transaction
- Validation blocks stock-out beyond available stock
- User, timestamp, reference, customer, and notes audit fields
- Database low-stock notifications with unread count and mark-as-read
- Database queue worker for background notification processing
- API JSON endpoints under `/api/dashboard/stats`, `/api/products/low-stock`, `/api/transactions/recent`
- Dashboard AJAX polling every 30 seconds
- Transaction filters and pagination
- Product transaction history and reports
- Bootstrap-based dashboard styling

## Notes
- The observer triggers when a transaction is created. Do not separately change product quantity in the controller, or stock could be counted twice.
- The current demo notifies all users because the Lab 5 schema has no role/permission field to identify administrators. Add roles before using in a multi-user production environment.
- Notifications currently use Laravel's database channel. Email delivery requires configuring mail credentials and adding the `mail` channel.
- Keep `.env` private; never commit it to GitHub.
