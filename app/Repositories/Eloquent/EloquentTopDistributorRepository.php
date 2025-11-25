<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Contracts\TopDistributorRepositoryInterface;
use Illuminate\Support\Facades\DB;

class EloquentTopDistributorRepository implements TopDistributorRepositoryInterface
{
    public function queryTopDistributors(): \Illuminate\Database\Query\Builder
    {
        // the view v_top_distributors returns distributor_id, distributor_name, total_sales
        return DB::table('v_top_distributors');
    }
}
