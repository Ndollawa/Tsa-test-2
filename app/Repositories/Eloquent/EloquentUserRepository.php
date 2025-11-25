<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Support\Facades\DB;

class EloquentUserRepository implements UserRepositoryInterface
{
    public function find(int $id)
    {
        return DB::table('users')->where('id', $id)->first();
    }

    /**
     * Count referred distributors for a given user as of a given date.
     * Uses users.referred_by + users.type or user_category lookup depending on schema.
     */
    public function countReferredDistributorsAsOf(int $userId, string $asOfDate): int
    {
        // this assumes `users.type = 'distributor'`. If schema uses user_category table, replace with appropriate join.
        return (int) DB::table('users')
            ->where('referred_by', $userId)
            ->where(function($q) use ($asOfDate) {
                // use enrolled_date if present, else created_at
                $q->whereDate(DB::raw('COALESCE(enrolled_date, created_at)'), '<=', $asOfDate);
            })
            ->where('type', 'distributor')
            ->count();
    }
}
