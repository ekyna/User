<?php

declare(strict_types=1);

namespace Ekyna\Component\User\Controller;

use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class OAuthController
 * @package Ekyna\Component\User\Controller
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class OAuthController
{
    private ClientRegistry $clientRegistry;
    private array          $config;

    public function __construct(ClientRegistry $clientRegistry, array $config)
    {
        $this->clientRegistry = $clientRegistry;
        $this->config = array_replace([
            'client' => null,
            'scopes'  => [],
            'options' => [],
        ], $config);
    }

    public function connect(): Response
    {
        $client = $this->clientRegistry->getClient($this->config['client']);

        return $client->redirect($this->config['scopes'], $this->config['options']);
    }

    public function check(): Response
    {
        return new Response();
    }
}
