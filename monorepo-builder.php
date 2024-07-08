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
use Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection;
use Symplify\MonorepoBuilder\Config\MBConfig;
use Symplify\MonorepoBuilder\Release\ReleaseWorker\PushNextDevReleaseWorker;
use Symplify\MonorepoBuilder\Release\ReleaseWorker\PushTagReleaseWorker;
use Symplify\MonorepoBuilder\Release\ReleaseWorker\SetCurrentMutualDependenciesReleaseWorker;
use Symplify\MonorepoBuilder\Release\ReleaseWorker\SetNextMutualDependenciesReleaseWorker;
use Symplify\MonorepoBuilder\Release\ReleaseWorker\TagVersionReleaseWorker;
use Symplify\MonorepoBuilder\Release\ReleaseWorker\UpdateBranchAliasReleaseWorker;
use Symplify\MonorepoBuilder\Release\ReleaseWorker\UpdateReplaceReleaseWorker;

return static function (MBConfig $mBConfig): void {
    $mBConfig->packageDirectories([
        __DIR__ . '/packages',
    ]);

    $mBConfig->dataToAppend([
        ComposerJsonSection::REQUIRE_DEV => [
            'ergebnis/composer-normalize' => '^2.6',
            'friendsofphp/php-cs-fixer' => '^3.14',
            'phpstan/phpstan' => '^1.10',
            'phpstan/phpstan-doctrine' => '^1.0',
            'phpstan/phpstan-phpunit' => '^1.0',
            'phpstan/phpstan-symfony' => '^1.0',
            'phpstan/phpstan-strict-rules' => '^1.0',
            'psalm/plugin-phpunit' => '^0.18',
            'psalm/plugin-symfony' => '^5.0',
            'rector/rector' => '^1.0',
            'symplify/monorepo-builder' => '^11.0 <11.1',
            'vimeo/psalm' => '^5.1',
            'weirdan/doctrine-psalm-plugin' => '^2.6',
        ],
    ]);

    $mBConfig->workers([
        UpdateReplaceReleaseWorker::class,
        SetCurrentMutualDependenciesReleaseWorker::class,
        ComposerNormalizePreReleaseWorker::class,
        TagVersionReleaseWorker::class,
        PushTagReleaseWorker::class,
        SetNextMutualDependenciesReleaseWorker::class,
        UpdateBranchAliasReleaseWorker::class,
        ComposerNormalizePostReleaseWorker::class,
        PushNextDevReleaseWorker::class,
    ]);
};
