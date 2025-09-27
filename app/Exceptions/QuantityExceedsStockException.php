<?php
namespace App\Exceptions;

use Exception;

class QuantityExceedsStockException extends Exception
{
    public function __construct($message = 'Request quantity exceeds product stock!')
    {
        parent::__construct($message);
    }
}