<?php

namespace App\Listeners;

use App\Events\OrderPlaced;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class UpdateInventory
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(OrderPlaced $event): void
    {
        //
        if($event->option === 'decrement'){

            $event->product->decrement('stock_quantity', $event->itemQuantity);
        
        }

        if($event->option === 'increment'){

            $event->product->increment('stock_quantity', $event->itemQuantity);

        }
    }
}
