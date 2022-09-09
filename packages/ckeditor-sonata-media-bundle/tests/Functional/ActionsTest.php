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

namespace Runroom\CkeditorSonataMediaBundle\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Zenstruck\Foundry\Test\ResetDatabase;

class ActionsTest extends WebTestCase
{
    use ResetDatabase;

    /**
     * @test
     */
    public function itAllowsBrowser(): void
    {
        $client = static::createClient();

        $client->request('GET', '/tests/app/media/browser');

        self::assertResponseIsSuccessful();
    }

    /**
     * @test
     */
    public function itAllowsUploads(): void
    {
        $client = static::createClient();

        $client->request('POST', '/tests/app/media/upload?provider=sonata.media.provider.image', [], [
            'upload' => new UploadedFile(__DIR__ . '/../Fixtures/file.png', 'file.png'),
        ]);

        self::assertResponseIsSuccessful();
    }
}
