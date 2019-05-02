<?php

declare(strict_types=1);

namespace Fmasa;

use Nette\Configurator;

final class Bootstrap
{
    public static function boot() : Configurator
    {
        $configurator = new Configurator();

        $configurator->setDebugMode((bool) getenv('DEVELOPMENT_MACHINE'));
        $configurator->addParameters(['appDir' => __DIR__]);
        $configurator->enableTracy(__DIR__ . '/../log');
        $configurator->setTimeZone('Europe/Prague');
        $configurator->setTempDirectory(__DIR__ . '/../temp');

        $configurator->addConfig(__DIR__ . '/config/config.neon');

        return $configurator;
    }
}
