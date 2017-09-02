<?php

namespace App\Exceptions;

use Exception;

class ShareableHandler extends Handler
{

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        // dd($request);
        // dd($exception->getMessage());
        // return view('shareables.error', []);
        if (env('APP_DEBUG') != true) {
            return response()->view('shareables.error', [], 500);
        }

        return parent::render($request, $exception);
    }
}