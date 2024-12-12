<?php

namespace App\Event\Order;

use App\Entity\Order\Order;
use App\Entity\User;
use Symfony\Contracts\EventDispatcher\Event;

class OrderCreateEvent extends Event
{
    public const NAME = 'order_create';

    private User $user;
    private Order $order;

    public function __construct(Order $order, User $user)
    {
        $this->order = $order;
        $this->user = $user;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getOrder(): Order
    {
        return $this->order;
    }
}
