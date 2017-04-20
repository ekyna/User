<?php

declare(strict_types=1);

namespace Ekyna\Component\User\Service;

use Ekyna\Component\User\Model\UserInterface;

/**
 * Interface UserProviderInterface
 * @package Ekyna\Component\User\Service
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
interface UserProviderInterface
{
    /**
     * Returns whether a user is available or not.
     *
     * @return bool
     */
    public function hasUser(): bool;

    /**
     * Returns the current user.
     *
     * @return UserInterface|null
     */
    public function getUser(): ?UserInterface;

    /**
     * Resets the user provider.
     */
    public function reset(): void;
}
