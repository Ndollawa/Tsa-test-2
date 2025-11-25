<?php

namespace App\Repositories\Contracts;

use Illuminate\Support\Collection;

interface TopDistributorRepositoryInterface
{
    /**
     * Return top distributors query builder (selects from v_top_distributors)
     */
    public function queryTopDistributors(): \Illuminate\Database\Query\Builder;
}
