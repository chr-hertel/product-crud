<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller;

use App\Tests\Functional\Page\CategoryPage;
use App\Tests\Functional\Page\DashboardPage;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class CategoryControllerTest extends WebTestCase
{
    private static KernelBrowser $browser;
    private static SchemaTool $schemaTool;

    public static function setUpBeforeClass(): void
    {
        self::$browser = self::createClient();
        $container = self::$browser->getContainer();

        /** @var EntityManagerInterface $entityManager */
        $entityManager = $container->get(EntityManagerInterface::class);
        self::$schemaTool = new SchemaTool($entityManager);
        self::$schemaTool->dropDatabase();
        self::$schemaTool->createSchema($entityManager->getMetadataFactory()->getAllMetadata());
    }

    public static function tearDownAfterClass(): void
    {
        self::$schemaTool->dropDatabase();
    }

    public function testCreateCategoryWithoutParentScenario(): void
    {
        self::$browser->request('GET', CategoryPage::URI);
        self::assertResponseIsSuccessful('Category page renders successful');

        self::$browser->submitForm(CategoryPage::FORM_SUBMIT, CategoryPage::FORM_DATA_VALID_WITHOUT_PARENT);
        self::assertResponseRedirects(DashboardPage::URI, null, 'Response redirects to dashboard page');

        $crawler = self::$browser->followRedirect();

        self::assertCount(1, $crawler->filter('.alert-success'), 'Success message found');
        self::assertStringContainsString('Vegetable', $crawler->filter('#categories li')->last()->text());
    }

    public function testCreateCategoryWithoutParentAndTooShortNameScenario(): void
    {
        self::$browser->request('GET', CategoryPage::URI);
        $crawler = self::$browser->submitForm(CategoryPage::FORM_SUBMIT, CategoryPage::FORM_DATA_VALID_WITHOUT_PARENT_TOO_SHORT_NAME);

        $formError = $crawler->filter('form:contains("Name "test" is not valid for a category")');
        self::assertCount(1, $formError, 'Error message found');
    }

    /**
     * @depends testCreateCategoryWithoutParentScenario
     */
    public function testEditCategoryWithoutParentScenario(): void
    {
        self::$browser->request('GET', DashboardPage::URI);
        self::$browser->clickLink('Vegetable');

        self::$browser->submitForm(CategoryPage::FORM_SUBMIT, ['category[name]' => 'Food']);

        $crawler = self::$browser->followRedirect();

        self::assertCount(1, $crawler->filter('.alert-success'), 'Success message found');
        self::assertStringContainsString('Food', $crawler->filter('#categories li')->last()->text());
    }
}
