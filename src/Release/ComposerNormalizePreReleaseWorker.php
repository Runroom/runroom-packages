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

namespace App\Release;

use PharIo\Version\Version;
use Symplify\MonorepoBuilder\Release\Contract\ReleaseWorker\ReleaseWorkerInterface;
use Symplify\MonorepoBuilder\Release\Process\ProcessRunner;

class ComposerNormalizePreReleaseWorker implements ReleaseWorkerInterface
{
    /** @var ProcessRunner */
    protected $processRunner;

    public function __construct(ProcessRunner $processRunner)
    {
        $this->processRunner = $processRunner;
    }

    public function getPriority(): int
    {
        return 450;
    }

    public function work(Version $version): void
    {
        $this->processRunner->run('composer normalize-run');
    }

    public function getDescription(Version $version): string
    {
        return sprintf('Composer normalize');
    }
}
