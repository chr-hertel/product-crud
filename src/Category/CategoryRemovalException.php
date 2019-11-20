<?php

declare(strict_types=1);

namespace App\Category;

use DomainException;

class CategoryRemovalException extends DomainException
{
    public static function categoryNotFound(int $id): self
    {
        return new self(sprintf('Category with id %d not found', $id));
    }

    public static function hasChildCategories(): self
    {
        return new self('Cannot remove due to child category relation');
    }

    public static function hasProductRelation(): self
    {
        return new self('Cannot remove due to product relation');
    }
}
