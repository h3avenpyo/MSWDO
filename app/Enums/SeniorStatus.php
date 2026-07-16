<?php

namespace App\Enums;

enum SeniorStatus: string
{
    case Active = 'active';
    case Pending = 'pending';
    case Archived = 'archived';

    public function label(): string
    {
        return ucfirst($this->value);
    }
}
