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

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Runroom\UserBundle\Security\RolesBuilder\MatrixRolesBuilderInterface;
use Runroom\UserBundle\Twig\RolesMatrixRuntime;
use Symfony\Component\Form\FormView;
use Twig\Environment;

class RolesMatrixRuntimeTest extends TestCase
{
    /** @var MockObject&Environment */
    private MockObject $twig;

    /** @var Stub&MatrixRolesBuilderInterface */
    private Stub $rolesBuilder;
    private RolesMatrixRuntime $runtime;

    protected function setUp(): void
    {
        $this->twig = $this->createMock(Environment::class);
        $this->rolesBuilder = $this->createStub(MatrixRolesBuilderInterface::class);

        $this->runtime = new RolesMatrixRuntime($this->twig, $this->rolesBuilder);
    }

    /** @test */
    public function itRendersRolesList(): void
    {
        $childForm = new FormView();
        $childForm->vars['value'] = 'ROLE';
        $childForm2 = new FormView();
        $childForm2->vars['value'] = 'ROLE_ADMIN';

        $form = new FormView();
        $form->children = [
            'foo2' => $childForm2,
            'foo' => $childForm,
        ];

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

        $this->twig->expects(static::once())->method('render')->with(
            '@RunroomUser/admin/roles_matrix_list.html.twig',
            ['roles' => [
                'ROLE' => [
                    'role' => 'ROLE',
                    'role_translated' => 'Role translated',
                    'is_granted' => true,
                    'form' => $childForm,
                ],
            ]]
        )->willReturn('rendered string');

        $rolesList = $this->runtime->renderRolesList($form);

        static::assertSame('rendered string', $rolesList);
    }
}
