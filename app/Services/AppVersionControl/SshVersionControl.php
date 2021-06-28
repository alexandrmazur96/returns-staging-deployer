<?php

namespace App\Services\AppVersionControl;

use Spatie\Ssh\Ssh;

class SshVersionControl
{
    public function getVersions(): array
    {
        $dirListCmd = 'ls /home/www/releases/';

//        Ssh::
//        $ll = 'ln -sf %s %s';
    }
}
