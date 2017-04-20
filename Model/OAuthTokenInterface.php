<?php

declare(strict_types=1);

namespace Ekyna\Component\User\Model;

use DateTime;

/**
 * Interface OAuthTokenInterface
 * @package Ekyna\Component\User\Model
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
interface OAuthTokenInterface
{
    /**
     * Returns the user.
     *
     * @return UserInterface|null
     */
    public function getUser(): ?UserInterface;

    /**
     * Sets the user.
     *
     * @param UserInterface $user
     *
     * @return OAuthTokenInterface
     */
    public function setUser(UserInterface $user): OAuthTokenInterface;

    /**
     * Returns the resource owner.
     *
     * @return string
     */
    public function getOwner(): ?string;

    /**
     * Sets the resource owner.
     *
     * @param string $owner
     *
     * @return OAuthTokenInterface
     */
    public function setOwner(string $owner): OAuthTokenInterface;

    /**
     * Returns the identifier.
     *
     * @return string
     */
    public function getIdentifier(): ?string;

    /**
     * Sets the identifier.
     *
     * @param string $identifier
     *
     * @return OAuthTokenInterface
     */
    public function setIdentifier(string $identifier): OAuthTokenInterface;

    /**
     * Returns the hash.
     *
     * @return string
     */
    public function getHash(): ?string;

    /**
     * Sets the hash.
     *
     * @param string $hash
     *
     * @return OAuthTokenInterface
     */
    public function setHash(string $hash): OAuthTokenInterface;

    /**
     * Returns the createdAt.
     *
     * @return DateTime
     */
    public function getCreatedAt(): DateTime;

    /**
     * Sets the createdAt.
     *
     * @param DateTime $createdAt
     *
     * @return OAuthTokenInterface
     */
    public function setCreatedAt(DateTime $createdAt): OAuthTokenInterface;

    /**
     * Returns the expiration date time.
     *
     * @return DateTime
     */
    public function getExpiresAt(): ?DateTime;

    /**
     * Sets the expiration date time.
     *
     * @param DateTime|null $expiresAt
     *
     * @return OAuthTokenInterface
     */
    public function setExpiresAt(?DateTime $expiresAt): OAuthTokenInterface;
}
