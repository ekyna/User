<?php

declare(strict_types=1);

namespace Ekyna\Component\User\Repository;

use Ekyna\Component\Resource\Doctrine\ORM\Repository\ResourceRepository;
use Ekyna\Component\User\Model\UserInterface;

/**
 * Class UserRepository
 * @package Ekyna\Component\User\Repository
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class UserRepository extends ResourceRepository implements UserRepositoryInterface
{
    /**
     * @inheritDoc
     */
    public function findOneByEmail(string $email, bool $enabled = true): ?UserInterface
    {
        $qb = $this->createQueryBuilder('u');

        $parameters = [
            'email' => $email,
        ];

        if ($enabled) {
            $qb->andWhere($qb->expr()->eq('u.enabled', ':enabled'));
            $parameters['enabled'] = true;
        }

        /** @noinspection PhpUnhandledExceptionInspection */
        return $qb
            ->andWhere($qb->expr()->eq('u.email', ':email'))
            ->getQuery()
            ->useQueryCache(true)
            ->setParameters($parameters)
            ->getOneOrNullResult();
    }
}
