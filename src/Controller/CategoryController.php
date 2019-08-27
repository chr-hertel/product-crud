<?php

declare(strict_types=1);

namespace App\Controller;

use App\Category\CategoryRemovalException;
use App\Category\Removal;
use App\Entity\Category;
use App\Form\CategoryType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/category/", name="category_")
 */
class CategoryController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("{id<\d+>?}", name="form", methods={"POST", "GET"})
     */
    public function form(Request $request, Category $category = null): Response
    {
        $form = $this->createForm(CategoryType::class, $category);
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
     * @Route("remove", name="remove", methods={"DELETE"})
     */
    public function remove(Request $request, Removal $removal): RedirectResponse
    {
        try {
            $removal->remove($request->request->getInt('id'));
        } catch (CategoryRemovalException $exception) {
            $this->addFlash('danger', $exception->getMessage());
        }

        return $this->redirectToRoute('dashboard');
    }
}
