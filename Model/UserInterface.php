<?php

declare(strict_types=1);

namespace Ekyna\Component\User\Model;

use Ekyna\Component\Resource\Model\ResourceInterface;
use Ekyna\Component\Resource\Model\TimestampableInterface;
use Serializable;
use Symfony\Component\Security\Core\User\UserInterface as SymfonyUser;

/**
 * Interface UserInterface
 * @package Ekyna\Component\User
 * @author  Étienne Dauvergne <contact@ekyna.com>
 *
 * @TODO (Sf 6) implements PasswordAuthenticatedUserInterface
 */
interface UserInterface extends SymfonyUser, TimestampableInterface, ResourceInterface, Serializable
{
    /**
     * Sets the email.
     *
     * @param string $email
     *
     * @return $this|UserInterface
     */
    public function setEmail(string $email): UserInterface;

    /**
     * Returns the email.
     *
     * @return string|null
     */
    public function getEmail(): ?string;

    /**
     * Sets the password.
     *
     * @param string|null $password
     *
     * @return $this|UserInterface
     */
    public function setPassword(?string $password): UserInterface;

    /**
     * Returns whether the user is enabled.
     *
     * @return bool
     */
    public function isEnabled(): bool;

    /**
     * Sets whether the user is enabled.
     *
     * @param bool $enabled
     *
     * @return AbstractUser
     */
    public function setEnabled(bool $enabled): UserInterface;

    /**
     * Sets the plain password.
     * (non mapped)
     *
     * @param string|null $plainPassword
     *
     * @return $this|UserInterface
     */
    public function setPlainPassword(?string $plainPassword): UserInterface;

    /**
     * Returns the plain password.
     * (non mapped)
     *
     * @return string|null
     */
    public function getPlainPassword(): ?string;
}
