<?php

declare(strict_types=1);

namespace App\Contact;

class Mailer
{
    private $recipient;
    private $mailer;

    public function __construct(string $recipient, \Swift_Mailer $mailer)
    {
        $this->recipient = $recipient;
        $this->mailer = $mailer;
    }

    public function sendMail(Dto $contact): void
    {
        /** @var \Swift_Message $message */
        $message = $this->mailer->createMessage();
        $message
            ->setTo($this->recipient)
            ->setReplyTo($contact->email, $contact->name)
            ->setSubject($contact->subject)
            ->setBody($contact->message);

        $this->mailer->send($message);
    }
}
