<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Category;
use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Product>
 *
 * @method Product[] findByCategory(Category $category)
 */
final class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    /**
     * @return Paginator<Product>
     */
    public function search(string $query): Paginator
    {
        $queryBuilder = $this->createQueryBuilder('p')
            ->where('p.name LIKE :query')
            ->setParameter('query', sprintf('%%%s%%', $query));

        return new Paginator($queryBuilder);
    }
}
