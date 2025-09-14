<?php
namespace App\Exceptions;

use Exception;

class ProductOutOfStockException extends Exception
{
    public function __construct($message = 'Product out of Stock!')
    {
        parent::__construct($message);
    }
}
