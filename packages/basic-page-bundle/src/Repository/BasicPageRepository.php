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

namespace Runroom\BasicPageBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;
use Runroom\BasicPageBundle\Entity\BasicPage;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * @final
 * @extends ServiceEntityRepository<BasicPage>
 */
class BasicPageRepository extends ServiceEntityRepository
{
    /** @var RequestStack */
    private $requestStack;

    public function __construct(ManagerRegistry $registry, RequestStack $requestStack)
    {
        parent::__construct($registry, BasicPage::class);

        $this->requestStack = $requestStack;
    }

    public function findBySlug(string $slug): BasicPage
    {
        $request = $this->requestStack->getCurrentRequest() ?? new Request();

        $query = $this->createQueryBuilder('basic_page')
            ->leftJoin('basic_page.translations', 'translations', Join::WITH, 'translations.locale = :locale')
            ->where('translations.slug = :slug')
            ->andWhere('basic_page.publish = true')
            ->setParameter('slug', $slug)
            ->setParameter('locale', $request->getLocale())
            ->getQuery();

        return $query->getSingleResult();
    }
}
