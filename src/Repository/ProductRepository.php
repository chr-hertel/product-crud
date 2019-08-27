<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Category;
use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * @method Product[] findByCategory(Category $category)
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    public function search(string $query): Paginator
    {
        $queryBuilder = $this->createQueryBuilder('p')
            ->where('p.name LIKE :query')
            ->setParameter('query', sprintf('%%%s%%', $query));

        return new Paginator($queryBuilder);
    }
}
