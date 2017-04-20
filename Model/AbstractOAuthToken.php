<?php

declare(strict_types=1);

namespace Ekyna\Component\User\Model;

use DateTime;

/**
 * Class OAuthToken
 * @package Ekyna\Bundle\UserBundle\Entity
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
abstract class AbstractOAuthToken implements OAuthTokenInterface
{
    protected ?int           $id         = null;
    protected ?UserInterface $user       = null;
    protected ?string        $owner      = null;
    protected ?string        $identifier = null;
    protected ?string        $hash       = null;
    protected DateTime       $createdAt;
    protected ?DateTime      $expiresAt  = null;


    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->createdAt = new DateTime();
    }

    /**
     * Returns the id.
     *
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?UserInterface
    {
        return $this->user;
    }

    public function setUser(UserInterface $user): OAuthTokenInterface
    {
        $this->user = $user;

        return $this;
    }

    public function getOwner(): ?string
    {
        return $this->owner;
    }

    public function setOwner(string $owner): OAuthTokenInterface
    {
        $this->owner = $owner;

        return $this;
    }

    public function getIdentifier(): ?string
    {
        return $this->identifier;
    }

    public function setIdentifier(string $identifier): OAuthTokenInterface
    {
        $this->identifier = $identifier;

        return $this;
    }

    public function getHash(): ?string
    {
        return $this->hash;
    }

    public function setHash(string $hash): OAuthTokenInterface
    {
        $this->hash = $hash;

        return $this;
    }

    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTime $createdAt): OAuthTokenInterface
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getExpiresAt(): ?DateTime
    {
        return $this->expiresAt;
    }

    public function setExpiresAt(?DateTime $expiresAt): OAuthTokenInterface
    {
        $this->expiresAt = $expiresAt;

        return $this;
    }
}
