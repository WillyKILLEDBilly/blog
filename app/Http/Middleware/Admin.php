<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Closure;
use JWTAuth;

class Admin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
    	if ($token = $request->bearerToken())
    	{
			$jwtToken = JWTAuth::setToken($token)
                        ->getPayload();
			if ($jwtToken->get('sub') == 'ADMIN') 
                return $next($request);;		
    	}
    	   return response('[no access]', 404);
    }
}