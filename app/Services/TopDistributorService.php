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
        ->orderBy('rank')
        ->limit($limit);
}

  
}
