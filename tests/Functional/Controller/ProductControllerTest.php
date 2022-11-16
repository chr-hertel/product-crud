<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller;

use App\Entity\Category;
use App\Tests\Functional\Page\DashboardPage;
use App\Tests\Functional\Page\ProductPage;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class ProductControllerTest extends WebTestCase
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

        $entityManager->persist(new Category('Food'));
        $entityManager->flush();
    }

    public static function tearDownAfterClass(): void
    {
        self::$schemaTool->dropDatabase();
    }

    public function testCreateProductScenario(): void
    {
        self::$browser->request('GET', ProductPage::URI);
        self::assertResponseIsSuccessful('Product page renders successful');

        self::$browser->submitForm(ProductPage::FORM_SUBMIT, ProductPage::FORM_DATA_VALID);
        self::assertResponseRedirects(DashboardPage::URI, null, 'Response redirects to dashboard page');

        $crawler = self::$browser->followRedirect();

        self::assertCount(1, $crawler->filter('.alert-success'), 'Success message found');
        self::assertStringContainsString('Rocket', $crawler->filter('#products li')->last()->text());
    }

    /**
     * @depends testCreateProductScenario
     */
    public function testEditExistingProductWithFrozenSku(): void
    {
        self::$browser->request('GET', DashboardPage::URI);
        $crawler = self::$browser->clickLink('Rocket');

        self::assertCount(0, $crawler->filter('input[name="product[sku]"]'), 'SKU form field not found');

        self::$browser->submitForm(ProductPage::FORM_SUBMIT, ['product[name]' => 'Salad']);

        $crawler = self::$browser->followRedirect();

        self::assertCount(1, $crawler->filter('.alert-success'), 'Success message found');
        self::assertStringContainsString('Salad', $crawler->filter('#products li')->last()->text());
    }
}
