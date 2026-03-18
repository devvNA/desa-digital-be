<?php

namespace App\Providers;

use App\Interfaces\FamilyMemberRepositoryInterface;
use App\Interfaces\HeadOfFamilyRepositoryInterface;
use App\Interfaces\SocialAssistanceRepositoryInterface;
use App\Interfaces\UserRepositoryInterface;
use App\Repositories\FamilyMemberRepository;
use App\Repositories\HeadofFamilyRepository;
use App\Repositories\SocialAssistanceRepository;
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
        $this->app->bind(FamilyMemberRepositoryInterface::class, FamilyMemberRepository::class);
        $this->app->bind(SocialAssistanceRepositoryInterface::class, SocialAssistanceRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
