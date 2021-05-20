<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;

class CheckForAllScopes
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param mixed ...$scopes
     * @return mixed
     * 
     * @throws \Illuminate\Auth\AuthenticationException|\Laravel\Passport\Exceptions\MissingScopeException
     */
    public function handle(Request $request, Closure $next, ...$scopes)
    {
        if(!$request->user() || !$request->user()->token()) {
            throw new AuthenticationException;
        }
        foreach($scopes as $scope) {
            if($request->user()->tokenCan($scope)) {
                return $next($request);
            }
        }
        return response([
            'message' => 'Not Authorized.'
        ], 403);
    }
}
