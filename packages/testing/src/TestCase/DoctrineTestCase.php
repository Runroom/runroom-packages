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

namespace Runroom\Testing\TestCase;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Fidry\AliceDataFixtures\LoaderInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Zenstruck\Foundry\Test\DatabaseResetter;

abstract class DoctrineTestCase extends KernelTestCase
{
    protected static LoaderInterface $loader;
    protected static EntityManagerInterface $entityManager;
    protected static Connection $connection;
    protected static ContainerBagInterface $containerBag;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->ensureSchemaIsCreated();

        static::$connection->beginTransaction();
        static::$loader->load($this->processDataFixtures(), static::$containerBag->all());
        static::$entityManager->clear();
    }

    protected function tearDown(): void
    {
        static::$connection->rollBack();
    }

    /**
     * @return string[]
     */
    abstract protected function getDataFixtures(): array;

    /**
     * @return string[]
     */
    private function processDataFixtures(): array
    {
        $testClass = new \ReflectionClass(static::class);
        $filename = $testClass->getFileName();

        \assert(false !== $filename);

        return array_map(
            static fn ($value): string => \dirname($filename, 2) . '/Fixtures/' . $value,
            $this->getDataFixtures()
        );
    }

    private function ensureSchemaIsCreated(): void
    {
        static::$entityManager = static::getContainer()->get(EntityManagerInterface::class);
        static::$loader = static::getContainer()->get('fidry_alice_data_fixtures.loader.doctrine');
        static::$connection = static::getContainer()->get(Connection::class);
        static::$containerBag = static::getContainer()->get(ContainerBagInterface::class);

        static::getContainer()->get(RequestStack::class)->push(new Request());

        if (!class_exists(DatabaseResetter::class)) {
            $schemaTool = new SchemaTool(static::$entityManager);
            $classes = static::$entityManager->getMetadataFactory()->getAllMetadata();

            $schemaTool->dropSchema($classes);
            $schemaTool->createSchema($classes);
        }
    }
}
