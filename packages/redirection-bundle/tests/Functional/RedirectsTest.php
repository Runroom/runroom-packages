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

namespace Runroom\RedirectionBundle\Tests\Functional;

use Runroom\RedirectionBundle\Entity\Redirect;
use Runroom\RedirectionBundle\Factory\RedirectFactory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class RedirectsTest extends WebTestCase
{
    use Factories;
    use ResetDatabase;

    /**
     * @test
     */
    public function itRedirects(): void
    {
        $client = static::createClient();

        $client->request('GET', '/redirects');
        self::assertResponseStatusCodeSame(404);

        RedirectFactory::new([
            'source' => '/redirects',
            'destination' => '/destination',
            'publish' => true,
            'httpCode' => Redirect::PERMANENT,
        ])->create()->object();

        $client->request('GET', '/redirects');
        self::assertResponseRedirects('/destination', Redirect::PERMANENT);
    }
}
