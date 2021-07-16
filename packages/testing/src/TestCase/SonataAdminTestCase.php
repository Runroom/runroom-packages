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

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Request;

/**
 * @phpstan-template T of object
 */
abstract class SonataAdminTestCase extends KernelTestCase
{
    /** @var AbstractAdmin<T>|null */
    protected ?AbstractAdmin $admin = null;

    /* @todo: Simplify when dropping support for Symfony 4 */
    protected function setUp(): void
    {
        self::bootKernel();

        $container = method_exists(static::class, 'getContainer') ? static::getContainer() : static::$container;

        /** @var AbstractAdmin<T> */
        $admin = $container->get($this->getAdminClass());

        $this->admin = $admin;
        $this->admin->setSubject($this->admin->getNewInstance());
        $this->admin->setRequest(new Request());
    }

    final protected function assertAdminRoutesDoesContainRoute(string $route): void
    {
        \assert($this->admin instanceof AbstractAdmin);

        self::assertTrue($this->admin->hasRoute($route), 'It does not contain route: ' . $route);
    }

    final protected function assertAdminRoutesDoesNotContainRoute(string $route): void
    {
        \assert($this->admin instanceof AbstractAdmin);

        self::assertFalse($this->admin->hasRoute($route), 'It does contain route: ' . $route);
    }

    final protected function assertAdminShowContainsField(string $field): void
    {
        \assert($this->admin instanceof AbstractAdmin);

        self::assertTrue($this->admin->hasShowFieldDescription($field), 'It does not contain show field: ' . $field);
    }

    final protected function assertAdminListContainsField(string $field): void
    {
        \assert($this->admin instanceof AbstractAdmin);

        self::assertTrue($this->admin->hasListFieldDescription($field), 'It does not contain list field: ' . $field);
    }

    final protected function assertAdminFormContainsField(string $field): void
    {
        \assert($this->admin instanceof AbstractAdmin);

        self::assertTrue($this->admin->hasFormFieldDescription($field), 'It does not contain form field: ' . $field);
    }

    final protected function assertAdminFilterContainsField(string $field): void
    {
        \assert($this->admin instanceof AbstractAdmin);

        self::assertTrue($this->admin->hasFilterFieldDescription($field), 'It does not contain filter field: ' . $field);
    }

    /** @param mixed $value */
    final protected function assertAdminFilterParametersContainsFilter(string $filter, $value = null): void
    {
        \assert($this->admin instanceof AbstractAdmin);

        $filterParameters = $this->admin->getFilterParameters();

        self::assertTrue(isset($filterParameters[$filter]) && (null === $value || $filterParameters[$filter] === $value), 'It does not contain filter parameter: ' . $filter);
    }

    /** @return class-string<AbstractAdmin>|string  */
    abstract protected function getAdminClass(): string;
}
