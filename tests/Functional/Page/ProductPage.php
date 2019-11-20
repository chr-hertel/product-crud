<?php

declare(strict_types=1);

namespace App\Tests\Functional\Page;

class ProductPage
{
    public const URI = '/product';

    public const FORM_SUBMIT = 'Submit';

    public const FORM_DATA_VALID = [
        'product[name]' => 'Rocket',
        'product[sku]' => 'TE01-rock1',
        'product[category]' => '1',
        'product[price][amount]' => '0.59',
        'product[price][tax]' => '7',
        'product[price][currency]' => 'EUR',
    ];
}
