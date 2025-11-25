<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\Contracts\OrderRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\Eloquent\EloquentOrderRepository;
use App\Repositories\Eloquent\EloquentUserRepository;
use App\Repositories\Contracts\CommissionRepositoryInterface;
use App\Repositories\Contracts\TopDistributorRepositoryInterface;
use App\Repositories\Eloquent\EloquentCommissionRepository;
use App\Repositories\Eloquent\EloquentTopDistributorRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(OrderRepositoryInterface::class, EloquentOrderRepository::class);
        $this->app->bind(UserRepositoryInterface::class, EloquentUserRepository::class);
        $this->app->bind(CommissionRepositoryInterface::class, EloquentCommissionRepository::class);
        $this->app->bind(TopDistributorRepositoryInterface::class, EloquentTopDistributorRepository::class);

    }

    public function boot()
    {
        //
    }
}
