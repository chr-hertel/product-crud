<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Exception\ProductException;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class Product
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id = 0;

    /**
     * @ORM\Column(unique=true)
     */
    private $name;

    /**
     * @ORM\Embedded(class="Sku", columnPrefix=false)
     */
    private $sku;

    /**
     * @ORM\ManyToOne(targetEntity="Category")
     */
    private $category;

    /**
     * @ORM\Embedded(class="Price")
     */
    private $price;

    public function __construct(string $name, Sku $sku, Category $category, Price $price)
    {
        $this->validateName($name);

        $this->name = $name;
        $this->sku = $sku;
        $this->category = $category;
        $this->price = $price;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function rename(string $name): void
    {
        $this->validateName($name);

        $this->name = $name;
    }

    public function getSku(): Sku
    {
        return $this->sku;
    }

    public function getCategory(): Category
    {
        return $this->category;
    }

    public function categorize(Category $category): void
    {
        $this->category = $category;
    }

    public function getPrice(): Price
    {
        return $this->price;
    }

    public function costs(Price $price): void
    {
        $this->price = $price;
    }

    private function validateName(string $name): void
    {
        if (strlen($name) < 3) {
            throw ProductException::invalidName($name);
        }
    }
}
