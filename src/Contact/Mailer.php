<?php

declare(strict_types=1);

namespace App\Contact;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

class Mailer
{
    public function __construct(
        private readonly string $recipient,
        private readonly MailerInterface $mailer,
    ) {
    }

    public function sendMail(Dto $contact): void
    {
        $message = new Email();
        $message
            ->to($this->recipient)
            ->sender(new Address($contact->email, $contact->name))
            ->subject($contact->subject)
            ->text($contact->message);

        $this->mailer->send($message);
    }
}
