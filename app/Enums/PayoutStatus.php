<?php

namespace App\Enums;

enum PayoutStatus: string
{
    case Pending = 'pending';
    case Released = 'released';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return ucfirst($this->value);
    }
}
