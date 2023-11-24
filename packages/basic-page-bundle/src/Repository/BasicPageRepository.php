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
use Doctrine\Persistence\ManagerRegistry;
use Runroom\BasicPageBundle\Entity\BasicPage;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * @extends ServiceEntityRepository<BasicPage>
 */
final class BasicPageRepository extends ServiceEntityRepository implements BasicPageRepositoryInterface
{
    public function __construct(
        ManagerRegistry $registry,
        private readonly RequestStack $requestStack
    ) {
        parent::__construct($registry, BasicPage::class);
    }

    public function findBySlug(string $slug): BasicPage
    {
        $request = $this->requestStack->getCurrentRequest() ?? new Request();

        $query = $this->createQueryBuilder('basic_page')
            ->leftJoin('basic_page.translations', 'translations')
            ->where('translations.locale = :locale')
            ->andWhere('translations.slug = :slug')
            ->andWhere('basic_page.publish = true')
            ->setParameter('slug', $slug)
            ->setParameter('locale', $request->getLocale())
            ->getQuery();

        $basicPage = $query->getSingleResult();
        \assert($basicPage instanceof BasicPage);

        return $basicPage;
    }

    public function findPublished(?string $location = null): array
    {
        $criteria = ['publish' => true];

        if (null !== $location) {
            $criteria['location'] = $location;
        }

        return $this->findBy($criteria);
    }
}
