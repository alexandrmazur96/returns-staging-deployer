<?php

namespace App\Providers;

use App\Services\BranchFetcher;
use App\Services\Deployer\DeployerInterface;
use App\Services\Deployer\SshDeployer;
use Illuminate\Support\ServiceProvider;
use Psr\Log\LoggerInterface;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->bind(
            BranchFetcher::class,
            static function () {
                return new BranchFetcher(env('GITHUB_TOKEN'));
            }
        );
        $this->app->bind(DeployerInterface::class, function () {
            return new SshDeployer(
                app(LoggerInterface::class),
                env('SSH_USER'),
                env('SSH_HOST'),
                env('SSH_PORT')
            );
        });
    }
}
