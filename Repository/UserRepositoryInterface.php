<?php

declare(strict_types=1);

namespace Ekyna\Component\User\Repository;

use Ekyna\Component\Resource\Repository\ResourceRepositoryInterface;
use Ekyna\Component\User\Model\UserInterface;

/**
 * Interface UserRepositoryInterface
 * @package Ekyna\Component\User\Repository
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
interface UserRepositoryInterface extends ResourceRepositoryInterface
{
    /**
     * Finds one user with the given email.
     *
     * @param string $email   The user's email address
     * @param bool   $enabled Whether to filter enabled users
     */
    public function findOneByEmail(string $email, bool $enabled = true): ?UserInterface;
}
