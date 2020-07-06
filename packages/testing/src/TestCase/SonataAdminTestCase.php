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

abstract class SonataAdminTestCase extends KernelTestCase
{
    /** @var AbstractAdmin|null */
    protected $admin;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        $admin = $kernel->getContainer()->get($this->getAdminClass());
        \assert($admin instanceof AbstractAdmin);

        $this->admin = $admin;
        $this->admin->setSubject($this->admin->getNewInstance());
    }

    final protected function assertAdminListContainsField(string $field): void
    {
        if (null !== $this->admin) {
            self::assertTrue($this->admin->hasListFieldDescription($field));
        } else {
            self::fail();
        }
    }

    final protected function assertAdminFormContainsField(string $field): void
    {
        if (null !== $this->admin) {
            self::assertTrue($this->admin->hasFormFieldDescription($field));
        } else {
            self::fail();
        }
    }

    final protected function assertAdminFilterContainsField(string $field): void
    {
        if (null !== $this->admin) {
            self::assertTrue($this->admin->hasFilterFieldDescription($field));
        } else {
            self::fail();
        }
    }

    /** @return class-string<AbstractAdmin>  */
    abstract protected function getAdminClass(): string;
}
