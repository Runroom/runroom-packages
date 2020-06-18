<?php

declare(strict_types=1);

/*
 * This file is part of the Runroom package.
 *
 * (c) Runroom <runroom@runroom.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Runroom\RedirectionBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;
use Runroom\RedirectionBundle\Entity\Redirect;

/**
 * @final
 * @extends ServiceEntityRepository<Redirect>
 */
class RedirectRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Redirect::class);
    }

    /** @return array{ destination: string, httpCode: string }|null */
    public function findRedirect(string $source): ?array
    {
        $query = $this->createQueryBuilder('redirect')
            ->select('redirect.destination, redirect.httpCode')
            ->where('redirect.source = :source')
            ->andWhere('redirect.publish = :publish')
            ->setParameter('source', $source)
            ->setParameter('publish', true)
            ->getQuery();

        return $query->getOneOrNullResult(Query::HYDRATE_SCALAR);
    }
}
