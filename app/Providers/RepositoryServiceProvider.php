<?php

namespace App\Providers;

use App\Interfaces\HeadOfFamilyRepositoryInterface;
use App\Interfaces\UserRepositoryInterface;
use App\Repositories\HeadofFamilyRepository;
use Illuminate\Support\ServiceProvider;
use App\Repositories\UserRepository;


class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(HeadOfFamilyRepositoryInterface::class, HeadofFamilyRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
