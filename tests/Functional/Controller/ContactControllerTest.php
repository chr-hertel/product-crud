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

    /**
     * @dataProvider provideInvalidFormData
     *
     * @param array<string, string> $formData
     */
    public function testFailureContactScenario(array $formData, int $violationCount): void
    {
        $this->browser->request('GET', ContactPage::URI);

        self::assertResponseIsSuccessful('Contact page renders successful');

        $this->browser->enableProfiler();
        $crawler = $this->browser->submitForm(ContactPage::FORM_SUBMIT, $formData);

        $this->assertMailSent(0);
        $this->assertValidationViolation($violationCount);

        self::assertCount(0, $crawler->filter('.alert-success'), 'Success message not found');
    }

    /**
     * @return array<array{0: array<string, string>, 1: int}>
     */
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

        self::assertResponseIsSuccessful('Contact page renders successful');
        self::assertCount(4, $crawler->filter('form[name='.ContactPage::FORM_PREFIX.'] .form-group'), 'Bootstrap form-group classes are present');
        self::assertCount(1, $crawler->filter('form[name='.ContactPage::FORM_PREFIX.'] .btn'), 'Bootstrap button is present');
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
