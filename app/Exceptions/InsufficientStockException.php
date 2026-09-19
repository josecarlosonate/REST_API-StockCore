<?php

namespace App\Exceptions;

use Exception;

class InsufficientStockException extends Exception
{
    public function __construct()
    {
        parent::__construct('Stock insuficiente para realizar la salida.');
    }
}
