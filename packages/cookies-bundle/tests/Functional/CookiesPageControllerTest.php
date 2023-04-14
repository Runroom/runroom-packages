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

namespace Runroom\CookiesBundle\Tests\Functional;

use Runroom\CookiesBundle\Factory\CookiesPageFactory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class CookiesPageControllerTest extends WebTestCase
{
    use Factories;
    use ResetDatabase;

    public function testItRendersCookiesPage(): void
    {
        $client = static::createClient();

        CookiesPageFactory::createOne();

        $client->request('GET', '/cookies-policy');

        self::assertResponseIsSuccessful();

        $client->request('GET', '/politica-de-cookies');

        self::assertResponseIsSuccessful();

        $client->request('GET', '/cookies-policy');

        self::assertResponseIsSuccessful();
    }
}
