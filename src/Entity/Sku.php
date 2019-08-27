<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Exception\SkuException;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Embeddable
 */
class Sku
{
    /**
     * @ORM\Column(length=10)
     */
    private $sku;

    public function __construct(string $sku)
    {
        if (1 !== preg_match('/[A-Z]{2}\d{2}-[a-z0-9]{5}/', $sku)) {
            throw SkuException::invalidSku($sku);
        }

        $this->sku = $sku;
    }

    public function toString(): string
    {
        return $this->sku;
    }
}
