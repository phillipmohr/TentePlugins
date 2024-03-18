<?php declare(strict_types=1);

use Shopware\Core\TestBootstrapper;

$loader = (new TestBootstrapper())
    ->addCallingPlugin()
    ->addActivePlugins('NwgncyAcrisProductDownloadsCSFix')
    ->setForceInstallPlugins(true)
    ->bootstrap()
    ->getClassLoader();

$loader->addPsr4('NwgncyAcrisProductDownloadsCSFix\\Tests\\', __DIR__);
