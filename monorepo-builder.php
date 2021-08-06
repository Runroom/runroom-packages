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

use App\Release\ComposerNormalizePostReleaseWorker;
use App\Release\ComposerNormalizePreReleaseWorker;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection;
use Symplify\MonorepoBuilder\Release\ReleaseWorker\PushNextDevReleaseWorker;
use Symplify\MonorepoBuilder\Release\ReleaseWorker\PushTagReleaseWorker;
use Symplify\MonorepoBuilder\Release\ReleaseWorker\SetCurrentMutualDependenciesReleaseWorker;
use Symplify\MonorepoBuilder\Release\ReleaseWorker\SetNextMutualDependenciesReleaseWorker;
use Symplify\MonorepoBuilder\Release\ReleaseWorker\TagVersionReleaseWorker;
use Symplify\MonorepoBuilder\Release\ReleaseWorker\UpdateBranchAliasReleaseWorker;
use Symplify\MonorepoBuilder\Release\ReleaseWorker\UpdateReplaceReleaseWorker;
use Symplify\MonorepoBuilder\ValueObject\Option;

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();
    $services = $containerConfigurator->services();

    $parameters->set(Option::DATA_TO_APPEND, [
        ComposerJsonSection::REQUIRE_DEV => [
            'ergebnis/composer-normalize' => '^2.2',
            'friendsofphp/php-cs-fixer' => '^3.0',
            'phpstan/phpstan' => '^0.12',
            'phpstan/phpstan-doctrine' => '^0.12',
            'phpstan/phpstan-phpunit' => '^0.12',
            'phpstan/phpstan-symfony' => '^0.12',
            'phpstan/phpstan-strict-rules' => '^0.12',
            'psalm/plugin-phpunit' => '^0.16',
            'psalm/plugin-symfony' => '^2.0',
            'symplify/monorepo-builder' => '^9.3',
            'vimeo/psalm' => '^4.0',
            'weirdan/doctrine-psalm-plugin' => '^1.0',
        ],
    ]);

    $services->set(UpdateReplaceReleaseWorker::class);
    $services->set(ComposerNormalizePreReleaseWorker::class);
    $services->set(TagVersionReleaseWorker::class);
    $services->set(PushTagReleaseWorker::class);
    $services->set(UpdateBranchAliasReleaseWorker::class);
    $services->set(ComposerNormalizePostReleaseWorker::class);
    $services->set(PushNextDevReleaseWorker::class);
};
