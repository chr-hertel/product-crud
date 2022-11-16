<?php

declare(strict_types=1);

namespace App\Contact;

use Symfony\Component\Validator\Constraints as Assert;

final class Dto
{
    #[Assert\NotBlank]
    #[Assert\Length(min: 2, max: 30)]
    public string $name;

    #[Assert\NotBlank]
    #[Assert\Email]
    public string $email;

    #[Assert\NotBlank]
    #[Assert\Length(min: 3, max: 50)]
    public string $subject;

    #[Assert\NotBlank]
    #[Assert\Length(min: 10, max: 300)]
    public string $message;
}
