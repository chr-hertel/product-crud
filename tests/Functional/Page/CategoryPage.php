<?php

declare(strict_types=1);

namespace App\Tests\Functional\Page;

class CategoryPage
{
    public const URI = '/category';

    public const FORM_SUBMIT = 'Submit';

    public const FORM_DATA_VALID_WITHOUT_PARENT = [
        'category[name]' => 'Vegetable',
    ];

    public const FORM_DATA_VALID_WITHOUT_PARENT_TOO_SHORT_NAME = [
        'category[name]' => 'Fo',
    ];

    public const FORM_DATA_VALID_WITH_PARENT = [
        'category[name]' => 'Vegetable',
        'category[parent]' => 1,
    ];
}
