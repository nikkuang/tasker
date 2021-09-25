<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class TaskStatus extends Enum
{
    const PENDING     = 'pending';
    const CANCELLED   = 'cancelled';
    const COMPLETED   = 'completed';
}
