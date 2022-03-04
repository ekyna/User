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
    public function getUser(): ?UserInterface;

    public function setUser(UserInterface $user): OAuthTokenInterface;

    public function getOwner(): ?string;

    public function setOwner(string $owner): OAuthTokenInterface;

    public function getIdentifier(): ?string;

    public function setIdentifier(string $identifier): OAuthTokenInterface;

    public function getHash(): ?string;

    public function setHash(string $hash): OAuthTokenInterface;

    public function getCreatedAt(): DateTime;

    public function setCreatedAt(DateTime $createdAt): OAuthTokenInterface;

    public function getExpiresAt(): ?DateTime;

    public function setExpiresAt(?DateTime $expiresAt): OAuthTokenInterface;
}
