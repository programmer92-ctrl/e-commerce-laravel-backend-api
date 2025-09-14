<?php

namespace App\Enums;

enum ShippingMethod: string
{
    //
    case Ground = 'Ground';
    case Standard = 'Standard';
    case Express = 'Express';
}
