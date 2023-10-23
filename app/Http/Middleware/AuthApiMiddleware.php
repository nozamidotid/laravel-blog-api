<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AuthApiMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->header("api_key");
        $auntheticate = true;

        if(!$token){
            $auntheticate = false;
        }

        $user = User::query()->where('token', $token)->first();

        If(!$user){
            $auntheticate = false;
        }else{
            Auth::login($user);
        }

        if($auntheticate){
            return $next($request);
        }else{
            return response([
                "errors" => [
                    "message" => [
                        "unauthorized"
                    ]
                ]
                    ], 401);
        }
    }
}
