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

/**
 * @extends SonataAdminTestCase<UserInterface>
 */
class UserAdminTest extends SonataAdminTestCase
{
    use Factories;

    /**
     * @test
     */
    public function itHasAllListFields(): void
    {
        $this->assertAdminListContainsField('createdAt');
        $this->assertAdminListContainsField('email');
        $this->assertAdminListContainsField('enabled');
    }

    /**
     * @test
     */
    public function itHasAllFormFields(): void
    {
        $this->assertAdminFormContainsField('email');
        $this->assertAdminFormContainsField('plainPassword');
        $this->assertAdminFormContainsField('enabled');
        $this->assertAdminFormContainsField('roles');
    }

    /**
     * @test
     */
    public function itHasAllFilterFields(): void
    {
        $this->assertAdminFilterContainsField('email');
        $this->assertAdminFilterContainsField('enabled');
    }

    /**
     * @test
     */
    public function itDoesNotHaveDisabledRoutes(): void
    {
        $this->assertAdminRoutesDoesNotContainRoute('show');
    }

    /**
     * @test
     */
    public function itHasAllExportFields(): void
    {
        $this->assertAdminExportDoesContainField('id');
        $this->assertAdminExportDoesContainField('email');
        $this->assertAdminExportDoesContainField('roles');
        $this->assertAdminExportDoesContainField('enabled');
        $this->assertAdminExportDoesContainField('createdAt');
        $this->assertAdminExportDoesNotContainField('password');
        $this->assertAdminExportDoesNotContainField('salt');
    }

    /**
     * @test
     */
    public function itUpdatesPasswordOnCreate(): void
    {
        $user = UserFactory::createOne([
            'plainPassword' => 'new_password',
        ])->object();

        $createdUser = $this->admin->create($user);

        static::assertSame('new_password', $createdUser->getPassword());
        static::assertNull($createdUser->getPlainPassword());
    }

    /**
     * @test
     */
    public function itDoesNotChangePasswordIfNoNewPasswordIsProvided(): void
    {
        $user = UserFactory::createOne(['password' => 'testing'])->object();

        $createdUser = $this->admin->update($user);

        static::assertSame('testing', $createdUser->getPassword());
    }

    protected function getAdminClass(): string
    {
        return 'runroom.user.admin.user';
    }
}
