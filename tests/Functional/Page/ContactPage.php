<?php

declare(strict_types=1);

namespace App\Tests\Functional\Page;

class ContactPage
{
    public const URI = '/contact';

    public const FORM_SUBMIT = 'Submit';
    public const FORM_DATA_VALID = [
        'contact[name]' => 'Jane Doe',
        'contact[email]' => 'jane@example.com',
        'contact[subject]' => 'Test',
        'contact[message]' => 'Hello World!',
    ];

    public const FORM_DATA_EMPTY = [];
    public const FORM_DATA_MISSING_EMAIL = [
        'contact[name]' => 'Jane Doe',
        'contact[email]' => '',
        'contact[subject]' => 'Test',
        'contact[message]' => 'Hello World!',
    ];
    public const FORM_DATA_INVALID_EMAIL = [
        'contact[name]' => 'Jane Doe',
        'contact[email]' => 'test@test',
        'contact[subject]' => 'Test',
        'contact[message]' => 'Hello World!',
    ];
    public const FORM_DATA_TOO_SHORT = [
        'contact[name]' => 'a',
        'contact[email]' => 'mail@example.com',
        'contact[subject]' => 'b',
        'contact[message]' => 'c',
    ];
}
