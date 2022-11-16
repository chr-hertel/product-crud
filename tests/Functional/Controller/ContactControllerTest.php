<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller;

use App\Tests\Functional\Page\ContactPage;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Mailer\DataCollector\MessageDataCollector;
use Symfony\Component\Validator\DataCollector\ValidatorDataCollector;

final class ContactControllerTest extends WebTestCase
{
    private KernelBrowser $browser;

    protected function setUp(): void
    {
        $this->browser = self::createClient();
    }

    public function testSuccessfulContactScenario(): void
    {
        $this->browser->request('GET', ContactPage::URI);

        self::assertResponseIsSuccessful('Contact page renders successful');

        $this->browser->enableProfiler();
        $this->browser->submitForm(ContactPage::FORM_SUBMIT, ContactPage::FORM_DATA_VALID);

        self::assertResponseRedirects(ContactPage::URI, null, 'Response redirects to contact page');
        $this->assertMailSent(1);
        $this->assertValidationViolation(0);

        $crawler = $this->browser->followRedirect();

        self::assertCount(1, $crawler->filter('.alert-success'), 'Success message found');
    }

    private function assertMailSent(int $count): void
    {
        $profile = $this->browser->getProfile();

        if (false === $profile) {
            return;
        }

        /** @var MessageDataCollector $mailerCollector */
        $mailerCollector = $profile->getCollector('mailer');
        self::assertCount($count, $mailerCollector->getEvents()->getEvents(), sprintf('Exactly %d mails sent', $count));
    }

    private function assertValidationViolation(int $count): void
    {
        $profile = $this->browser->getProfile();

        if (false === $profile) {
            return;
        }

        /** @var ValidatorDataCollector $validatorCollector */
        $validatorCollector = $profile->getCollector('validator');
        $message = sprintf('Exactly %d validation violations', $count);
        self::assertSame($count, $validatorCollector->getViolationsCount(), $message);
    }
}
