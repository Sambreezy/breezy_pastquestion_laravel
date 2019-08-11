<?php

namespace App\Http\Middleware;

use Closure;

class VerifyRankToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $rank_token)
    {
        if ($rank_token !== auth()->user()->rank) {
            return response()->json([
                'status' => 'failed',
                'status_code' => 401,
                'message' => 'Please contact management immediately',
                "redirect" => 'home'
            ], 401);
        }
        return $next($request);
    }
}
