# Naxum Assessment Project - Laravel 12

This project is a Laravel-based application that tracks **orders, distributors, commissions**, and related reports. It uses database views for efficient aggregation and ranking.  

---

## Table of Contents

1. [Requirements](#requirements)  
2. [Installation](#installation)  
3. [Environment Setup](#environment-setup)  
4. [Database Setup](#database-setup)  
5. [Views & Migrations](#views--migrations)  
6. [Running the Application](#running-the-application)  
7. [Running Tests](#running-tests)  
8. [Project Structure](#project-structure)  
9. [Notes & Best Practices](#notes--best-practices)  

---

## Requirements

- PHP >= 8.2  
- Composer  
- Laravel 12.x  
- MySQL >= 8.0 (for window functions like `RANK()`)  
- Node.js >= 18  
- NPM/Yarn  

---

## Installation

Clone the repository:

```bash
git clone <https://github.com/Ndollawa/Tsa-test-2.git>
cd <repo-folder>
```

Install PHP dependencies:

```bash
./vendor/bin/sail composer install
```

Install frontend dependencies:

```bash
./vendor/bin/sail npm install
# or yarn install
```

Build assets:

```bash
./vendor/bin/sail npm run dev
# or yarn dev
```

---

## Environment Setup

Copy the example environment file:

```bash
cp .env.example .env
```

Edit `.env` to configure your database and other settings:

```dotenv
APP_NAME=NxMAssessment
APP_ENV=local
APP_KEY=base64:GENERATE_KEY
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=mariadb
DB_HOST=mariadb
DB_PORT=3306
DB_DATABASE=nxm_assessment_2023
DB_USERNAME=sail
DB_PASSWORD=
```

Generate application key:

```bash
./vendor/bin/sail php artisan key:generate
```

---

## Database Setup

1. Create the database:

```sql
CREATE DATABASE nxm_assessment_2023;
```
or

```bash
./vendor/bin/sail exec -i mariadb sh -c "mariadb -u root -p<PASSWORD> -e 'CREATE DATABASE nxm_assessment_2023;'"

```

2. Import the SQL file:
import the nxm_assessment_2023.sql in the root dir into the nxm_assessment_2023 database

```bash
./vendor/bin/sail exec -i mariadb sh -c "mariadb -u root -p nxm_assessment_2023 < /nxm_assessment_2023.sql"
```


3. (Optional) Seed sample data:


```bash
./vendor/bin/sail php artisan migrate
```

4. **Views** are created via migrations. If manually updating, ensure the following views exist:

- `v_commission_report` – computes order totals, commission percentages, and commission amounts per invoice.  
- `v_top_distributors` – computes total sales and rank per distributor.

Example `v_top_distributors` SQL:

```sql
DROP VIEW IF EXISTS v_top_distributors;
CREATE VIEW v_top_distributors AS
SELECT
    r_user.id AS distributor_id,
    CONCAT(r_user.first_name, ' ', r_user.last_name) AS distributor_name,
    SUM(p.price * oi.quantity) AS total_sales,
    RANK() OVER (ORDER BY SUM(p.price * oi.quantity) DESC) AS rank
FROM orders o
JOIN users purchaser ON purchaser.id = o.purchaser_id
JOIN users ref ON ref.id = purchaser.referred_by
JOIN user_category uc ON uc.user_id = ref.id
JOIN categories c ON c.id = uc.category_id AND c.name = 'Distributor'
JOIN users r_user ON r_user.id = ref.id
JOIN order_items oi ON oi.order_id = o.id
JOIN products p ON p.id = oi.product_id
GROUP BY r_user.id, r_user.first_name, r_user.last_name;
```

---

## Views & Migrations

To manage views in Laravel migrations:

```bash
php artisan make:migration create_distributor_views
```

In the migration file:

```php
public function up(): void
{
    DB::statement('DROP VIEW IF EXISTS v_top_distributors');
    DB::statement('CREATE VIEW v_top_distributors AS ...');

    DB::statement('DROP VIEW IF EXISTS v_commission_report');
    DB::statement('CREATE VIEW v_commission_report AS ...');
}

public function down(): void
{
    DB::statement('DROP VIEW IF EXISTS v_top_distributors');
    DB::statement('DROP VIEW IF EXISTS v_commission_report');
}
```

---

## Running the Application

Start the Laravel server:

```bash
./vendor/bin/sail php artisan serve
```

Access the app at:

```
http://localhost:8000 or  http://localhost
```

---

## Running Tests

This project uses **Pest** for testing. Run all tests:

```bash
vendor/bin/pest
```

Or run a specific test file:

```bash
vendor/bin/pest tests/Feature/TopDistributorTest.php
vendor/bin/pest tests/Feature/CommissionReportTest.php
```

---

## Project Structure

```
app/
├── Http/Controllers/
|    ├──  CommissionReportController.php
|    ├──  TopDistributorController.php
├── Services/
|   ├── TopDistributorService.php
|   ├── CommissionReportService.php
├── Repositories/
│   |── Eloquent/EloquentTopDistributorRepository.php
│   └── Eloquent/EloquentCommisionReportRepository.php
database/
├── migrations/
│   └── <view migrations>.php
tests/
├── Feature/TopDistributorTest.php
├── Feature/CommissionReportTest.php
resources/
├── js/Pages/reports/CommissionReport.vue
└── js/Pages/reports/TopDistributors.vue
```

- **Controllers**: Handle HTTP requests and call services.  
- **Services**: Contains business logic (top distributors, commissions).  
- **Repositories**: Abstract DB queries (views, aggregates).  
- **Views**: MySQL views handle aggregation for performance.  

---


---

## License

This project is open-source and avai
