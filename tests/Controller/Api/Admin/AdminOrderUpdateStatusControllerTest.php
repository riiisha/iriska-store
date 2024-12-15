<?php

namespace App\Tests\Controller\Api\Admin;

use App\DTO\Order\OrderUpdateStatusDTO;
use App\Entity\User;
use App\Enum\DeliveryMethod;
use App\Enum\OrderStatus;
use App\Tests\Controller\Api\BaseWebTestCase;
use Symfony\Component\HttpFoundation\Response;

class AdminOrderUpdateStatusControllerTest extends BaseWebTestCase
{
    protected function getUrl(): string
    {
        return $this->generateUrl('api_admin_order_update_status');
    }

    protected function getOrderUpdateStatusDTO(): OrderUpdateStatusDTO
    {
        return new OrderUpdateStatusDTO(1, OrderStatus::READY_TO_PICKUP->value);
    }

    public function testUpdateStatusActionSuccess(): void
    {
        $this->createOrder();
        $this->loginAdmin();
        $data = $this->getOrderUpdateStatusDTO();

        $this->patchRequest($this->getUrl(), $data);

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $userRepository = $this->em->getRepository(User::class);
        $user = $userRepository->getById(2);
        $order = $user->getOrders()[0];
        $this->assertEquals(OrderStatus::READY_TO_PICKUP, $order->getStatus());
    }

    public function testUpdateStatusActionNotFound(): void
    {
        $this->loginAdmin();

        $data = $this->getOrderUpdateStatusDTO();
        $data->orderId = 9999;

        $this->patchRequest($this->getUrl(), $data);
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function testEditActionUnauthorized(): void
    {
        $data = $this->getOrderUpdateStatusDTO();
        $this->patchRequest($this->getUrl(), $data);
        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
    }

    public function testEditActionForbidden(): void
    {
        $this->loginUser();
        $data = $this->getOrderUpdateStatusDTO();
        $this->patchRequest($this->getUrl(), $data);
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }


    private function createOrder(): void
    {
        $this->loginUser();
        $order = [
            'phone' => 'phone',
            'deliveryMethod' => DeliveryMethod::SELF_DELIVERY->value,
            'products' => [[
                'id' => 1,
                'quantity' => 1
            ]]
        ];

        $this->postRequest('api/order', $order);
        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);

    }
}
