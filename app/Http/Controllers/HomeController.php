<?php

namespace App\Http\Controllers;

use App\Http\Actions\Deployer\Index;
use App\Services\BranchFetcher;
use App\Services\Deployer\DeployerInterface;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use JsonException;

class HomeController extends Controller
{
    /**
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @param Index $indexAction
     * @return Renderable
     */
    public function actionIndex(Index $indexAction): Renderable
    {
        $tools = [
            [
                'name' => 'Returns',
                'icon' => '<i class="fas fa-cubes"></i>',
                'url' => route('returns-tool'),
                'description' => 'Tools for creating returns for further manual testing.',
            ],
            [
                'name' => 'Tracking',
                'icon' => '<i class="far fa-compass"></i>',
                'url' => route('tracking-tool'),
                'description' => 'Tools for creating different tracking history.',
            ],
            [
                'name' => 'Orders',
                'icon' => '<i class="fas fa-cart-arrow-down"></i>',
                'url' => route('orders-tool'),
                'description' => 'Tools for creating orders for further manual testing.',
            ],
            [
                'name' => 'Files',
                'icon' => '<i class="fas fa-file-csv"></i>',
                'url' => route('files-tool'),
                'description' => 'Generate or/and download different kind of files for further testing.',
            ],
            [
                'name' => 'Tests',
                'icon' => '<i class="fas fa-vials"></i>',
                'url' => route('tests-tool'),
                'description' => 'Run tests here',
            ],
            [
                'name' => 'API',
                'icon' => '<i class="fas fa-bug"></i>',
                'url' => route('api-tool'),
                'description' => 'Generate dummy API requests and other stuff...',
            ],
        ];

        return view('welcome')
            ->with('tools', $tools);
    }

    public function actionDeployer(Index $indexAction)
    {
        return $indexAction->run();
    }

    /**
     * @param string $repository
     * @param BranchFetcher $branchFetcher
     * @return JsonResponse
     * @throws JsonException
     */
    public function actionFetchBranches(string $repository, BranchFetcher $branchFetcher): JsonResponse
    {
        if (Cache::has($repository)) {
            $branches = Cache::get($repository);
        } else {
            $branches = $branchFetcher->getBranches(env('GITHUB_ORGANIZATION'), $repository);
            $pulls = $branchFetcher->getPullRequests(env('GITHUB_ORGANIZATION'), $repository);
            $pullsReduced = $this->reducePulls($pulls, $repository);
            $branches = $this->mapBranches($branches, $pullsReduced);

            Cache::add($repository, $branches, self::CACHE_TTL);
        }
        $branches = $branches->sortByDesc('pull_link');

        return new JsonResponse($branches);
    }

    public function actionDeployBranch(string $repository, string $branch, DeployerInterface $deployer): JsonResponse
    {
        $result = $deployer->deploy(env('GITHUB_ORGANIZATION'), $repository, $branch);

        return new JsonResponse(['success' => $result]);
    }
}
