<?php

namespace App\Controller\Api;

use App\DTO\Product\ProductFilter;
use App\Service\Product\ProductService;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/api/products')]
#[AsController]
class ProductController extends AbstractController
{
    private Serializer $serializer;

    public function __construct(
        private readonly ProductService $productService,
    ) {
        $this->serializer = (new SerializerBuilder())->build();
    }

    #[Route(path: '', name: 'api_product_list', methods: ['GET'])]
    public function listAction(Request $request): Response
    {
        $filter = new ProductFilter(
            (int)$request->query->get('page', 1),
            (int)$request->query->get('limit', 10),
            $request->query->get('name'),
            $request->query->get('minCost') ? (int)$request->query->get('minCost') : null,
            $request->query->get('maxCost') ? (int)$request->query->get('maxCost') : null,
        );

        $products = $this->productService->getList($filter);
        return new JsonResponse($this->serializer->serialize($products, 'json'), Response::HTTP_OK, [], true);
    }
}
