<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\AI\Platform\Vector\Vector;
use Symfony\AI\Store\Document\Metadata;
use Symfony\AI\Store\Document\TextDocument;
use Symfony\AI\Store\Document\VectorDocument;
use Symfony\AI\Store\Document\VectorizerInterface;
use Symfony\AI\Platform\Vector\VectorInterface;
use Symfony\AI\Store\Query\VectorQuery;
use Symfony\AI\Store\StoreInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Uid\Uuid;

final class EmbeddingController extends AbstractController
{
    #[Route('/embedding', name: 'app_embedding')]
    public function index(ProductRepository $repository, VectorizerInterface $default): JsonResponse
    {
        //Symfony\AI\Platform\Vector\Vector
       $result = $default->vectorize(values: 'I love my family',options: ['dimensions'=>1536]);
        print_r($result->getData());
        $result = $repository->test();
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/EmbeddingController.php',
        ]);
    }
    #[Route('/store/save')]
    public function store(
        StoreInterface $postgresDefault,
        VectorizerInterface $default,
        Request $request
        ) {
        $text = $request->query->get('text',"I am a web developer");

        $vector = $default->vectorize($text, ['dimensions'=> 1536]);

        if($vector instanceof VectorInterface) {
            $document = new VectorDocument(
                id: Uuid::v4(),
                vector: $vector,
                metadata: new Metadata(['text'=>$text,'expert'=>'PHP'])
                );
            $postgresDefault->add($document);
            return new Response('Text successsfully stored in the vector Database');
        }

        return new Response('Something is wrong!');
    }
    #[Route('/store/query')]
    public function query(
         StoreInterface $postgresDefault,
         VectorizerInterface $default,
        Request $request
        ){

        $query = $request->query->get('q');
        $vector = $default->vectorize($query, ['dimensions' => 1536]);

        $results = $postgresDefault->query(new VectorQuery($vector), [
            'limit' => 2,
            'where' => "metadata->>'expert'=:expert",
            'params' => ['expert'=>'PHP']
        ]);
        $data = [];
        foreach($results as $result){
            if($result->getScore()>0){
                $data[$result->getId()] = $result->getScore();
            }
        }

        return new JsonResponse($data);
    }

    public function remove(){

    }
}
