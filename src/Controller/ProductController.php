<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/product/", name="product_")
 */
class ProductController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("{id<\d+>?}", name="form", methods={"POST", "GET"})
     */
    public function form(Request $request, Product $product = null): Response
    {
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($form->getData());
            $this->entityManager->flush();

            $this->addFlash('success', 'Successfully saved');

            return $this->redirectToRoute('dashboard');
        }

        return $this->render('form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("remove", name="remove", methods={"POST"})
     */
    public function remove(ProductRepository $repository, Request $request): RedirectResponse
    {
        $id = $request->request->getInt('id');
        $product = $repository->find($id);

        if (null === $product) {
            throw $this->createNotFoundException(sprintf('Product with id %d not found', $id));
        }

        $this->entityManager->remove($product);
        $this->entityManager->flush();

        $this->addFlash('success', 'Successfully removed');

        return $this->redirectToRoute('dashboard');
    }
}
