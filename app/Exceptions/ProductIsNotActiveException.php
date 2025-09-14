<?php
namespace App\Exceptions;

use Exception;

class ProductIsNotActiveException extends Exception
{
    public function __construct($message = 'Product is not active!')
    {
        parent::__construct($message);
    }
}
