<?php

declare(strict_types=1);

namespace Ekyna\Component\User\Service\OAuth;

use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Security\Authenticator\OAuth2Authenticator;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\Passport\PassportInterface;

/**
 * Class OAuthAuthenticator
 * @package Ekyna\Component\User\Service\OAuth
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class OAuthAuthenticator extends OAuth2Authenticator
{
    private ClientRegistry         $clientRegistry;
    private OAuthPassportGenerator $passportGenerator;
    private UrlGeneratorInterface  $urlGenerator;

    private array $config;

    public function __construct(
        ClientRegistry         $clientRegistry,
        OAuthPassportGenerator $passportGenerator,
        UrlGeneratorInterface  $urlGenerator,
        array                  $config
    ) {
        $this->clientRegistry = $clientRegistry;
        $this->passportGenerator = $passportGenerator;
        $this->urlGenerator = $urlGenerator;

        $this->config = array_replace([
            'create_user'  => false,
            'target_route' => null,
            'client_name'  => null,
            'check_route'  => null,
        ], $config);
    }

    public function supports(Request $request): ?bool
    {
        return $request->attributes->get('_route') === $this->config['check_route'];
    }

    public function authenticate(Request $request): PassportInterface
    {
        $client = $this->clientRegistry->getClient($this->config['client_name']);

        $oAuthToken = $this->fetchAccessToken($client);

        return $this
            ->passportGenerator
            ->generate(
                $oAuthToken,
                $client,
                $this->config['client_name'],
                $this->config['create_user']
            );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        if ($this->config['target_route']) {
            $targetUrl = $this->urlGenerator->generate($this->config['target_route']);

            return new RedirectResponse($targetUrl);
        }

        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        $message = strtr($exception->getMessageKey(), $exception->getMessageData());

        return new Response($message, Response::HTTP_FORBIDDEN);
    }
}
