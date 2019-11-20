<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Exception\CategoryException;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity
 * @UniqueEntity("name")
 */
class Category
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
     * @ORM\ManyToOne(targetEntity="Category", inversedBy="children")
     */
    private $parent;

    /**
     * @ORM\OneToMany(targetEntity="Category", mappedBy="parent")
     */
    private $children;

    public function __construct(string $name, Category $parent = null)
    {
        $this->validateName($name);

        $this->name = $name;
        $this->parent = $parent;
        $this->children = new ArrayCollection();
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

    public function hasParent(): bool
    {
        return null !== $this->parent;
    }

    public function getParent(): Category
    {
        if (!$this->hasParent()) {
            throw CategoryException::hasNoParent($this);
        }

        return $this->parent;
    }

    public function moveTo(Category $parent): void
    {
        $this->parent = $parent;
    }

    public function removeParent(): void
    {
        $this->parent = null;
    }

    public function getParentNames(): array
    {
        $names = [];
        $category = $this;

        while ($category->hasParent()) {
            $parent = $category->getParent();
            array_unshift($names, $parent->getName());

            $category = $parent;
        }

        if (count($names) > 3) {
            $names = array_slice($names, 0, 2);
            $names[] = '...';
            $names[] = $this->getParent()->getName();
        }

        return $names;
    }

    public function hasChildren(): bool
    {
        return 0 !== $this->children->count();
    }

    private function validateName(string $name): void
    {
        if (strlen($name) < 3) {
            throw CategoryException::invalidName($name);
        }
    }
}
