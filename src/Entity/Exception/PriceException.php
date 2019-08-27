<?php

declare(strict_types=1);

namespace App\Entity\Exception;

use DomainException;

class PriceException extends DomainException
{
    private $property = '';

    public static function invalidAmount(int $value): self
    {
        $priceException = new self(sprintf('Value "%s" is not a valid price value', $value));
        $priceException->property = 'amount';

        return $priceException;
    }

    public static function invalidTax(int $tax): self
    {
        $priceException = new self(sprintf('Tax rate "%s" is not valid for a price', $tax));
        $priceException->property = 'tax';

        return $priceException;
    }

    public static function invalidCurrency(string $currency): self
    {
        $priceException = new self(sprintf('The given currency "%s" is invalid.', $currency));
        $priceException->property = 'currency';

        return $priceException;
    }

    public function getProperty(): string
    {
        return $this->property;
    }
}
