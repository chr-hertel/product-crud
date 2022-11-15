<?php

declare(strict_types=1);

namespace App\Tests\Functional\Page;

final class ContactPage
{
    public const URI = '/contact';

    public const FORM_SUBMIT = 'Submit';
    public const FORM_PREFIX = 'contact_us';
    public const FORM_DATA_VALID = [
        self::FORM_PREFIX.'[name]' => 'Jane Doe',
        self::FORM_PREFIX.'[email]' => 'jane@example.com',
        self::FORM_PREFIX.'[subject]' => 'Test',
        self::FORM_PREFIX.'[message]' => 'Hello World!',
    ];

    public const FORM_DATA_EMPTY = [];
    public const FORM_DATA_MISSING_EMAIL = [
        self::FORM_PREFIX.'[name]' => 'Jane Doe',
        self::FORM_PREFIX.'[email]' => '',
        self::FORM_PREFIX.'[subject]' => 'Test',
        self::FORM_PREFIX.'[message]' => 'Hello World!',
    ];
    public const FORM_DATA_INVALID_EMAIL = [
        self::FORM_PREFIX.'[name]' => 'Jane Doe',
        self::FORM_PREFIX.'[email]' => 'test@test',
        self::FORM_PREFIX.'[subject]' => 'Test',
        self::FORM_PREFIX.'[message]' => 'Hello World!',
    ];
    public const FORM_DATA_TOO_SHORT = [
        self::FORM_PREFIX.'[name]' => 'a',
        self::FORM_PREFIX.'[email]' => 'mail@example.com',
        self::FORM_PREFIX.'[subject]' => 'b',
        self::FORM_PREFIX.'[message]' => 'c',
    ];
}
