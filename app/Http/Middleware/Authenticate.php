<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Factory as Auth;

class Authenticate
{
    /**
     * The authentication guard factory instance.
     *
     * @var \Illuminate\Contracts\Auth\Factory
     */
    protected $auth;

    /**
     * Create a new middleware instance.
     *
     * @param  \Illuminate\Contracts\Auth\Factory  $auth
     * @return void
     */
    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {   $request_headers=apache_request_headers();
        foreach ($request_headers as $key => $value) {
            $request->headers->set($key, $value);
        }
        if ($this->isPreflightRequest($request)) {
            return response()->json('option request', 200);
        }
        if (!$request->hasHeader('Authorization')) {
           
            return response()->json('Authorization Header not found', 401);
        }
        if ($request->header('Authorization') == null) {
            return response()->json('No token provided', 401);
        } else{
            $check_bearer = strpos($request->header('Authorization'), 'Bearer ');
            if ($check_bearer !== false) {
                $token = explode(" ", $request->header('Authorization'));
                auth()->setToken($token[1]);
                $userinfo=auth()->user();
                if($userinfo){
                    $request->merge(['user_id' => $userinfo->id]);
                }else {
                    return response()->json('Wrong token provided', 401);
                }
               
                return $next($request);
            } else {
                return response()->json('Wrong token ', 401);
            }
        }
        if ($this->auth->guard($guard)->guest()) {
            return response('Unauthorized.', 401);
        }

        return $next($request);
    }
    protected function isPreflightRequest($request)
    {
        return $request->isMethod('OPTIONS');
    }
}
