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
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class DeactivateUserCommand extends Command
{
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
            ->setDescription(static::$defaultDescription)
            ->addArgument('email', InputArgument::REQUIRED, 'The email')
            ->setHelp(<<<'EOT'
The <info>%command.full_name%</info> command deactivates a user (will not be able to log in):

  <info>php %command.full_name% my@email.com</info>
EOT
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $email = $input->getArgument('email');

        $this->userManipulator->deactivate($email);

        $output->writeln(sprintf('User "%s" has been deactivated.', $email));

        return 0;
    }
}
