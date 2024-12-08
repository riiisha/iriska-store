<?php

namespace App\Tests\Controller\Api\Order;

use App\Entity\Order\Order;
use App\Enum\DeliveryMethod;
use App\Tests\Controller\Api\BaseWebTestCase;
use Symfony\Component\HttpFoundation\Response;

class OrderCreateControllerTest extends BaseWebTestCase
{
    protected function getUrl(): string
    {
        return '/api/order';
    }

    protected function getData(): array
    {
        return [
            'phone' => 'phone',
            'deliveryMethod' => DeliveryMethod::COURIER->value,
            'products' => [[
                'id' => 1,
                'quantity' => 1
            ]],
            'address' => [
                'city' => 'city',
                'street' => 'street',
                'house' => 'house',
            ],
        ];
    }

    public function testCreateActionSuccessCourier()
    {
        $this->loginUser();

        $this->postRequest($this->getUrl(), $this->getData());

        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
    }


    public function testCreateActionSuccessPickup()
    {
        $this->loginUser();

        $data = $this->getData();
        $data['deliveryMethod'] = DeliveryMethod::SELF_DELIVERY->value;

        $this->postRequest($this->getUrl(), $data);

        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
    }

    public function testCreateActionFailure()
    {
        $this->loginUser();

        $data = $this->getData();
        $data['phone'] = '79111111111111111';

        $this->postRequest($this->getUrl(), $data);

        $this->assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testCreateActionFailureExceedsMaxQuantity()
    {
        $this->loginUser();

        $data = $this->getData();
        $data['products'] = [['id' => 1, 'quantity' => Order::MAX_QUANTITY_ORDER_ITEMS + 1]];

        $this->postRequest($this->getUrl(), $data);

        $this->assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testCreateActionFailureEmptyAddressForCourier()
    {
        $this->loginUser();

        $data = $this->getData();
        $data['address'] = null; // Пустой адрес для курьерской доставки
        $data['deliveryMethod'] = DeliveryMethod::COURIER->value;

        $this->postRequest($this->getUrl(), $data);

        $this->assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testCreateActionFailureUnauthorized()
    {
        $this->postRequest($this->getUrl(), $this->getData());

        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
    }
}
