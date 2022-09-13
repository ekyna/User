<?php

declare(strict_types=1);

namespace Ekyna\Component\User\EventListener;

use Ekyna\Component\Resource\Event\ResourceEventInterface;
use Ekyna\Component\Resource\Event\ResourceMessage;
use Ekyna\Component\Resource\Exception\UnexpectedTypeException;
use Ekyna\Component\Resource\Persistence\PersistenceHelperInterface;
use Ekyna\Component\User\Model\UserInterface;
use Ekyna\Component\User\Service\Security\SecurityUtil;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

use function sprintf;

/**
 * Class UserEventSubscriber
 * @package Ekyna\Component\User\EventListener
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
abstract class UserEventSubscriber
{
    protected readonly UserPasswordHasherInterface $passwordHasher;
    protected readonly SecurityUtil                $securityUtil;
    protected readonly PersistenceHelperInterface  $persistenceHelper;

    public function setPasswordHasher(UserPasswordHasherInterface $encoder): void
    {
        $this->passwordHasher = $encoder;
    }

    public function setSecurityUtil(SecurityUtil $securityUtil): void
    {
        $this->securityUtil = $securityUtil;
    }

    public function setPersistenceHelper(PersistenceHelperInterface  $persistenceHelper): void
    {
        $this->persistenceHelper = $persistenceHelper;
    }

    public function onPreCreate(ResourceEventInterface $event): void
    {
        $user = $this->getUserFromEvent($event);

        if (!empty($user->getPlainPassword())) {
            return;
        }

        $password = $this->securityUtil->generatePassword();

        $user->setPlainPassword($password);

        $event
            ->addMessage(new ResourceMessage(
                sprintf('Generated password : "%s".', $password),
                ResourceMessage::TYPE_INFO
            ))
            ->addData('password', $password);
    }

    public function onInsert(ResourceEventInterface $event): void
    {
        $user = $this->getUserFromEvent($event);

        $this->hashUserPassword($user);
    }

    public function onUpdate(ResourceEventInterface $event): void
    {
        $user = $this->getUserFromEvent($event);

        $this->hashUserPassword($user);
    }

    protected function hashUserPassword(UserInterface $user): void
    {
        if (empty($plain = $user->getPlainPassword())) {
            return;
        }

        $encoded = $this->passwordHasher->hashPassword($user, $plain);

        $user
            ->setPassword($encoded)
            ->eraseCredentials();

        $this->persistenceHelper->persistAndRecompute($user, false);
    }

    /**
     * Returns the user form the event.
     */
    protected function getUserFromEvent(ResourceEventInterface $event): UserInterface
    {
        $user = $event->getResource();

        if (!$user instanceof UserInterface) {
            throw new UnexpectedTypeException($user, UserInterface::class);
        }

        return $user;
    }
}

