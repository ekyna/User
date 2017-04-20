<?php

declare(strict_types=1);

namespace Ekyna\Component\User\Service\OAuth;

use Ekyna\Component\User\Exception\RuntimeException;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

use function array_keys;
use function in_array;
use function rtrim;
use function sprintf;

/**
 * Class RoutingLoader
 * @package Ekyna\Component\User\Service\OAuth
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class RoutingLoader extends Loader
{
    private ClientRegistry $clientRegistry;
    private string $name;
    private string  $prefix;
    private bool   $loaded = false;

    public function __construct(ClientRegistry $clientRegistry, string $name, string $prefix, string $env = null)
    {
        parent::__construct($env);

        $this->clientRegistry = $clientRegistry;
        $this->name = $name;
        $this->prefix = $prefix;
    }

    /**
     * @inheritDoc
     */
    public function load($resource, string $type = null)
    {
        if ($this->loaded) {
            throw new RuntimeException(sprintf('Do not add the \'%s_oauth\' routes loader twice.', $this->name));
        }

        $this->loaded = true;

        $collection = new RouteCollection();

        $clients = $this->clientRegistry->getEnabledClientKeys();

        $pathPrefix = rtrim($this->prefix, '/');

        foreach (array_keys(OAuthConfigurator::OWNERS) as $owner) {
            if (!in_array($this->name . '_' . $owner, $clients, true)) {
                continue;
            }

            $route = new Route($pathPrefix . '/oauth/' . $owner . '/connect');
            $route->setDefault('_controller', OAuthConfigurator::controller($this->name, $owner, false));
            $collection->add(OAuthConfigurator::route($this->name, $owner, false), $route);

            $route = new Route($pathPrefix . '/oauth/' . $owner . '/check');
            $route->setDefault('_controller', OAuthConfigurator::controller($this->name, $owner, true));
            $collection->add(OAuthConfigurator::route($this->name, $owner, true), $route);
        }

        return $collection;
    }

    /**
     * @inheritDoc
     */
    public function supports($resource, string $type = null): bool
    {
        return $this->name . '_oauth' === $type;
    }
}
