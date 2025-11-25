<?php

namespace App\Repositories\Contracts;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface CommissionRepositoryInterface
{
    /**
     * Query the commission view with optional filters. Returns a query builder.
     *
     * Filters:
     *  - invoice (string)
     *  - distributor (string or id)
     *  - date_from (YYYY-MM-DD)
     *  - date_to (YYYY-MM-DD)
     */
    public function queryReport(array $filters = []): \Illuminate\Database\Query\Builder;

    /**
     * Return single commission row by order id
     */
    public function findByOrderId(int $orderId): ?object;

    /**
     * Return order items for an order (sku, product name, price, quantity, total)
     */
    public function getOrderItems(int $orderId): Collection;
}
