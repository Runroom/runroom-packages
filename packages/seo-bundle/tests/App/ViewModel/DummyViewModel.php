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

namespace Runroom\SeoBundle\Tests\App\ViewModel;

use Runroom\SeoBundle\Model\SeoModelInterface;

class DummyViewModel implements SeoModelInterface
{
    public function getPlaceholder(): string
    {
        return 'test';
    }
}
