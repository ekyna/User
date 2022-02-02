<?php

declare(strict_types=1);

namespace Ekyna\Component\User\Service\Security;

use Ekyna\Component\Resource\Manager\ResourceManagerInterface;
use Ekyna\Component\User\Model\UserInterface;
use Ekyna\Component\User\Repository\UserRepositoryInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface as SymfonyUser;
use Symfony\Component\Security\Core\User\UserProviderInterface;

use function get_class;
use function is_a;
use function sprintf;

/**
 * Class UserProvider
 * @package Ekyna\Component\User\Service\Security
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class UserProvider implements UserProviderInterface, PasswordUpgraderInterface
{
    private UserRepositoryInterface  $userRepository;
    private ResourceManagerInterface $userManager;

    public function __construct(
        UserRepositoryInterface  $userRepository,
        ResourceManagerInterface $userManager
    ) {
        $this->userRepository = $userRepository;
        $this->userManager = $userManager;
    }

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

    public function refreshUser(SymfonyUser $user): SymfonyUser
    {
        if (!$this->supportsClass($class = get_class($user))) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $class));
        }

        /** @var UserInterface $user */

        return $this->findUserByEmail($user->getEmail());
    }

    public function supportsClass(string $class): bool
    {
        return is_a($class, $this->userRepository->getClassName(), true);
    }

    /**
     * @param PasswordAuthenticatedUserInterface|SymfonyUser $user
     */
    public function upgradePassword($user, string $newHashedPassword): void
    {
        if (!$this->supportsClass(get_class($user))) {
            return;
        }

        /** @var UserInterface $user */

        $user->setPassword($newHashedPassword);

        $this->userManager->persist($user);
        $this->userManager->flush();
    }

    protected function findUserByEmail(string $email): UserInterface
    {
        /** @var UserInterface $user */
        if ($user = $this->userRepository->findOneByEmail($email)) {
            return $user;
        }

        $exception = new UserNotFoundException(sprintf('User with email "%s" not found.', $email));
        $exception->setUserIdentifier($email);

        throw $exception;
    }
}
