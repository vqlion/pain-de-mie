<?php

namespace App;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    public function __construct($environment, $debug)
    {
        date_default_timezone_set( $_ENV['APP_TIMEZONE'] );
        parent::__construct($environment, $debug);
    }
}
