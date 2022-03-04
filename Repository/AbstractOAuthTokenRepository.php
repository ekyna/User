<?php

declare(strict_types=1);

namespace Ekyna\Component\User\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Ekyna\Component\User\Model\OAuthTokenInterface;
use Ekyna\Component\User\Model\UserInterface;
use LogicException;

use function sprintf;

/**
 * Class AbstractOAuthTokenRepository
 * @package Ekyna\Component\User\Repository
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
abstract class AbstractOAuthTokenRepository extends EntityRepository implements OAuthTokenRepositoryInterface
{
    /**
     * @param string $entityClass The class name of the entity this repository manages
     */
    public function __construct(ManagerRegistry $registry, string $entityClass)
    {
        $manager = $registry->getManagerForClass($entityClass);

        if ($manager === null) {
            throw new LogicException(sprintf(
                'Could not find the entity manager for class "%s". Check your Doctrine configuration to make sure it is configured to load this entity’s metadata.',
                $entityClass
            ));
        }

        /** @noinspection PhpParamsInspection */
        parent::__construct($manager, $manager->getClassMetadata($entityClass));
    }

    public function findOneByUser(UserInterface $user, string $owner): ?OAuthTokenInterface
    {
        $qb = $this->createQueryBuilder('t');
        $qb
            ->andWhere($qb->expr()->eq('t.owner', ':owner'))
            ->andWhere($qb->expr()->eq('t.user', ':user'));

        return $qb
            ->setMaxResults(1)
            ->getQuery()
            ->useQueryCache(true)
            ->setParameters([
                'user'  => $user,
                'owner' => $owner,
            ])
            ->getOneOrNullResult();
    }

    public function findOneByIdentifier(string $identifier, string $owner): ?OAuthTokenInterface
    {
        $qb = $this->createQueryBuilder('t');
        $qb
            ->andWhere($qb->expr()->eq('t.owner', ':owner'))
            ->andWhere($qb->expr()->eq('t.identifier', ':identifier'));

        return $qb
            ->setMaxResults(1)
            ->getQuery()
            ->useQueryCache(true)
            ->setParameters([
                'identifier' => $identifier,
                'owner'      => $owner,
            ])
            ->getOneOrNullResult();
    }
}
