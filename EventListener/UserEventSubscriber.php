<?php

declare(strict_types=1);

namespace Ekyna\Component\User\EventListener;

use Ekyna\Component\Resource\Event\ResourceEventInterface;
use Ekyna\Component\Resource\Event\ResourceMessage;
use Ekyna\Component\Resource\Exception\UnexpectedTypeException;
use Ekyna\Component\User\Model\UserInterface;
use Ekyna\Component\User\Service\SecurityUtil;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

use function sprintf;

/**
 * Class UserEventSubscriber
 * @package Ekyna\Component\User\EventListener
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
abstract class UserEventSubscriber
{
    protected UserPasswordEncoderInterface $passwordHasher;
    protected SecurityUtil                $securityUtil;

    // @TODO (Sf 6) Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface
    public function setPasswordHasher(UserPasswordEncoderInterface $encoder): void
    {
        $this->passwordHasher = $encoder;
    }

    public function setSecurityUtil(SecurityUtil $securityUtil): void
    {
        $this->securityUtil = $securityUtil;
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

        $encoded = $this->passwordHasher->encodePassword($user, $plain);

        $user
            ->setPassword($encoded)
            ->eraseCredentials();
    }

    /**
     * Returns the user form the event.
     *
     * @param ResourceEventInterface $event
     *
     * @return UserInterface
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

