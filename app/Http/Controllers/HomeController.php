<?php

namespace App\Http\Controllers;

use App\Services\BranchFetcher;
use App\Services\Deployer\DeployerInterface;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use JsonException;

class HomeController extends Controller
{
    private const CACHE_TTL = 5 * 60;

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
     * @param BranchFetcher $branchFetcher
     * @return Renderable
     * @throws JsonException
     */
    public function actionIndex(BranchFetcher $branchFetcher): Renderable
    {
        $defaultRepository = config('app.github-default-project');
        if (Cache::has($defaultRepository)) {
            $branches = Cache::get($defaultRepository);
        } else {
            $branches = $branchFetcher->getBranches(env('GITHUB_ORGANIZATION'), $defaultRepository);
            $pulls = $branchFetcher->getPullRequests(env('GITHUB_ORGANIZATION'), $defaultRepository);
            $pullsReduced = $this->reducePulls($pulls, $defaultRepository);
            $branches = $this->mapBranches($branches, $pullsReduced);

            Cache::add($defaultRepository, $branches, self::CACHE_TTL);
        }
        $branches = $branches->sortByDesc('pull_link');

        return view('home')
            ->with('branches', $branches)
            ->with('projects', config('app.github-projects'))
            ->with('defaultProject', $defaultRepository);
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

    private function reducePulls(Collection $pulls, $repository): array
    {
        return $pulls->reduce(
            function ($carry, $item) use ($repository) {
                $carry[$item['head']['ref']] = [
                    'link' => sprintf(
                        'https://github.com/%s/%s/pull/%d',
                        env('GITHUB_ORGANIZATION'),
                        $repository,
                        $item['number']
                    ),
                    'user' => $item['user']['login'] ?? null,
                ];

                return $carry;
            },
            []
        );
    }

    private function mapBranches(Collection $branches, array $pullsReduced): Collection
    {
        return $branches->map(
            function (array $branch) use ($pullsReduced) {
                if (isset($pullsReduced[$branch['name']])) {
                    return [
                        'name' => $branch['name'],
                        'pull_link' => $pullsReduced[$branch['name']]['link'],
                        'user' => $pullsReduced[$branch['name']]['user'],
                    ];
                }

                return [
                    'name' => $branch['name'],
                    'pull_link' => null,
                    'user' => null,
                ];
            }
        );
    }
}
