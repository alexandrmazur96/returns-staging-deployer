<?php

namespace App\Services\Deployer;

interface DeployerInterface
{
    public function deploy(string $organization, string $repo, string $branch): bool;
}
