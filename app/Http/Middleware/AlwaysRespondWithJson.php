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
            return response()->json([
                "status" => 'failed',
                "status_code" => 400,
                "message" => 'Header must Accept:application/json',
                "redirect" =>'login',
            ], 400);
        }
        return $next($request);
    }
}
