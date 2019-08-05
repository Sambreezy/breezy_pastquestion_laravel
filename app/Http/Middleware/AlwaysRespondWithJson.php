<?php

namespace App\Http\Middleware;

use Closure;

class AlwaysRespondWithJson
{
    /**
     * Handle an incoming request.
     * Change the Request headers to accept "application/json" first
     * in order to make the wantsJson() function return true
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $request->headers->set('Accept', 'application/json');
        
        $acceptHeader = $request->header('Accept');
        if ($acceptHeader != 'application/json') {
            return response()->json(['error' => 'Header must Accept:application/json'], 400);
        }
        return $next($request);
    }
}
