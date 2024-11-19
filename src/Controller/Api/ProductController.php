<?php

namespace App\Controller\Api;

use App\Repository\ProductRepository;
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
        private readonly ProductRepository $productRepository,
    )
    {
        $this->serializer = (new SerializerBuilder())->build();
    }

    #[Route(path: '', name: 'api_product_list', methods: ['GET'])]
    public function listAction(Request $request): Response
    {
        /* TODO - добавить пагинацию */
        $products = $this->productRepository->findProductsWithLatestVersion();
        return new JsonResponse($this->serializer->serialize($products, 'json'), Response::HTTP_OK, [], true);
    }
}
