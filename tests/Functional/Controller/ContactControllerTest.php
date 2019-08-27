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

    /**
     * @dataProvider provideInvalidFormData
     */
    public function testFailureContactScenario(array $formData, int $violationCount): void
    {
        $this->browser->request('GET', ContactPage::URI);

        static::assertResponseIsSuccessful('Contact page renders successful');

        $this->browser->enableProfiler();
        $crawler = $this->browser->submitForm(ContactPage::FORM_SUBMIT, $formData);

        $this->assertMailSent(0);
        $this->assertValidationViolation($violationCount);

        static::assertCount(0, $crawler->filter('.alert-success'), 'Success message not found');
    }

    public function provideInvalidFormData(): array
    {
        return [
            [ContactPage::FORM_DATA_EMPTY, 4],
            [ContactPage::FORM_DATA_MISSING_EMAIL, 1],
            [ContactPage::FORM_DATA_INVALID_EMAIL, 1],
            [ContactPage::FORM_DATA_TOO_SHORT, 3],
        ];
    }

    public function testContactFormRendersWithFormGroups(): void
    {
        $crawler = $this->browser->request('GET', ContactPage::URI);

        static::assertResponseIsSuccessful('Contact page renders successful');
        static::assertCount(4, $crawler->filter('form[name=contact] .form-group'), 'Bootstrap form-group classes are present');
        static::assertCount(1, $crawler->filter('form[name=contact] .btn'), 'Bootstrap buttonis present');
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
