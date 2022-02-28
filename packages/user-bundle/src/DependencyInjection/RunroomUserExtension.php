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

namespace Runroom\UserBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\Security\Http\Authentication\AuthenticatorManager;
use SymfonyCasts\Bundle\ResetPassword\SymfonyCastsResetPasswordBundle;

/**
 * @phpstan-type UserBundleConfiguration = array{
 *     reset_password: array{
 *         enabled: bool,
 *         email: array{
 *             address: string,
 *             sender_name: string,
 *         },
 *         lifetime: int,
 *         throttle_limit: int,
 *         enable_garbage_collection: bool,
 *     }
 * }
 */
final class RunroomUserExtension extends Extension
{
    /**
     * @psalm-suppress UndefinedInterfaceMethod $bundles is an array
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $bundles = $container->getParameter('kernel.bundles');

        $configuration = new Configuration();
        /** @phpstan-var UserBundleConfiguration */
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new PhpFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('command.php');
        $loader->load('controller.php');
        $loader->load('form.php');
        $loader->load('repository.php');
        $loader->load('security.php');
        $loader->load('twig.php');
        $loader->load('util.php');

        /* @todo: Simplify this when dropping support for Symfony 4 */
        if (class_exists(AuthenticatorManager::class)) {
            $loader->load('security_sf5.php');
        }

        if (isset($bundles['SonataAdminBundle'])) {
            $loader->load('admin.php');

            $container->getDefinition('runroom_user.twig.global_variables')
                ->setArgument('$hasRequestPasswordEnabled', $config['reset_password']['enabled']);
        }

        if ($this->isConfigEnabled($container, $config['reset_password'])) {
            $this->registerReserPasswordConfiguration($container, $config['reset_password'], $loader);
        }
    }

    /**
     * @param array<string, int|bool|array> $config
     *
     * @phpstan-param array{
     *     enabled: bool,
     *     email: array{
     *         address: string,
     *         sender_name: string,
     *     },
     *     lifetime: int,
     *     throttle_limit: int,
     *     enable_garbage_collection: bool,
     * } $config
     *
     * @psalm-suppress UndefinedInterfaceMethod $bundles is an array
     */
    private function registerReserPasswordConfiguration(ContainerBuilder $container, array $config, PhpFileLoader $loader): void
    {
        $bundles = $container->getParameter('kernel.bundles');

        if (!class_exists(SymfonyCastsResetPasswordBundle::class) || !isset($bundles['SymfonyCastsResetPasswordBundle'])) {
            throw new \LogicException('Reset password support cannot be enabled as the SymfonyCastsResetPasswordBundle is not installed or not registered. Try running "composer require symfonycasts/reset-password-bundle".');
        }

        $loader->load('reset_password.php');

        if (isset($bundles['SonataAdminBundle'])) {
            $loader->load('admin_reset_password.php');
        }

        $container->getDefinition('runroom_user.reset_password.helper')
            ->setArgument('$resetRequestLifetime', $config['lifetime'])
            ->setArgument('$requestThrottleTime', $config['throttle_limit']);

        $container->getDefinition('runroom_user.reset_password.cleaner')
            ->setArgument('$enabled', $config['enable_garbage_collection']);

        $container->getDefinition('runroom_user.service.mailer')
            ->setArgument('$fromEmail', $config['email']['address'])
            ->setArgument('$fromName', $config['email']['sender_name']);
    }
}
