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
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'runroom:user:create', description: 'Create a user.')]
final class CreateUserCommand extends Command
{
    public function __construct(private readonly UserManipulator $userManipulator)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('identifier', InputArgument::REQUIRED, 'The identifier')
            ->addArgument('password', InputArgument::REQUIRED, 'The password')
            ->addOption('inactive', null, InputOption::VALUE_NONE, 'Set the user as inactive')
            ->setHelp(
                <<<'EOT'
The <info>%command.full_name%</info> command creates a user:

  <info>php %command.full_name% my@email.com password</info>

You can create an inactive user (will not be able to log in):

  <info>php %command.full_name% my@email.com password --inactive</info>
EOT
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $identifier = $input->getArgument('identifier');
        $password = $input->getArgument('password');
        $inactive = $input->getOption('inactive');

        $this->userManipulator->create($identifier, $password, !$inactive);

        $output->writeln(sprintf('Created user <comment>%s</comment>', $identifier));

        return 0;
    }
}
