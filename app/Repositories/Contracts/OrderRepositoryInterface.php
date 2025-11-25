<?php

namespace App\Repositories\Contracts;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface OrderRepositoryInterface
{
    public function find(int $id);
    public function getOrderItems(int $orderId): Collection;
    public function queryCommissionReport(array $filters = []): \Illuminate\Database\Query\Builder;
}
