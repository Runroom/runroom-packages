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

namespace Runroom\FormHandlerBundle;

use Runroom\FormHandlerBundle\ViewModel\FormAwareInterface;
use Symfony\Component\Form\FormTypeInterface;

interface FormHandlerInterface
{
    /**
     * @param class-string<FormTypeInterface<object|null>> $type
     * @param array<string, mixed>                         $options
     */
    public function handleForm(string $type, array $options = [], ?FormAwareInterface $model = null): FormAwareInterface;
}
