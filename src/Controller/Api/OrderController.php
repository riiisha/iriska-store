<?php

namespace App\Controller\Api;

use App\DTO\Cart\Request\AddToCartDTO;
use App\DTO\Cart\Request\UpdateCartDTO;
use App\DTO\Order\OrderDTO;
use App\Entity\User;
use App\Manager\CartManager;
use App\Manager\OrderManager;
use JMS\Serializer\SerializerBuilder;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

#[Route(path: '/api/order')]
#[AsController]
class OrderController extends AbstractController
{
    public function __construct(
        private readonly OrderManager $manager,
    )
    {
        $this->serializer = (new SerializerBuilder())->build();
    }

    #[Route(path: '', name: 'api_order_create', methods: ['POST'])]
    public function createAction(#[MapRequestPayload] OrderDTO $orderDTO): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $this->manager->create($orderDTO, $user);
        return new JsonResponse([], Response::HTTP_CREATED);
    }

    #[Route(path: '', name: 'api_order_show', methods: ['GET'])]
    public function showAction(): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $orders = $user->getOrders();
        return new JsonResponse($this->serializer->serialize($orders, 'json'), Response::HTTP_OK, [], true);

//        return new JsonResponse($orders->toArray(), Response::HTTP_OK);
    }

    protected function getUser(): ?UserInterface
    {
        $user = parent::getUser();
        if (!$user) {
            throw new NotFoundHttpException("Пользователь не найден");
        }
        return $user;
    }
}
