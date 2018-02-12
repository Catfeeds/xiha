<?php

namespace App\Http\Middleware;

use Closure;
use Log;
use App\Http\Controllers\v1\AuthController;

class AuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $expire_check = 'yes')
    {
        // token没有传值
        if (! $request->has('token')) {
            $data = [
                'code' => 401,
                'msg'  => '请登陆',
                'data' => new \stdClass,
            ];
            Log::error('没有字段token');
            return response()->json($data);
        }

        // token存在
        $token = $request->input('token');
        $auth = new AuthController();

        // token来源及客户端是否正确
        if (! $auth->isValidated($token)) {
            $data = [
                'code' => 401,
                'msg'  => '登陆失效，请重新登陆',
                'data' => new \stdClass,
            ];
            Log::error('token验证失败，可能不同源');
            return response()->json($data);
        }

        // token过期
        if ($expire_check == 'yes') {
            if ( $auth->isExpired($token) ) {
                $data = [
                    'code' => 401,
                    'msg'  => '登陆过期，请重新登陆',
                    'data' => new \stdClass,
                ];
                Log::error('token已过期');
                return response()->json($data);
            }
        }

        // 用户真实性检查
        if ( ! $auth->isUserReal($token) ) {
            $data = [
                'code' => 401,
                'msg'  => '登陆过期，请重新登陆',
                'data' => new \stdClass,
            ];
            Log::error('token中的用户信息有误');
            return response()->json($data);
        }

        return $next($request);
    }
}
