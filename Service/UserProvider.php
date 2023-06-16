<?php

declare(strict_types=1);

namespace Ekyna\Component\User\Service;

use Ekyna\Component\User\Model\UserInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Class UserProvider
 * @package Ekyna\Component\User\Service
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
final class UserProvider implements UserProviderInterface
{
    private TokenStorageInterface $tokenStorage;
    private string                $userClass;

    private ?UserInterface $user        = null;
    private bool           $initialized = false;

    public function __construct(
        TokenStorageInterface $tokenStorage,
        string                $userClass
    ) {
        $this->tokenStorage = $tokenStorage;
        $this->userClass = $userClass;
    }

    public function hasUser(): bool
    {
        $this->initialize();

        return null !== $this->user;
    }

    public function getUser(): ?UserInterface
    {
        $this->initialize();

        return $this->user;
    }

    public function reset(): void
    {
        $this->user = null;
        $this->initialized = false;
    }

    public function onClear(): void
    {
        $this->reset();
    }

    /**
     * Loads the user once.
     */
    private function initialize(): void
    {
        if ($this->initialized) {
            return;
        }

        $this->initialized = true;

        if (null === $token = $this->tokenStorage->getToken()) {
            return;
        }

        $user = $token->getUser();
        if (!$user instanceof $this->userClass) {
            return;
        }

        /** @var UserInterface $user */
        $this->user = $user;
    }
}
