<?php

namespace App\Services;

use App\Repositories\Contracts\TopDistributorRepositoryInterface;
use Illuminate\Database\Query\Builder;

class TopDistributorService
{
    protected TopDistributorRepositoryInterface $repo;

    public function __construct(TopDistributorRepositoryInterface $repo)
    {
        $this->repo = $repo;
    }

    /**
     * Returns a query builder for top distributors.
     * The controller will apply pagination.
     */
    public function queryTop(int $limit = 200): Builder
    {
        return $this->repo
            ->queryTopDistributors()
            ->orderByDesc('total_sales')
            ->limit($limit);
    }

    /**
     * Computes rank numbers WITH ties after paginated results.
     */
    public function applyRanks($paginated)
    {
        $prevTotal = null;
        $rank = 0;
        $index = ($paginated->currentPage() - 1) * $paginated->perPage();

        foreach ($paginated->items() as &$row) {
            $index++;

            if ($prevTotal === null || floatval($row->total_sales) < floatval($prevTotal)) {
                $rank = $index;
            }

            $row->rank = $rank;
            $prevTotal = $row->total_sales;
        }

        return $paginated;
    }
}
