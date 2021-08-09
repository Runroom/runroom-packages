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

namespace Runroom\SeoBundle\Context;

use Runroom\SeoBundle\Model\SeoModelInterface;

final class DefaultContextExtractor implements ContextExtractorInterface
{
    private string $modelKey;

    public function __construct(string $modelKey)
    {
        $this->modelKey = $modelKey;
    }

    public function extract(array $context): ?SeoModelInterface
    {
        if (!isset($context[$this->modelKey])) {
            return null;
        }

        $model = $context[$this->modelKey];

        if (!$model instanceof SeoModelInterface) {
            return null;
        }

        return $model;
    }
}
