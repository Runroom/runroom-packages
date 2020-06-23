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
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Test\TestContainer;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\KernelInterface;

abstract class DoctrineIntegrationTestBase extends TestCase
{
    /** @var KernelInterface */
    protected static $kernel;

    /** @var LoaderInterface */
    protected static $loader;

    /** @var TestContainer */
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

        static::$kernel = static::createKernel();
        static::$kernel->boot();

        static::$container = static::$kernel->getContainer()->get('test.service_container');
        static::$entityManager = static::$container->get(EntityManagerInterface::class);
        static::$loader = static::$container->get('fidry_alice_data_fixtures.loader.doctrine');
        static::$connection = static::$container->get(Connection::class);
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

    /** @return string[] */
    protected function processDataFixtures(): array
    {
        return array_map(function ($value): string {
            $testClass = new \ReflectionClass(static::class);
            $filename = $testClass->getFileName();

            if (false !== $filename) {
                return \dirname($filename, 2) . '/Fixtures/' . $value;
            }

            return '';
        }, $this->getDataFixtures());
    }

    /** @return string[] */
    abstract protected function getDataFixtures(): array;

    private static function createKernel(): KernelInterface
    {
        if (!isset($_SERVER['KERNEL_CLASS']) && !isset($_ENV['KERNEL_CLASS'])) {
            throw new \LogicException(sprintf('You must set the KERNEL_CLASS environment variable to the fully-qualified class name of your Kernel in phpunit.xml / phpunit.xml.dist or override the "%1$s::createKernel()" or "%1$s::getKernelClass()" method.', static::class));
        }

        if (!class_exists($class = $_ENV['KERNEL_CLASS'] ?? $_SERVER['KERNEL_CLASS'])) {
            throw new \RuntimeException(sprintf('Class "%s" doesn\'t exist or cannot be autoloaded. Check that the KERNEL_CLASS value in phpunit.xml matches the fully-qualified class name of your Kernel or override the "%s::createKernel()" method.', $class, static::class));
        }

        return new $class('test', true);
    }
}
