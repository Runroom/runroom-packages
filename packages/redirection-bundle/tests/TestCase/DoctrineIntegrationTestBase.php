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

use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\Persistence\ManagerRegistry;
use Fidry\AliceDataFixtures\LoaderInterface;
use PHPUnit\Framework\TestCase;
use Runroom\RedirectionBundle\Tests\Fixtures\App\Kernel;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

abstract class DoctrineIntegrationTestBase extends TestCase
{
    /** @var Kernel */
    protected static $kernel;

    /** @var LoaderInterface */
    protected static $loader;

    /** @var ContainerInterface */
    protected static $container;

    /** @var EntityManagerInterface */
    protected static $entityManager;

    /** @var Connection */
    protected static $connection;

    /** @var ParameterBagInterface */
    protected static $parameterBag;

    public static function setUpBeforeClass(): void
    {
        if (null !== static::$kernel) {
            return;
        }

        static::$kernel = new Kernel('test', false);
        static::$kernel->boot();

        static::$container = static::$kernel->getContainer()->get('test.service_container');
        static::$entityManager = static::$container->get(EntityManagerInterface::class);
        static::$loader = static::$container->get('fidry_alice_data_fixtures.loader.doctrine');
        static::$connection = static::$container->get(ManagerRegistry::class)->getConnection();
        static::$parameterBag = static::$container->getParameterBag();

        static::$container->get(RequestStack::class)->push(new Request());

        $schemaTool = new SchemaTool(static::$entityManager);
        $schemaTool->createSchema(static::$entityManager->getMetadataFactory()->getAllMetadata());
    }

    protected function setUp(): void
    {
        static::$connection->beginTransaction();
        static::$loader->load($this->processDataFixtures(), static::$parameterBag->all());
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
