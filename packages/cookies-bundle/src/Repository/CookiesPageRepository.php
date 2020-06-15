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

namespace Runroom\CookiesBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Runroom\CookiesBundle\Entity\CookiesPage;

class CookiesPageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CookiesPage::class);
    }

    public function findCookiesPage(): CookiesPage
    {
        $query = $this->createQueryBuilder('cookies_page')
            ->select('cookies_page')
            ->from('RunroomCookiesBundle:CookiesPage', 'cookies_page')
            ->getQuery();

        return $query->getSingleResult();
    }
}
