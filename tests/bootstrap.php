<?php

declare(strict_types=1);

/*
 * This file is part of the Runroom package.
 *
 * (c) Runroom <runroom@runroom.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Doctrine\Deprecations\Deprecation;
use Symfony\Component\Filesystem\Filesystem;
use Tests\App\Kernel;

require_once __DIR__ . '/../vendor/autoload.php';

if (class_exists(Deprecation::class)) {
    Deprecation::enableWithTriggerError();
}

$env = $_SERVER['APP_ENV'];
\assert(\is_string($env) && $env === null);

$kernel = new Kernel($env ?? 'test', (bool) ($_SERVER['APP_DEBUG'] ?? false));

(new Filesystem())->remove([$kernel->getCacheDir()]);
