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

namespace Runroom\DoctrineTranslatableBundle\Provider;

use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use Symfony\Component\DependencyInjection\Exception\ParameterNotFoundException;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Translation\TranslatorInterface;

final class LocaleProvider implements LocaleProviderInterface
{
    public function __construct(
        private readonly RequestStack $requestStack,
        private readonly ParameterBagInterface $parameterBag,
        private readonly ?TranslatorInterface $translator,
    ) {}

    public function provideCurrentLocale(): ?string
    {
        $currentRequest = $this->requestStack->getCurrentRequest();
        if (!$currentRequest instanceof Request) {
            return null;
        }

        $currentLocale = $currentRequest->getLocale();
        if ('' !== $currentLocale) {
            return $currentLocale;
        }

        if (null !== $this->translator) {
            return $this->translator->getLocale();
        }

        return null;
    }

    public function provideFallbackLocale(): ?string
    {
        $currentRequest = $this->requestStack->getCurrentRequest();
        if (null !== $currentRequest) {
            return $currentRequest->getDefaultLocale();
        }

        try {
            if ($this->parameterBag->has('locale')) {
                $locale = $this->parameterBag->get('locale');

                if (!\is_string($locale)) {
                    return null;
                }

                return $locale;
            }

            return $this->parameterBag->get('kernel.default_locale');
        } catch (ParameterNotFoundException|InvalidArgumentException) {
            return null;
        }
    }
}
