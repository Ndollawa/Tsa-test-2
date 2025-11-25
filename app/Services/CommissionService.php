<?php

namespace App\Services;

use App\Repositories\Contracts\CommissionRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class CommissionService
{
    protected CommissionRepositoryInterface $repo;

    public function __construct(CommissionRepositoryInterface $repo)
    {
        $this->repo = $repo;
    }

    /**
     * Get paginated commission report rows.
     *
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
   public function getReport(array $filters, int $perPage): LengthAwarePaginator
    {
        $query = $this->repo->queryReport($filters);

        return $query->paginate($perPage)->through(function ($row) {
            return [
                'order_id' => $row->order_id,
                'invoice' => $row->invoice,
                'purchaser' => $row->purchaser,
                'distributor' => $row->distributor,
                'referred_distributors' => (int) $row->referred_distributors,
                'order_date' => $row->order_date,
                'order_total' => (float) $row->order_total,
                'percentage' => (float) $row->commission_percentage,
                'commission' => (float) $row->commission,
            ];
        });
    }


    /**
     * Get detailed order row and items
     */

    public function getOrderDetails(int $orderId): ?array
    {
        $order = $this->repo->findByOrderId($orderId);
        if (!$order) return null;

        $items = $this->repo->getOrderItems($orderId);

        return [
            'order' => $order,
            'items' => $items,
        ];
    }
}

