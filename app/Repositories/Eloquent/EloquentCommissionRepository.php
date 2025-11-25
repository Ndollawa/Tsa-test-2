<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Contracts\CommissionRepositoryInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class EloquentCommissionRepository implements CommissionRepositoryInterface
{
    public function queryReport(array $filters = []): \Illuminate\Database\Query\Builder
    {
        $q = DB::table('v_commission_report');

        if (!empty($filters['invoice'])) {
            $q->where('invoice', 'like', '%' . $filters['invoice'] . '%');
        }

        if (!empty($filters['distributor'])) {
            $term = $filters['distributor'];
            // numeric -> search by id, otherwise search name
            if (is_numeric($term)) {
                $q->where('distributor_id', intval($term));
            } else {
                $q->where('distributor', 'like', '%' . $term . '%');
            }
        }

        if (!empty($filters['date_from'])) {
            $q->whereDate('order_date', '>=', $filters['date_from']);
        }
        if (!empty($filters['date_to'])) {
            $q->whereDate('order_date', '<=', $filters['date_to']);
        }

        return $q->orderByDesc('order_date');
    }

    public function findByOrderId(int $orderId): ?object
    {
        return DB::table('v_commission_report')->where('order_id', $orderId)->first();
    }

    public function getOrderItems(int $orderId): Collection
    {
        // order_items join products to produce sku, product_name, price, quantity, total
        return DB::table('order_items as oi')
        ->select([
            'oi.product_id',
            'p.sku',
            'p.name as product_name',
            'p.price as price',
            'oi.quantity',
            DB::raw('p.price * oi.quantity as total'),
        ])
        ->leftJoin('products as p', 'p.id', '=', 'oi.product_id')
        ->where('oi.order_id', $orderId)
        ->get();
    }
}
