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

namespace Runroom\UserBundle\Tests\Integration;

use Runroom\Testing\TestCase\SonataAdminTestCase;
use Runroom\UserBundle\Factory\UserFactory;
use Runroom\UserBundle\Model\UserInterface;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

/**
 * @extends SonataAdminTestCase<UserInterface>
 */
final class UserAdminTest extends SonataAdminTestCase
{
    use Factories;
    use ResetDatabase;

    public function testItHasAllListFields(): void
    {
        $this->assertAdminListContainsField('createdAt');
        $this->assertAdminListContainsField('email');
        $this->assertAdminListContainsField('enabled');
    }

    public function testItHasAllFormFields(): void
    {
        $this->assertAdminFormContainsField('email');
        $this->assertAdminFormContainsField('plainPassword');
        $this->assertAdminFormContainsField('enabled');
        $this->assertAdminFormContainsField('roles');
    }

    public function testItHasAllFilterFields(): void
    {
        $this->assertAdminFilterContainsField('email');
        $this->assertAdminFilterContainsField('enabled');
    }

    public function testItDoesNotHaveDisabledRoutes(): void
    {
        $this->assertAdminRoutesDoesNotContainRoute('show');
    }

    public function testItHasAllExportFields(): void
    {
        $this->assertAdminExportDoesContainField('id');
        $this->assertAdminExportDoesContainField('email');
        $this->assertAdminExportDoesContainField('roles');
        $this->assertAdminExportDoesContainField('enabled');
        $this->assertAdminExportDoesContainField('createdAt');
        $this->assertAdminExportDoesNotContainField('password');
        $this->assertAdminExportDoesNotContainField('salt');
    }

    public function testItUpdatesPasswordOnCreate(): void
    {
        $user = UserFactory::createOne([
            'plainPassword' => 'new_password',
        ]);

        $createdUser = $this->admin->create($user);

        static::assertSame('new_password', $createdUser->getPassword());
        static::assertNull($createdUser->getPlainPassword());
    }

    public function testItDoesNotChangePasswordIfNoNewPasswordIsProvided(): void
    {
        $user = UserFactory::createOne(['password' => 'testing']);

        $createdUser = $this->admin->update($user);

        static::assertSame('testing', $createdUser->getPassword());
    }

    protected function getAdminClass(): string
    {
        return 'runroom.user.admin.user';
    }
}
