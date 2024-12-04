<?php

namespace App\Enum;

enum NotificationType: string
{
    case REQUIRES_PAYMENT = 'requires_payment';
    case SUCCESS_PAYMENT = 'success_payment';
    case COMPLETED = 'completed';
}
