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

namespace Runroom\CookiesBundle\Tests\Unit;

use Runroom\CookiesBundle\Form\Type\CookiesFormType;
use Symfony\Component\Form\Test\TypeTestCase;

class CookiesFormTypeTest extends TypeTestCase
{
    /** @test */
    public function itSubmitsValidData(): void
    {
        $form = $this->factory->create(CookiesFormType::class);

        $form->submit([
            'performanceCookies' => false,
            'targetingCookies' => false,
        ]);

        static::assertTrue($form->isSynchronized());
        static::assertSame($form->getData(), [
            'performanceCookies' => false,
            'targetingCookies' => false,
        ]);
    }
}
