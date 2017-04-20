<?php

declare(strict_types=1);

namespace Ekyna\Component\User\Repository;

use Doctrine\Persistence\ObjectRepository;
use Ekyna\Component\User\Model\OAuthTokenInterface;
use Ekyna\Component\User\Model\UserInterface;

/**
 * Interface OAuthTokenRepositoryInterface
 * @package Ekyna\Component\User\Repository
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
interface OAuthTokenRepositoryInterface extends ObjectRepository
{
    public function findOneByUser(UserInterface $user, string $owner): ?OAuthTokenInterface;

    public function findOneByIdentifier(string $identifier, string $owner): ?OAuthTokenInterface;
}
