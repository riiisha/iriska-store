<?php

namespace App\Enum;

enum DeliveryMethod: string
{
    case SELF_DELIVERY = 'selfdelivery';
    case COURIER = 'courier';
}
