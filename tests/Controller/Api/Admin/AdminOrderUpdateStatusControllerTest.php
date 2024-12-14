<?php

namespace App\Tests\Controller\Api\Admin;

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

    protected function getData(): array
    {
        return [
            'orderId' => 1,
            'newStatus' => OrderStatus::READY_TO_PICKUP->value,
        ];
    }

    public function testUpdateStatusActionSuccess()
    {
        $this->createOrder();
        $this->loginAdmin();
        $data = $this->getData();

        $this->patchRequest($this->getUrl(), $data);

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $userRepository = $this->em->getRepository(User::class);
        $user = $userRepository->getById(2);
        $order = $user->getOrders()[0];
        $this->assertEquals(OrderStatus::READY_TO_PICKUP, $order->getStatus());
    }

    public function testUpdateStatusActionNotFound()
    {
        $this->loginAdmin();

        $data = $this->getData();
        $data['orderId'] = 9999;

        $this->patchRequest($this->getUrl(), $data);
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function testEditActionUnauthorized()
    {
        $data = $this->getData();
        $this->patchRequest($this->getUrl(), $data);
        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
    }

    public function testEditActionForbidden()
    {
        $this->loginUser();
        $data = $this->getData();
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
