<?php

declare(strict_types=1);

namespace App\Category;

use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;

class Removal
{
    private $entityManager;
    private $categoryRepository;
    private $productRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        CategoryRepository $categoryRepository,
        ProductRepository $productRepository
    ) {
        $this->entityManager = $entityManager;
        $this->categoryRepository = $categoryRepository;
        $this->productRepository = $productRepository;
    }

    public function remove(int $id): void
    {
        $category = $this->categoryRepository->find($id);

        if (null === $category) {
            throw CategoryRemovalException::categoryNotFound($id);
        }

        $children = $this->categoryRepository->findByParent($category);

        if (0 !== count($children)) {
            throw CategoryRemovalException::hasChildCategories();
        }

        $products = $this->productRepository->findByCategory($category);

        if (0 !== count($products)) {
            throw CategoryRemovalException::hasProductRelation();
        }

        $this->entityManager->remove($category);
        $this->entityManager->flush();
    }
}
