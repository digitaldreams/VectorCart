<?php
// src/Controller/ProductController.php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Symfony\AI\Store\Document\VectorizerInterface;
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
    public function search(Request $request, VectorizerInterface $ollama, ProductRepository $productRepository): Response
    {
        $query = $request->query->get('q', '');
        $category = $request->query->get('category', '');
        $minScore = $request->query->get('score', 0.75);

        $products = [];
        $searchTime = 0;

        if ($query) {
            $start = microtime(true);

            // Get query embedding
            $queryEmbedding = $ollama->vectorize($query,['dimensions' => 1536 ]);

            // Search by vector
            $products = $productRepository->searchByDql(
                $queryEmbedding->getData(),
                limit: 20,
                category: $category ?: null,
                minScore: $minScore
            );

            $searchTime = round((microtime(true) - $start) * 1000, 2);
        }

        return $this->render('product/search.html.twig', [
            'query' => $query,
            'category' => $category,
            'products' => $products,
            'searchTime' => $searchTime,
            'categories' => array_column($productRepository->getCategories(),'category'),
        ]);
    }
}
