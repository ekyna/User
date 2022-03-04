<?php

declare(strict_types=1);

namespace Ekyna\Component\User\Controller;

use LogicException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Twig\Environment;

/**
 * Class SecurityController
 * @package Ekyna\Component\User\Controller
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class SecurityController
{
    protected AuthenticationUtils $authenticationUtils;
    protected Environment         $twig;
    protected array $config;

    public function __construct(
        AuthenticationUtils $authenticationUtils,
        Environment $twig,
        array $config
    ) {
        $this->authenticationUtils = $authenticationUtils;
        $this->twig = $twig;

        $this->config= array_replace([
            'template' => null,
            'remember_me' => null,
            'target_path' => null,
        ], $config);
    }

    /**
     * Security login.
     */
    public function login(Request $request): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        $content = $this->twig->render($this->config['template'], $this->getLoginParameters());

        $response = new Response($content);
        $response->setPrivate();

        return $response;
    }

    /**
     * Returns the login form vars.
     */
    protected function getLoginParameters(): array
    {
        // get the login error if there is one
        $error = $this->authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $this->authenticationUtils->getLastUsername();

        return [
            'last_username' => $lastUsername,
            'error'         => $error,
            'remember_me'   => $this->config['remember_me'],
            'target_path'   => $this->config['target_path'],
        ];
    }

    /**
     * Security check.
     */
    public function check(): Response
    {
        throw new LogicException(
            'This method can be blank - it will be intercepted by the check key on your firewall.'
        );
    }

    /**
     * Security logout.
     */
    public function logout(): Response
    {
        throw new LogicException(
            'This method can be blank - it will be intercepted by the logout key on your firewall.'
        );
    }
}
