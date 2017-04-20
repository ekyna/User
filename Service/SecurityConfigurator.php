<?php

declare(strict_types=1);

namespace Ekyna\Component\User\Service;

use ReflectionProperty;
use Symfony\Component\DependencyInjection\ContainerBuilder;

use function array_push;

/**
 * Class SecurityConfigurator
 * @package Ekyna\Component\User
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
final class SecurityConfigurator
{
    private static array $firewallPriority = [
        'dev' => INF,
    ];

    private array $security;

    /**
     * Configures the security.
     */
    public function configure(ContainerBuilder $container, array $config): void
    {
        $reflection = new ReflectionProperty(ContainerBuilder::class, 'extensionConfigs');
        $reflection->setAccessible(true);
        $property = $reflection->getValue($container);

        $this->security = $property['security'][0];

        foreach (['role_hierarchy', 'providers', 'password_hashers'] as $entry) {
            if (!isset($config[$entry])) {
                continue;
            }

            $this->configureEntry($entry, $config[$entry]);
        }

        if (isset($config['firewalls'])) {
            $this->configureFirewalls($config['firewalls']);
        }

        if (isset($config['access_control'])) {
            $this->configureAccessControl($config['access_control']);
        }

        $property['security'][0] = $this->security;

        $reflection->setValue($container, $property);
    }

    private function configureEntry(string $entry, array $config): void
    {
        $values = $this->security[$entry] ?? [];

        foreach ($config as $name => $value) {
            if (isset($values[$name])) {
                continue;
            }

            $values[$name] = $value;
        }

        $this->security[$entry] = $values;
    }

    private function configureFirewalls(array $config): void
    {
        $firewalls = $this->security['firewalls'] ?? [];

        foreach ($config as $name => $firewall) {
            if (isset($firewall['_priority'])) {
                self::$firewallPriority[$name] = $firewall['_priority'];
            } elseif (!isset(self::$firewallPriority)) {
                self::$firewallPriority[$name] = 0;
            }

            unset($firewall['_priority']);

            $firewalls[$name] = $firewall;
        }

        uksort($firewalls, function ($a, $b) {
            $a = self::$firewallPriority[$a] ?? 0;
            $b = self::$firewallPriority[$b] ?? 0;

            if ($a === $b) {
                return 0;
            }

            return ($a < $b) ? 1 : -1;
        });

        $this->security['firewalls'] = $firewalls;
    }

    private function configureAccessControl(array $config): void
    {
        $rules = $this->security['access_control'] ?? [];

        array_push($rules, ...$config);

        $this->security['access_control'] = $rules;
    }
}
