<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller;

use App\Tests\Functional\Page\ContactPage;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\SwiftmailerBundle\DataCollector\MessageDataCollector;
use Symfony\Component\Validator\DataCollector\ValidatorDataCollector;

class ContactControllerTest extends WebTestCase
{
    /** @var KernelBrowser */
    private $browser;

    protected function setUp(): void
    {
        $this->browser = static::createClient();
    }

    public function testSuccessfulContactScenario(): void
    {
        $this->browser->request('GET', ContactPage::URI);

        static::assertResponseIsSuccessful('Contact page renders successful');

        $this->browser->enableProfiler();
        $this->browser->submitForm(ContactPage::FORM_SUBMIT, ContactPage::FORM_DATA_VALID);

        static::assertResponseRedirects(ContactPage::URI, null, 'Response redirects to contact page');
        $this->assertMailSent(1);
        $this->assertValidationViolation(0);

        $crawler = $this->browser->followRedirect();

        static::assertCount(1, $crawler->filter('.alert-success'), 'Success message found');
    }

    private function assertMailSent(int $count): void
    {
        $profile = $this->browser->getProfile();

        if (false === $profile) {
            return;
        }

        /** @var MessageDataCollector $mailerCollector */
        $mailerCollector = $profile->getCollector('swiftmailer');
        static::assertSame($count, $mailerCollector->getMessageCount(), sprintf('Exactly %d mails sent', $count));
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
        static::assertSame($count, $validatorCollector->getViolationsCount(), $message);
    }
}
