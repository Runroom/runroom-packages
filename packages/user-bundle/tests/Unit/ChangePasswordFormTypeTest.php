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
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\Validator\Validation;

class ChangePasswordFormTypeTest extends TypeTestCase
{
    private FormInterface $form;

    protected function setUp(): void
    {
        parent::setUp();

        $this->form = $this->factory->create(ChangePasswordFormType::class);
    }

    /**
     * @test
     * @dataProvider submitValuesProvider
     */
    public function itSubmitsWithDifferentValues(string $fistPassword, string $secondPassword, bool $isValid, ?string $expectedData): void
    {
        $this->form->submit(['plainPassword' => [
            'first' => $fistPassword,
            'second' => $secondPassword,
        ]]);

        static::assertSame($isValid, $this->form->isValid());
        static::assertTrue($this->form->isSynchronized());
        static::assertSame($expectedData, $this->form->get('plainPassword')->getData());
    }

    /** @return iterable<array{string, string, bool, string|null}> */
    public function submitValuesProvider(): iterable
    {
        yield ['newPassword', 'oldPassword', false, null];
        yield ['newPassword', 'newPassword', true, 'newPassword'];
        yield ['', '', false, null];
        yield 'Password length shorter than 6 characters' => ['new', 'new', false, 'new'];
    }

    /** @test */
    public function itGetsFormDefaultOptions(): void
    {
        static::assertSame('RunroomUserBundle', $this->form->getConfig()->getOption('translation_domain'));
    }

    /** @return FormExtensionInterface[] */
    protected function getExtensions(): array
    {
        return [
            new ValidatorExtension(Validation::createValidator()),
        ];
    }
}
