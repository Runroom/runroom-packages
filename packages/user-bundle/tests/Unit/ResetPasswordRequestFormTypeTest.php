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

use Runroom\UserBundle\Form\ResetPasswordRequestFormType;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Form\FormExtensionInterface;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\Validator\Validation;

class ResetPasswordRequestFormTypeTest extends TypeTestCase
{
    /**
     * @test
     * @dataProvider submitValuesProvider
     */
    public function itSubmitsWithDifferentValues(string $email, bool $isValid): void
    {
        $formData = [
            'identifier' => $email,
        ];

        $form = $this->factory->create(ResetPasswordRequestFormType::class);
        $form->submit($formData);

        static::assertSame($isValid, $form->isValid());
        static::assertTrue($form->isSynchronized());
    }

    /** @return iterable<array{string, bool}> */
    public function submitValuesProvider(): iterable
    {
        yield ['', false];
        yield ['emailNotValid', false];
        yield ['email@localhost.com', true];
    }

    /** @test */
    public function itGetsFormDefaultOptions(): void
    {
        $form = $this->factory->create(ResetPasswordRequestFormType::class);
        static::assertSame('RunroomUserBundle', $form->getConfig()->getOption('translation_domain'));
    }

    /** @return FormExtensionInterface[] */
    protected function getExtensions(): array
    {
        return [
            new ValidatorExtension(Validation::createValidator()),
        ];
    }
}
