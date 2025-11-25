<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Contracts\OrderRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class EloquentOrderRepository implements OrderRepositoryInterface
{
    public function find(int $id)
    {
        return DB::table('orders')->where('id', $id)->first();
    }

    public function getOrderItems(int $orderId): Collection
    {
        return DB::table('order_items as oi')
            ->select('oi.*', 'p.name as product_name')
            ->leftJoin('products as p', 'p.id', '=', 'oi.product_id')
            ->where('oi.order_id', $orderId)
            ->get();
    }

    /**
     * Returns a query builder based on the commission view; caller can paginate
     */
    public function queryCommissionReport(array $filters = [])
    {
        $q = DB::table('v_commission_report');

        if (!empty($filters['invoice'])) {
            $q->where('invoice', 'like', '%' . $filters['invoice'] . '%');
        }

        if (!empty($filters['distributor'])) {
            $term = $filters['distributor'];
            $q->where(function ($sub) use ($term) {
                $sub->where('distributor_id', $term)
                    ->orWhere('distributor', 'like', '%' . $term . '%');
            });
        }

        if (!empty($filters['date_from'])) {
            $q->whereDate('order_date', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $q->whereDate('order_date', '<=', $filters['date_to']);
        }

        return $q;
    }
}
