<?php
// src/Controller/ProductController.php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use App\Service\QueryVectorCache;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/products')]
class ProductController extends AbstractController
{

    #[Route('/', name: 'product_index', methods: ['GET'])]
    public function index(ProductRepository $productRepository): Response
    {

        return $this->render('product/index.html.twig', [
            'categories' => array_column($productRepository->getCategories(),'category'),
        ]);
    }

    #[Route('/search', name: 'product_search', methods: ['GET'])]
    public function search(Request $request, QueryVectorCache $vectorCache, ProductRepository $productRepository): Response
    {
        $query = $request->query->get('q', '');
        $category = $request->query->get('category', '');
        $minScore = (float) $request->query->get('score', 0.75);
        $page = (int) $request->query->get('page', 1);
        $limit = 20;

        $products = [];
        $searchTime = 0;
        $totalResults = 0;

        if ($query) {
            $start = microtime(true);

            // Get cached query embedding
            $queryVector = $vectorCache->getVector($query, 1536);

            // Search by vector
            $products = $productRepository->searchByDql(
                $queryVector->toArray(),
                limit: $limit,
                category: $category ?: null,
                minScore: $minScore,
                offset: ($page - 1) * $limit
            );

            // Get total count for pagination
            $totalResults = $productRepository->countSearchResults(
                $queryVector->toArray(),
                category: $category ?: null,
                minScore: $minScore
            );

            $searchTime = round((microtime(true) - $start) * 1000, 2);
        }

        return $this->render('product/search.html.twig', [
            'query' => $query,
            'category' => $category,
            'score' => $minScore,
            'products' => $products,
            'searchTime' => $searchTime,
            'categories' => array_column($productRepository->getCategories(), 'category'),
            'page' => $page,
            'limit' => $limit,
            'totalResults' => $totalResults,
            'totalPages' => (int) ceil($totalResults / $limit),
        ]);
    }
}
