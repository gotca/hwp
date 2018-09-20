<?php

namespace App\Http\Middleware;

use Closure;

class Grounded
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (
            $request->query('please', false) === false
            && in_array($request->player->name_key, config('grounded'))
        ) {
            return response(view('grounded', ['player' => $request->player]));
        } else {
            return $next($request);
        }
    }
}
