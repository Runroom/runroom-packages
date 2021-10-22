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
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\Validator\Validation;

class ResetPasswordRequestFormTypeTest extends TypeTestCase
{
    private FormInterface $form;

    protected function setUp(): void
    {
        parent::setUp();

        $this->form = $this->factory->create(ResetPasswordRequestFormType::class);
    }

    /**
     * @test
     * @dataProvider submitValuesProvider
     */
    public function itSubmitsWithDifferentValues(string $email, bool $isValid): void
    {
        $this->form->submit(['identifier' => $email]);

        static::assertSame($isValid, $this->form->isValid());
        static::assertTrue($this->form->isSynchronized());
    }

    /** @return iterable<array{string, bool}> */
    public function submitValuesProvider(): iterable
    {
        yield ['', false];
        yield ['emailNotValid', true];
        yield ['email@localhost.com', true];
    }

    /** @test */
    public function itGetsFormDefaultOptions(): void
    {
        static::assertSame('RunroomUserBundle', $this->form->getConfig()->getOption('translation_domain'));
    }

    /** @return FormExtensionInterface[] */
    protected function getExtensions(): array
    {
        return [new ValidatorExtension(Validation::createValidator())];
    }
}
