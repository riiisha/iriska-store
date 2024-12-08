<?php

namespace App\Controller\Api;

use App\DTO\Cart\Request\AddToCartDTO;
use App\DTO\Cart\Request\UpdateCartDTO;
use App\Entity\User;
use App\Service\Cart\CartService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

#[Route(path: '/api/cart')]
#[AsController]
class CartController extends AbstractController
{
    public function __construct(
        private readonly CartService $cartService,
    ) {
    }

    #[Route(path: '', name: 'api_cart_show', methods: ['GET'])]
    public function showAction(): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $cart = $this->cartService->show($user);
        return new JsonResponse($cart, Response::HTTP_OK);
    }

    #[Route(path: '', name: 'api_cart_add_item', methods: ['POST'])]
    public function addItemAction(#[MapRequestPayload] AddToCartDTO $addToCartDTO): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $this->cartService->add($addToCartDTO, $user);
        return new JsonResponse([], Response::HTTP_CREATED);
    }

    #[Route(path: '', name: 'api_cart_remove_item', methods: ['DELETE'])]
    public function removeItemAction(#[MapRequestPayload] UpdateCartDTO $updateCartDTO): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $this->cartService->remove($updateCartDTO, $user);
        return new JsonResponse([], Response::HTTP_OK);
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
