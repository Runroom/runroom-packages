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

namespace Runroom\UserBundle\Command;

use Runroom\UserBundle\Util\UserManipulator;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'runroom:user:deactivate', description: 'Deactivate a user.')]
final class DeactivateUserCommand extends Command
{
    // @todo: Remove static properties when support for Symfony < 5.4 is dropped.
    protected static $defaultName = 'runroom:user:deactivate';
    protected static $defaultDescription = 'Deactivate a user.';

    private UserManipulator $userManipulator;

    public function __construct(UserManipulator $userManipulator)
    {
        parent::__construct();

        $this->userManipulator = $userManipulator;
    }

    protected function configure(): void
    {
        \assert(null !== static::$defaultDescription);

        $this
            // @todo: Remove setDescription when support for Symfony < 5.4 is dropped.
            ->setDescription(static::$defaultDescription)
            ->addArgument('identifier', InputArgument::REQUIRED, 'The identifier')
            ->setHelp(
                <<<'EOT'
The <info>%command.full_name%</info> command deactivates a user (will not be able to log in):

  <info>php %command.full_name% my@email.com</info>
EOT
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $identifier = $input->getArgument('identifier');

        $this->userManipulator->deactivate($identifier);

        $output->writeln(sprintf('User "%s" has been deactivated.', $identifier));

        return 0;
    }
}
