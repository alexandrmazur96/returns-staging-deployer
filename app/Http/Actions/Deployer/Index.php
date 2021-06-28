<?php

namespace App\Http\Actions\Deployer;

use App\Http\Actions\Action;
use App\Services\BranchFetcher;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

final class Index implements Action
{
    private const CACHE_TTL = 5 * 60;

    /** @var BranchFetcher */
    private $branchFetcher;

    public function __construct(BranchFetcher $branchFetcher)
    {
        $this->branchFetcher = $branchFetcher;
    }

    public function run()
    {
        $defaultRepository = config('app.github-default-project');
        if (Cache::has($defaultRepository)) {
            $branches = Cache::get($defaultRepository);
        } else {
            $branches = $this->branchFetcher->getBranches(env('GITHUB_ORGANIZATION'), $defaultRepository);
            $pulls = $this->branchFetcher->getPullRequests(env('GITHUB_ORGANIZATION'), $defaultRepository);
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
