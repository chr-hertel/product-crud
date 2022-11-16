<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class SearchController extends AbstractController
{
    #[Route('/search', name: 'search', methods: ['GET'])]
    public function search(ProductRepository $repository, Request $request): Response
    {
        $query = (string)$request->query->get('query', '');

        return $this->render('search.html.twig', [
            'paginator' => $repository->search($query),
            'query' => $query,
        ]);
    }
}
