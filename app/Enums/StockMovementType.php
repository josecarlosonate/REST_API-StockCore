<?php

namespace App\Enums;

enum StockMovementType: string
{
    case ENTRY = 'entry';
    case EXIT = 'exit';
    case ADJUSTMENT = 'adjustment';
}
