<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;


return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
         // v_commission_report
        DB::statement('DROP VIEW IF EXISTS v_commission_report');
        DB::statement("
            CREATE VIEW v_commission_report AS
            SELECT
              o.id AS order_id,
              o.invoice_number AS invoice,
              o.order_date,
              p.id AS purchaser_id,
              CONCAT(p.first_name, ' ', p.last_name) AS purchaser,
              r.id AS referrer_id,
              CONCAT(r.first_name, ' ', r.last_name) AS referrer_name,
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
              (
                SELECT COUNT(*)
                FROM users u2
                JOIN user_category uc2 ON uc2.user_id = u2.id
                JOIN categories c2 ON c2.id = uc2.category_id
                WHERE u2.referred_by = r.id
                  AND c2.name = 'Distributor'
                  AND u2.enrolled_date <= o.order_date
              ) AS referred_distributors,
              COALESCE((
                SELECT SUM(p2.price * oi2.quantity)
                FROM order_items oi2
                JOIN products p2 ON p2.id = oi2.product_id
                WHERE oi2.order_id = o.id
              ), 0) AS order_total,
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
              ROUND(
                COALESCE((
                  SELECT SUM(p2.price * oi2.quantity)
                  FROM order_items oi2
                  JOIN products p2 ON p2.id = oi2.product_id
                  WHERE oi2.order_id = o.id
                ), 0) *
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
                END), 2) AS commission
            FROM orders o
            LEFT JOIN users p ON p.id = o.purchaser_id
            LEFT JOIN users r ON r.id = p.referred_by
        ");

        // v_top_distributors
        DB::statement('DROP VIEW IF EXISTS v_top_distributors');
        DB::statement("
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
            GROUP BY r_user.id, r_user.first_name, r_user.last_name
        ");


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

        DB::statement('DROP VIEW IF EXISTS v_commission_report');
        DB::statement('DROP VIEW IF EXISTS v_top_distributors');

    }
};
