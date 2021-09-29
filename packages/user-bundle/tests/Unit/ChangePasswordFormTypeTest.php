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

use Runroom\UserBundle\Form\ChangePasswordFormType;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Form\FormExtensionInterface;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\Validator\Validation;

class ChangePasswordFormTypeTest extends TypeTestCase
{
    /**
     * @test
     * @dataProvider dataProvider
     */
    public function itSubmitWithDifferentValues(string $fistPassword, string $secondPassword, bool $isValid, ?string $expectedData): void
    {
        $formData = [
            'plainPassword' => [
                'first' => $fistPassword,
                'second' => $secondPassword,
            ],
        ];

        $form = $this->factory->create(ChangePasswordFormType::class);
        $form->submit($formData);

        static::assertSame($isValid, $form->isValid());
        static::assertTrue($form->isSynchronized());
        static::assertSame($expectedData, $form->get('plainPassword')->getData());
    }

    /** @return iterable<array{string, string, bool, string|null}> */
    public function dataProvider(): iterable
    {
        yield ['newPassword', 'oldPassword', false, null];
        yield ['newPassword', 'newPassword', true, 'newPassword'];
        yield ['', '', false, null];
        yield 'Password length shorter than 6 characters' => ['new', 'new', false, 'new'];
    }

    public function itGetsFormDefaultOptions(): void
    {
        $form = $this->factory->create(ChangePasswordFormType::class);
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
