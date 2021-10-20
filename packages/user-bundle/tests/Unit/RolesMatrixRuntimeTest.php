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

use PHPUnit\Framework\TestCase;
use Runroom\UserBundle\Security\RolesBuilder\MatrixRolesBuilderInterface;
use Runroom\UserBundle\Twig\RolesMatrixRuntime;
use Symfony\Component\Form\FormView;
use Twig\Environment;

class RolesMatrixRuntimeTest extends TestCase
{
    private RolesMatrixRuntime $runtime;

    protected function setUp(): void
    {
        $twig = $this->createStub(Environment::class);
        $rolesBuilder = $this->createStub(MatrixRolesBuilderInterface::class);

        $this->runtime = new RolesMatrixRuntime($twig, $rolesBuilder);
    }

    /** @test */
    public function foo(): void
    {
        $form = $this->createStub(FormView::class);

        $this->runtime->renderRolesList($form);
    }
}
