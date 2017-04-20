<?php

declare(strict_types=1);

namespace Ekyna\Component\User\Service;

use Ekyna\Component\User\Model\UserInterface;
use Ekyna\Component\User\Repository\UserRepositoryInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface as SymfonyUser;
use Symfony\Component\Security\Core\User\UserProviderInterface as SymfonyProvider;

use function get_class;
use function is_a;
use function sprintf;

/**
 * Class UserProvider
 * @package Ekyna\Component\User\Service
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
final class UserProvider implements SymfonyProvider, UserProviderInterface
{
    private UserRepositoryInterface $userRepository;
    private TokenStorageInterface   $tokenStorage;
    private string                  $userClass;

    private ?UserInterface $user        = null;
    private bool           $initialized = false;


    /**
     * Constructor.
     *
     * @param UserRepositoryInterface $userRepository
     * @param TokenStorageInterface   $tokenStorage
     * @param string                  $userClass
     */
    public function __construct(
        UserRepositoryInterface $userRepository,
        TokenStorageInterface $tokenStorage,
        string $userClass
    ) {
        $this->userRepository = $userRepository;
        $this->tokenStorage = $tokenStorage;
        $this->userClass = $userClass;
    }

    /**
     * @inheritDoc
     */
    public function loadUserByIdentifier(string $identifier): SymfonyUser
    {
        return $this->findUserByEmail($identifier);
    }

    /**
     * @inheritDoc
     *
     * @TODO Remove
     */
    public function loadUserByUsername($username): SymfonyUser
    {
        return $this->findUserByEmail($username);
    }

    /**
     * @inheritDoc
     */
    public function refreshUser(SymfonyUser $user)
    {
        if (!$this->supportsClass($class = get_class($user))) {
            throw new UnsupportedUserException(
                sprintf('Instances of "%s" are not supported.', $class)
            );
        }

        /** @var UserInterface $user */
        $email = $user->getEmail();

        return $this->findUserByEmail($email);
    }

    /**
     * @inheritDoc
     */
    public function supportsClass($class): bool
    {
        return is_a($class, $this->userClass, true);
    }

    /**
     * @inheritDoc
     */
    public function hasUser(): bool
    {
        $this->initialize();

        return null !== $this->user;
    }

    /**
     * @inheritDoc
     */
    public function getUser(): ?UserInterface
    {
        $this->initialize();

        return $this->user;
    }

    /**
     * @inheritDoc
     */
    public function reset(): void
    {
        $this->user = null;
        $this->initialized = false;
    }

    /**
     * Finds the user by its email.
     *
     * @param string $email
     *
     * @return UserInterface
     */
    protected function findUserByEmail(string $email): UserInterface
    {
        /** @var UserInterface $user */
        if ($user = $this->userRepository->findOneByEmail($email)) {
            return $user;
        }

        throw new UserNotFoundException(
            sprintf('No user registered for email "%s".', $email)
        );
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
