<?php

declare(strict_types=1);

namespace App\Contact;

use Symfony\Component\Validator\Constraints as Assert;

class Dto
{
    /**
     * @Assert\NotBlank
     * @Assert\Length(min=2)
     */
    public $name;

    /**
     * @Assert\NotBlank
     * @Assert\Email
     */
    public $email;

    /**
     * @Assert\NotBlank
     * @Assert\Length(min=4, max=30)
     */
    public $subject;

    /**
     * @Assert\NotBlank
     * @Assert\Length(min=10, max=300)
     */
    public $message;
}
