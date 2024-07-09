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

namespace Runroom\BasicPageBundle\Tests\Functional;

use Runroom\BasicPageBundle\Factory\BasicPageFactory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

final class BasicPageControllerTest extends WebTestCase
{
    use Factories;
    use ResetDatabase;

    public function testItRendersStatic(): void
    {
        $client = static::createClient();

        $client->request('GET', '/basic-page');
        self::assertResponseStatusCodeSame(404);

        BasicPageFactory::new(['publish' => true])->withTranslations(['en'], [
            'slug' => 'basic-page',
        ])->create();

        $client->request('GET', '/basic-page');
        self::assertResponseIsSuccessful();
    }
}
