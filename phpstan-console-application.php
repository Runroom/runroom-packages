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

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Tests\App\Kernel;

require __DIR__ . '/vendor/autoload.php';

$kernel = new Kernel('test', true);

return new Application($kernel);
