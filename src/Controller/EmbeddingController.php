<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\AI\Store\Document\VectorizerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

final class EmbeddingController extends AbstractController
{
    #[Route('/embedding', name: 'app_embedding')]
    public function index(ProductRepository $repository, VectorizerInterface $ollama): JsonResponse
    {
        //Symfony\AI\Platform\Vector\Vector
       $result = $ollama->vectorize(values: 'I love my family',options: ['dimensions'=>1536]);
        print_r($result->getData());
        $result = $repository->test();
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/EmbeddingController.php',
        ]);
    }
}
