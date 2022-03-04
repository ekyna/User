<?php

declare(strict_types=1);

namespace Ekyna\Component\User\Model;

use DateTimeInterface;
use Ekyna\Component\Resource\Model\ResourceInterface;
use Ekyna\Component\Resource\Model\TimestampableInterface;
use Serializable;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface as SymfonyUser;

/**
 * Interface UserInterface
 * @package Ekyna\Component\User
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
interface UserInterface extends PasswordAuthenticatedUserInterface, SymfonyUser, TimestampableInterface, ResourceInterface, Serializable
{
    public function setEmail(string $email): UserInterface;

    public function getEmail(): ?string;

    public function setPassword(?string $password): UserInterface;

    /**
     * Returns whether the user is enabled.
     */
    public function isEnabled(): bool;

    /**
     * Sets whether the user is enabled.
     */
    public function setEnabled(bool $enabled): UserInterface;

    public function getLastLogin(): ?DateTimeInterface;

    public function setLastLogin(?DateTimeInterface $lastLogin): UserInterface;

    /**
     * Sets the plain password.
     * (non mapped)
     */
    public function setPlainPassword(?string $plainPassword): UserInterface;

    /**
     * Returns the plain password.
     * (non mapped)
     */
    public function getPlainPassword(): ?string;
}
