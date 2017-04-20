<?php

declare(strict_types=1);

namespace Ekyna\Component\User\Service;

/**
 * Class SecurityUtil
 * @package Ekyna\Component\User
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class SecurityUtil
{
    /**
     * Generate a new user password (8 chars).
     *
     * @return string The generated password.
     *
     * @noinspection PhpDocMissingThrowsInspection
     */
    public function generatePassword(): string
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        return bin2hex(random_bytes(4));
    }

    /**
     * Generate a new user token (128 chars).
     *
     * @return string The generated token.
     *
     * @noinspection PhpDocMissingThrowsInspection
     */
    public function generateToken(): string
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        return hash('sha512', bin2hex(random_bytes(32)));
    }
}
