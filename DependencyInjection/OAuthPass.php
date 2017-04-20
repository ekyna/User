<?php

declare(strict_types=1);

namespace Ekyna\Component\User\DependencyInjection;

use Ekyna\Component\User\Controller\OAuthController;
use Ekyna\Component\User\Service\OAuth\OAuthAuthenticator;
use Ekyna\Component\User\Service\OAuth\OAuthConfigurator;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

use function array_merge;
use function array_replace;

/**
 * Class OAuthPass
 * @package Ekyna\Component\User\DependencyInjection
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class OAuthPass implements CompilerPassInterface
{
    private string $name;
    private string $generator;
    private array  $config;

    public function __construct(string $name, string $generator, array $config)
    {
        $this->name = $name;
        $this->generator = $generator;
        $this->config = array_replace([
            'target_route' => null,
            'create_user'  => false,
        ], $config);
    }

    public function process(ContainerBuilder $container): void
    {
        if (!$container->has('knpu.oauth2.registry')) {
            return;
        }

        foreach (OAuthConfigurator::OWNERS as $owner => $config) {
            $clientName = $this->name . '_' . $owner;

            if (!$container->has('knpu.oauth2.client.' . $clientName)) {
                continue;
            }

            // Configure controller
            $container
                ->register(OAuthConfigurator::controller($this->name, $owner, null), OAuthController::class)
                ->setArguments([
                    new Reference('knpu.oauth2.registry'),
                    array_merge($config, [
                        'client' => $clientName,
                    ])
                ])
                ->setPublic(true);

            // Configure authenticator
            $container
                ->register(OAuthConfigurator::authenticator($this->name, $owner), OAuthAuthenticator::class)
                ->setArguments([
                    new Reference('knpu.oauth2.registry'),
                    new Reference($this->generator),
                    new Reference('router'),
                    array_merge($this->config, [
                        'client_name' => $clientName,
                        'check_route' => OAuthConfigurator::route($this->name, $owner, true),
                    ])
                ]);
        }
    }
}
