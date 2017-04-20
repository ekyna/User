<?php

declare(strict_types=1);

namespace Ekyna\Component\User\Model;

use Ekyna\Component\Resource\Model\TimestampableTrait;

use function serialize;
use function unserialize;

/**
 * Class AbstractUser
 * @package Ekyna\Component\User
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
abstract class AbstractUser implements UserInterface
{
    use TimestampableTrait;

    protected ?int    $id       = null;
    protected ?string $email    = null;
    protected ?string $password = null;
    protected bool    $enabled  = false;

    /* ---------- (non mapped) ---------- */
    protected ?string $plainPassword = null;


    public function __toString(): string
    {
        return $this->email ?: 'New user';
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setEmail(string $email): UserInterface
    {
        $this->email = $email;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setPassword(?string $password): UserInterface
    {
        $this->password = $password;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): UserInterface
    {
        $this->enabled = $enabled;

        return $this;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(?string $plainPassword): UserInterface
    {
        $this->plainPassword = $plainPassword;

        return $this;
    }

    public function getRoles(): array
    {
        return ['ROLE_USER'];
    }

    public function getSalt(): ?string
    {
        return null;
    }

    public function getUserIdentifier(): string
    {
        return (string)$this->email;
    }

    /**
     * @TODO Remove
     */
    public function getUsername(): string
    {
        return (string)$this->email;
    }

    public function eraseCredentials()
    {
        $this->plainPassword = null;
    }

    public function serialize(): ?string
    {
        return serialize([
            $this->id,
            $this->email,
            $this->password,
        ]);
    }

    /**
     * @inheritDoc
     */
    public function unserialize($data): void
    {
        [
            $this->id,
            $this->email,
            $this->password,
        ] = unserialize($data, ['allowed_classes' => false]);
    }
}
