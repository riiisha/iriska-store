<?php

namespace App\Enum;

enum DeliveryMethod: string
{
    case PICKUP = 'pickup';
    case COURIER = 'courier';
}
