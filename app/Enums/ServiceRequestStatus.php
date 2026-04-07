<?php

namespace App\Enums;

enum ServiceRequestStatus: string
{
    case PENDING = 'pending';
    case ENGAGED = 'engaged';
    case CANCELLED = 'cancelled';
    case COMPLETED = 'completed';
}