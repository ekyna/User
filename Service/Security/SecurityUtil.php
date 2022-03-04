<?php

declare(strict_types=1);

namespace Ekyna\Component\User\Service\Security;

/**
 * Class SecurityUtil
 * @package Ekyna\Component\User
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class SecurityUtil
{
    /**
     * Generate a new user password (8 chars).
     */
    public function generatePassword(): string
    {
        return bin2hex(random_bytes(4));
    }

    /**
     * Generate a new user token (128 chars).
     */
    public function generateToken(): string
    {
        return hash('sha512', bin2hex(random_bytes(32)));
    }
}
