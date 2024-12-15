<?php

namespace App\Tests\Controller\Api\Order;

use App\DTO\Address\AddressDTO;
use App\DTO\Order\OrderDTO;
use App\DTO\Order\ProductDTO;
use App\Entity\Order\Order;
use App\Enum\DeliveryMethod;
use App\Tests\Controller\Api\BaseWebTestCase;
use Symfony\Component\HttpFoundation\Response;

class OrderCreateControllerTest extends BaseWebTestCase
{
    protected function getUrl(): string
    {
        return $this->generateUrl('api_order_create');
    }

    protected function getOrderDTO(): OrderDTO
    {
        $productDTO = new ProductDTO(1, 1);
        $addressDTO = new AddressDTO('city', 'street', 'house');
        return new OrderDTO(
            'phone',
            DeliveryMethod::COURIER->value,
            [$productDTO],
            $addressDTO
        );
    }

    public function testCreateActionSuccessCourier(): void
    {
        $this->loginUser();

        $this->postRequest($this->getUrl(), $this->getOrderDTO());

        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
    }


    public function testCreateActionSuccessPickup(): void
    {
        $this->loginUser();

        $data = $this->getOrderDTO();

        $data->deliveryMethod = DeliveryMethod::SELF_DELIVERY->value;

        $this->postRequest($this->getUrl(), $data);

        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
    }

    public function testCreateActionFailure(): void
    {
        $this->loginUser();

        $data = $this->getOrderDTO();
        $data->phone = '79111111111111111';

        $this->postRequest($this->getUrl(), $data);

        $this->assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testCreateActionFailureExceedsMaxQuantity(): void
    {
        $this->loginUser();

        $data = $this->getOrderDTO();
        $productDTO = new ProductDTO(1, Order::MAX_QUANTITY_ORDER_ITEMS + 1);
        $data->products = [$productDTO];

        $this->postRequest($this->getUrl(), $data);

        $this->assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testCreateActionFailureEmptyAddressForCourier(): void
    {
        $this->loginUser();

        $data = $this->getOrderDTO();

        $data->address = null; // Пустой адрес для курьерской доставки
        $data->deliveryMethod = DeliveryMethod::COURIER->value;

        $this->postRequest($this->getUrl(), $data);

        $this->assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testCreateActionFailureUnauthorized(): void
    {
        $this->postRequest($this->getUrl(), $this->getOrderDTO());

        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
    }
}
