<?php

declare(strict_types=1);

namespace Ekyna\Component\User\EventListener;

use DateTime;
use Ekyna\Component\Resource\Manager\ResourceManagerInterface;
use Ekyna\Component\User\Model\UserInterface;
use Ekyna\Component\User\Service\UserProviderInterface;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;
use Symfony\Component\Security\Http\Event\LogoutEvent;

/**
 * Class SecurityEventListener
 * @package Ekyna\Component\User\EventListener
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class SecurityEventListener
{
    protected UserProviderInterface    $userProvider;
    protected ResourceManagerInterface $userManager;

    public function __construct(UserProviderInterface $userProvider, ResourceManagerInterface $userManager)
    {
        $this->userProvider = $userProvider;
        $this->userManager = $userManager;
    }

    public function onLoginSuccess(LoginSuccessEvent $event): void
    {
        $this->userProvider->reset();

        $user = $event->getUser();

        if (!$user instanceof UserInterface) {
            return;
        }

        $user->setLastLogin(new DateTime());

        $this->userManager->persist($user);
        $this->userManager->flush();
    }

    public function onLogout(LogoutEvent $event): void
    {
        $this->userProvider->reset();
    }
}
