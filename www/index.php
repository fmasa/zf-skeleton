<?php

use Fmasa\Bootstrap;
use Zend\HttpHandlerRunner\RequestHandlerRunner;

require __DIR__ . '/../vendor/autoload.php';

Bootstrap::boot()
    ->createContainer()
    ->getByType(RequestHandlerRunner::class)
    ->run();

