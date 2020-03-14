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

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query;

/**
 * @final
 */
class RedirectRepository
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function findRedirect(string $source): ?array
    {
        $builder = $this->entityManager->createQueryBuilder();
        $query = $builder
            ->select('redirect.destination, redirect.httpCode')
            ->from('RunroomRedirectionBundle:Redirect', 'redirect')
            ->where('redirect.source = :source')
            ->andWhere('redirect.publish = :publish')
            ->setParameter('source', $source)
            ->setParameter('publish', true)
            ->getQuery();

        return $query->getOneOrNullResult(Query::HYDRATE_SCALAR);
    }
}
