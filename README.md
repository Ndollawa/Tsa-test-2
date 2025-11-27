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


USE nxm_assessment_2023;

DROP VIEW IF EXISTS v_commission_report;
CREATE VIEW v_commission_report AS
SELECT
  o.id AS order_id,
  o.invoice_number AS invoice,
  o.order_date,

  -- purchaser
  p.id AS purchaser_id,
  CONCAT(p.first_name, ' ', p.last_name) AS purchaser,

  -- referrer user (may be NULL)
  r.id AS referrer_id,
  CONCAT(r.first_name, ' ', r.last_name) AS referrer_name,

  -- only mark distributor when referrer is a Distributor (via user_category + categories)
  (CASE
     WHEN EXISTS (
       SELECT 1
       FROM user_category ucx
       JOIN categories cx ON cx.id = ucx.category_id
       WHERE ucx.user_id = r.id AND cx.name = 'Distributor'
     ) THEN r.id
     ELSE NULL
  END) AS distributor_id,

  (CASE
     WHEN EXISTS (
       SELECT 1
       FROM user_category ucx
       JOIN categories cx ON cx.id = ucx.category_id
       WHERE ucx.user_id = r.id AND cx.name = 'Distributor'
     ) THEN CONCAT(r.first_name,' ',r.last_name)
     ELSE NULL
  END) AS distributor,

  /* number of referred distributors the referrer had by order_date */
  (
    SELECT COUNT(*)
    FROM users u2
    JOIN user_category uc2 ON uc2.user_id = u2.id
    JOIN categories c2 ON c2.id = uc2.category_id
    WHERE u2.referred_by = r.id
      AND c2.name = 'Distributor'
      AND u2.enrolled_date <= o.order_date
  ) AS referred_distributors,

  /* order total using product.price * quantity */
  COALESCE((
    SELECT SUM(p2.price * oi2.quantity)
    FROM order_items oi2
    JOIN products p2 ON p2.id = oi2.product_id
    WHERE oi2.order_id = o.id
  ), 0) AS order_total,

  /* commission percentage tiers you supplied:
     0-4 -> 5%
     5-10 -> 10%
     11-20 -> 15%
     21-29 -> 20%
     30+ -> 30%
     If referrer is not a distributor => 0%
  */
  (CASE
     WHEN NOT EXISTS (
       SELECT 1
       FROM user_category ucx
       JOIN categories cx ON cx.id = ucx.category_id
       WHERE ucx.user_id = r.id AND cx.name = 'Distributor'
     ) THEN 0.00
     WHEN (
       SELECT COUNT(*)
       FROM users u2
       JOIN user_category uc2 ON uc2.user_id = u2.id
       JOIN categories c2 ON c2.id = uc2.category_id
       WHERE u2.referred_by = r.id
         AND c2.name = 'Distributor'
         AND u2.enrolled_date <= o.order_date
     ) BETWEEN 30 AND 999999 THEN 0.30
     WHEN (
       SELECT COUNT(*)
       FROM users u2
       JOIN user_category uc2 ON uc2.user_id = u2.id
       JOIN categories c2 ON c2.id = uc2.category_id
       WHERE u2.referred_by = r.id
         AND c2.name = 'Distributor'
         AND u2.enrolled_date <= o.order_date
     ) BETWEEN 21 AND 29 THEN 0.20
     WHEN (
       SELECT COUNT(*)
       FROM users u2
       JOIN user_category uc2 ON uc2.user_id = u2.id
       JOIN categories c2 ON c2.id = uc2.category_id
       WHERE u2.referred_by = r.id
         AND c2.name = 'Distributor'
         AND u2.enrolled_date <= o.order_date
     ) BETWEEN 11 AND 20 THEN 0.15
     WHEN (
       SELECT COUNT(*)
       FROM users u2
       JOIN user_category uc2 ON uc2.user_id = u2.id
       JOIN categories c2 ON c2.id = uc2.category_id
       WHERE u2.referred_by = r.id
         AND c2.name = 'Distributor'
         AND u2.enrolled_date <= o.order_date
     ) BETWEEN 5 AND 10 THEN 0.10
     WHEN (
       SELECT COUNT(*)
       FROM users u2
       JOIN user_category uc2 ON uc2.user_id = u2.id
       JOIN categories c2 ON c2.id = uc2.category_id
       WHERE u2.referred_by = r.id
         AND c2.name = 'Distributor'
         AND u2.enrolled_date <= o.order_date
     ) BETWEEN 0 AND 4 THEN 0.05
     ELSE 0.00
  END) AS commission_percentage,

  /* commission amount — if referrer is not a distributor this will be zero */
  ROUND(
    COALESCE((
      SELECT SUM(p2.price * oi2.quantity)
      FROM order_items oi2
      JOIN products p2 ON p2.id = oi2.product_id
      WHERE oi2.order_id = o.id
    ), 0)
    *
    (CASE
       WHEN NOT EXISTS (
         SELECT 1
         FROM user_category ucx
         JOIN categories cx ON cx.id = ucx.category_id
         WHERE ucx.user_id = r.id AND cx.name = 'Distributor'
       ) THEN 0.00
       WHEN (
         SELECT COUNT(*)
         FROM users u2
         JOIN user_category uc2 ON uc2.user_id = u2.id
         JOIN categories c2 ON c2.id = uc2.category_id
         WHERE u2.referred_by = r.id
           AND c2.name = 'Distributor'
           AND u2.enrolled_date <= o.order_date
       ) BETWEEN 30 AND 999999 THEN 0.30
       WHEN (
         SELECT COUNT(*)
         FROM users u2
         JOIN user_category uc2 ON uc2.user_id = u2.id
         JOIN categories c2 ON c2.id = uc2.category_id
         WHERE u2.referred_by = r.id
           AND c2.name = 'Distributor'
           AND u2.enrolled_date <= o.order_date
       ) BETWEEN 21 AND 29 THEN 0.20
       WHEN (
         SELECT COUNT(*)
         FROM users u2
         JOIN user_category uc2 ON uc2.user_id = u2.id
         JOIN categories c2 ON c2.id = uc2.category_id
         WHERE u2.referred_by = r.id
           AND c2.name = 'Distributor'
           AND u2.enrolled_date <= o.order_date
       ) BETWEEN 11 AND 20 THEN 0.15
       WHEN (
         SELECT COUNT(*)
         FROM users u2
         JOIN user_category uc2 ON uc2.user_id = u2.id
         JOIN categories c2 ON c2.id = uc2.category_id
         WHERE u2.referred_by = r.id
           AND c2.name = 'Distributor'
           AND u2.enrolled_date <= o.order_date
       ) BETWEEN 5 AND 10 THEN 0.10
       WHEN (
         SELECT COUNT(*)
         FROM users u2
         JOIN user_category uc2 ON uc2.user_id = u2.id
         JOIN categories c2 ON c2.id = uc2.category_id
         WHERE u2.referred_by = r.id
           AND c2.name = 'Distributor'
           AND u2.enrolled_date <= o.order_date
       ) BETWEEN 0 AND 4 THEN 0.05
       ELSE 0.00
    END)
  , 2) AS commission

FROM orders o
LEFT JOIN users p ON p.id = o.purchaser_id
LEFT JOIN users r ON r.id = p.referred_by;

DROP VIEW IF EXISTS v_top_distributors;
CREATE VIEW v_top_distributors AS
SELECT
    d.id AS distributor_id,
    CONCAT(d.first_name, ' ', d.last_name) AS distributor_name,

    /* Sum of all product purchases made by referred purchasers */
    COALESCE(SUM(p.price * oi.quantity), 0) AS total_sales,

    RANK() OVER (
        ORDER BY COALESCE(SUM(p.price * oi.quantity), 0) DESC
    ) AS rank

FROM users d

/* Only include users who are categorized as Distributors */
JOIN user_category uc ON uc.user_id = d.id
JOIN categories c ON c.id = uc.category_id AND c.name = 'Distributor'

/* Purchasers referred by this distributor */
LEFT JOIN users purchaser ON purchaser.referred_by = d.id

/* Orders made by those purchasers */
LEFT JOIN orders o ON o.purchaser_id = purchaser.id

/* Items inside each order */
LEFT JOIN order_items oi ON oi.order_id = o.id
LEFT JOIN products p ON p.id = oi.product_id

GROUP BY d.id, d.first_name, d.last_name;


SELECT * FROM v_top_distributors LIMIT 300;


SELECT invoice, order_total, referred_distributors, commission_percentage, commission
FROM v_commission_report
WHERE invoice IN ('ABC4170','ABC6931','ABC23352','ABC3010','ABC19323');

SELECT distributor_name, total_sales FROM v_top_distributors
WHERE distributor_name IN (
  'Demario Purdy',
  'Floy Miller',
  'Loy Schamberger',
  'Chaim Kuhn',
  'Eliane Bogisich'
);
SELECT invoice, order_total, commission_percentage, commission
FROM v_commission_report
WHERE invoice = 'ABC19323';


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
