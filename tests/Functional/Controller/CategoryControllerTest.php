<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller;

use App\Tests\Functional\Page\CategoryPage;
use App\Tests\Functional\Page\DashboardPage;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CategoryControllerTest extends WebTestCase
{
    /** @var KernelBrowser */
    private static $browser;
    /** @var SchemaTool */
    private static $schemaTool;

    public static function setUpBeforeClass(): void
    {
        static::$browser = static::createClient();
        $container = static::$browser->getContainer();

        if (null === $container) {
            return;
        }

        /** @var EntityManagerInterface $entityManager */
        $entityManager = $container->get('test.'.EntityManagerInterface::class);
        static::$schemaTool = new SchemaTool($entityManager);
        static::$schemaTool->dropDatabase();
        static::$schemaTool->createSchema($entityManager->getMetadataFactory()->getAllMetadata());
    }
    public static function tearDownAfterClass(): void
    {
        static::$schemaTool->dropDatabase();
    }

    public function testCreateCategoryWithoutParentScenario(): void
    {
        static::$browser->request('GET', CategoryPage::URI);
        static::assertResponseIsSuccessful('Category page renders successful');

        static::$browser->submitForm(CategoryPage::FORM_SUBMIT, CategoryPage::FORM_DATA_VALID_WITHOUT_PARENT);
        static::assertResponseRedirects(DashboardPage::URI, null, 'Response redirects to dashboard page');

        $crawler = static::$browser->followRedirect();

        static::assertCount(1, $crawler->filter('.alert-success'), 'Success message found');
        static::assertContains('Vegetable', $crawler->filter('#categories li')->last()->text());
    }

    public function testCreateCategoryWithoutParentAndTooShortNameScenario(): void
    {
        static::$browser->request('GET', CategoryPage::URI);
        $crawler = static::$browser->submitForm(CategoryPage::FORM_SUBMIT, CategoryPage::FORM_DATA_VALID_WITHOUT_PARENT_TOO_SHORT_NAME);

        $formError = $crawler->filter('form:contains("Name "test" is not valid for a category")');
        static::assertCount(1, $formError, 'Error message found');
    }

    /**
     * @depends testCreateCategoryWithoutParentScenario
     */
    public function testEditCategoryWithoutParentScenario(): void
    {
        static::$browser->request('GET', DashboardPage::URI);
        static::$browser->clickLink('Vegetable');

        static::$browser->submitForm(CategoryPage::FORM_SUBMIT, ['category[name]' => 'Food']);

        $crawler = static::$browser->followRedirect();

        static::assertCount(1, $crawler->filter('.alert-success'), 'Success message found');
        static::assertContains('Food', $crawler->filter('#categories li')->last()->text());
    }
}
