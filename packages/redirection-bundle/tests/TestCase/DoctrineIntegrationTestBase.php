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

namespace Runroom\RedirectionBundle\Tests\TestCase;

use Doctrine\ORM\Tools\SchemaTool;
use PHPUnit\Framework\TestCase;
use Runroom\RedirectionBundle\Tests\Fixtures\App\Kernel;

abstract class DoctrineIntegrationTestBase extends TestCase
{
    protected static $kernel;
    protected static $loader;
    protected static $container;
    protected static $entityManager;
    protected static $connection;

    public static function setUpBeforeClass(): void
    {
        if (null !== static::$kernel) {
            return;
        }

        static::$kernel = new Kernel('test', true);
        static::$kernel->boot();
        static::$container = static::$kernel->getContainer();

        static::$entityManager = static::$container->get('doctrine.orm.entity_manager');

        static::$loader = static::$container->get('fidry_alice_data_fixtures.loader.doctrine');
        static::$connection = static::$container->get('doctrine')->getConnection();

        $schemaTool = new SchemaTool(static::$entityManager);
        $schemaTool->createSchema(static::$entityManager->getMetadataFactory()->getAllMetadata());
    }

    protected function setUp(): void
    {
        static::$connection->beginTransaction();
        static::$loader->load($this->processDataFixtures(), static::$container->getParameterBag()->all());
        static::$entityManager->clear();
    }

    protected function tearDown(): void
    {
        static::$connection->rollBack();
    }

    protected function processDataFixtures(): array
    {
        return array_map(function ($value) {
            $testClass = new \ReflectionClass(static::class);

            return \dirname($testClass->getFileName(), 2) . '/Fixtures/' . $value;
        }, $this->getDataFixtures());
    }

    abstract protected function getDataFixtures(): array;
}
