<?php

namespace App\Services;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use JsonException;

// todo: add handling rate limits
class BranchFetcher
{
    private PendingRequest $httpClient;

    public function __construct(string $oauthToken)
    {
        $this->httpClient = Http::withHeaders(
            [
                'Authorization' => 'token ' . $oauthToken,
            ]
        );
    }

    /**
     * @param string $owner
     * @param string $repository
     * @return Collection
     * @throws JsonException
     */
    public function getBranches(string $owner, string $repository): Collection
    {
        $page = 1;
        $limit = 100;
        $result = [];
        do {
            $response = $this->httpClient->get($this->buildBranchesUrl($owner, $repository, $page++, $limit));
            $rawResponse = $response->body();
            $requestResult = json_decode($rawResponse, true, 512, JSON_THROW_ON_ERROR);
            if (isset($requestResult['message'])) {
                break;
            }
            $result = array_merge($result, $requestResult);
        } while (!empty($requestResult));

        return collect($result);
    }

    public function getPullRequests(string $owner, string $repository): Collection
    {
        $page = 1;
        $limit = 100;
        $result = [];
        do {
            $response = $this->httpClient->get($this->buildPullsUrl($owner, $repository, $page++, $limit));
            $rawResponse = $response->body();
            $requestResult = json_decode($rawResponse, true, 512, JSON_THROW_ON_ERROR);
            if (isset($requestResult['message'])) {
                break;
            }
            $result = array_merge($result, $requestResult);
        } while (!empty($requestResult));

        return collect($result);
    }

    private function buildBranchesUrl(string $owner, string $repository, int $page, int $limit): string
    {
        return sprintf(
            'https://api.github.com/repos/%s/%s/branches?page=%d&per_page=%d',
            $owner,
            $repository,
            $page,
            $limit
        );
    }

    private function buildPullsUrl(string $owner, string $repository, int $page, int $limit): string
    {
        return sprintf(
            'https://api.github.com/repos/%s/%s/pulls?page=%d&per_page=%d',
            $owner,
            $repository,
            $page,
            $limit
        );
    }
}
