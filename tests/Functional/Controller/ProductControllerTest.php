<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller;

use App\Entity\Category;
use App\Tests\Functional\Page\CategoryPage;
use App\Tests\Functional\Page\DashboardPage;
use App\Tests\Functional\Page\ProductPage;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ProductControllerTest extends WebTestCase
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

        $entityManager->persist(new Category('Food'));
        $entityManager->flush();
    }
    public static function tearDownAfterClass(): void
    {
        static::$schemaTool->dropDatabase();
    }

    public function testCreateProductScenario(): void
    {
        static::$browser->request('GET', ProductPage::URI);
        static::assertResponseIsSuccessful('Product page renders successful');

        static::$browser->submitForm(ProductPage::FORM_SUBMIT, ProductPage::FORM_DATA_VALID);
        static::assertResponseRedirects(DashboardPage::URI, null, 'Response redirects to dashboard page');

        $crawler = static::$browser->followRedirect();

        static::assertCount(1, $crawler->filter('.alert-success'), 'Success message found');
        static::assertContains('Rocket', $crawler->filter('#products li')->last()->text());
    }
}
