<?php

namespace App\Services\Deployer;

use Exception;
use phpseclib\Crypt\RSA;
use phpseclib\Net\SSH2;
use Psr\Log\LoggerInterface;
use RuntimeException;

final class SshDeployer implements DeployerInterface
{
    /** @var string */
    private $sshUser;

    /** @var string */
    private $sshHost;

    /** @var int */
    private $sshPort;

    /** @var LoggerInterface */
    private $logger;

    public function __construct(
        LoggerInterface $logger,
        string $sshUser,
        string $sshHost,
        int $sshPort = 22
    ) {
        $this->logger = $logger;
        $this->sshUser = $sshUser;
        $this->sshHost = $sshHost;
        $this->sshPort = $sshPort;
    }

    /**
     * @param string $organization
     * @param string $repo
     * @param string $branch
     * @return bool
     * @throws Exception
     */
    public function deploy(string $organization, string $repo, string $branch): bool
    {
        $repoUrl = sprintf('git@github.com:%s/%s', $organization, $repo);
        $this->logger->info(
            'Deploying by ' . self::class,
            [
                'repository_url' => $repoUrl,
            ]
        );
        $deployGroup = $this->deployGroupByRepo($repo);
        $cmd = sprintf(
            "sudo su - ansible -c 'cd /home/ansible/playbooks; ./deploy-php-app.sh %s %s %s'",
            $branch,
            $deployGroup,
            $repoUrl
        );

        $rsa = $this->loadRsa();
        $ssh = new SSH2($this->sshHost, $this->sshPort);
        $loginStatus = $ssh->login($this->sshUser, $rsa);
        if (!$loginStatus) {
            $this->logger->error('Failed to login to ssh server');
            return false;
        }

        $response = $ssh->exec($cmd);

        $this->logger->debug(
            'Deploy info',
            [
                'cmd' => $cmd,
                'error' => $ssh->getLastError(),
                'response' => $response,
            ]
        );

        if ($response === false) {
            $this->logger->error('Failed to run command on ssh server');
            return false;
        }

        $sshCommandSuccess = $this->handleAnsibleResponse($response);
        if (!$sshCommandSuccess) {
            $this->logger->error('Command on ssh server failed');
            return false;
        }

        return true;
    }

    private function loadRsa(): RSA
    {
        $rsa = new RSA();
        $rsa->setPublicKeyFormat(RSA::PUBLIC_FORMAT_OPENSSH);
        $rsa->loadKey(file_get_contents(env('ID_RSA_PATH')));

        return $rsa;
    }

    /**
     * @param string $repo
     * @return string
     * @throws Exception
     */
    private function deployGroupByRepo(string $repo): string
    {
        switch ($repo) {
            case 'returns-platform-ir':
                return 'staging-ir';
            case 'returns-platform-rp':
                return 'staging-rp';
            case 'returns-platform-rq':
                return 'staging-rq';
            case 'returns-platform-air':
                return 'staging-air';
        }

        throw new RuntimeException('Undefined repository');
    }

    private function handleAnsibleResponse(string $response): bool
    {
        // todo: add handling
        return true;
    }
}
