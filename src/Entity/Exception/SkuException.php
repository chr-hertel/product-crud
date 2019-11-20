<?php

declare(strict_types=1);

namespace App\Entity\Exception;

use DomainException;

class SkuException extends DomainException
{
    public static function invalidSku(string $sku): self
    {
        return new self(sprintf('Given sku "%s" doesn\'t match the expected format', $sku));
    }
}
