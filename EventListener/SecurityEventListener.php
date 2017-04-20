<?php

declare(strict_types=1);

namespace Ekyna\Component\User\EventListener;

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
    protected UserProviderInterface $userProvider;

    public function __construct(UserProviderInterface $userProvider)
    {
        $this->userProvider = $userProvider;
    }

    public function onLoginSuccess(LoginSuccessEvent $event): void
    {
        $this->userProvider->reset();
    }

    public function onLogout(LogoutEvent $event): void
    {
        $this->userProvider->reset();
    }
}
