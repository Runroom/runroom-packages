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

namespace Runroom\UserBundle\Tests\Unit;

use PHPUnit\Framework\MockObject\Stub;
use Runroom\UserBundle\Form\RolesMatrixType;
use Runroom\UserBundle\Security\RolesBuilder\MatrixRolesBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\Form\Test\TypeTestCase;

class RolesMatrixTypeTest extends TypeTestCase
{
    /** @var Stub&MatrixRolesBuilderInterface */
    private Stub $rolesBuilder;
    private RolesMatrixType $rolesMatrixType;
    private FormInterface $form;

    protected function setUp(): void
    {
        $this->rolesBuilder = $this->createStub(MatrixRolesBuilderInterface::class);
        $this->rolesBuilder->method('getRoles')->willReturn([
            'ROLE' => [
                'role' => 'ROLE',
                'role_translated' => 'Role translated',
                'is_granted' => true,
            ],
            'ROLE_ADMIN' => [
                'role' => 'ROLE_ADMIN',
                'role_translated' => 'Role admin translated',
                'admin_label' => 'Role admin label',
                'is_granted' => true,
            ],
        ]);

        $this->rolesMatrixType = new RolesMatrixType($this->rolesBuilder);

        parent::setUp();

        $this->form = $this->factory->create(RolesMatrixType::class);
    }

    /** @test */
    public function itSubmits(): void
    {
        $this->form->submit(['ROLE', 'ROLE_ADMIN']);

        static::assertTrue($this->form->isValid());
        static::assertTrue($this->form->isSynchronized());
    }

    /** @test */
    public function itSubmitsInvalidRoles(): void
    {
        $this->form->submit(['ROLE_RANDOM']);

        static::assertFalse($this->form->isValid());
    }

    /** @test */
    public function itGetsFormDefaultOptions(): void
    {
        static::assertTrue($this->form->getConfig()->getOption('expanded'));
        static::assertNull($this->form->getConfig()->getOption('data_class'));
    }

    /** @test */
    public function itGetsBlockPrefix(): void
    {
        static::assertSame('sonata_roles_matrix', $this->rolesMatrixType->getBlockPrefix());
    }

    /** @test */
    public function itGetsParent(): void
    {
        static::assertSame(ChoiceType::class, $this->rolesMatrixType->getParent());
    }

    /** @return FormTypeInterface[] */
    protected function getTypes(): array
    {
        return [$this->rolesMatrixType];
    }
}
