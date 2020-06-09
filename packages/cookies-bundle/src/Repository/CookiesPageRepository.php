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

use Doctrine\ORM\EntityManagerInterface;
use Runroom\CookiesBundle\Entity\CookiesPage;

class CookiesPageRepository
{
    /** @var EntityManagerInterface  */
    protected $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function find(): CookiesPage
    {
        $builder = $this->entityManager->createQueryBuilder();
        $query = $builder
            ->select('cookies_page')
            ->from('RunroomCookiesBundle:CookiesPage', 'cookies_page')
            ->getQuery();

        return $query->getSingleResult();
    }
}
