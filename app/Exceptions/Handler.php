<?php
namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->renderable(function (ProductOutOfStockException $e, $request) {
            //return response()->view('errors.404', ['message' => $e->getMessage()], 404);
            return response()->json([
                'message' => $e->getMessage(),
            ]);
        });

        $this->renderable(function (ProductIsNotActiveException $e, $request) {
            return response()->json([
                'message' => $e->getMessage(),
            ]);
        });
        
    }
}
