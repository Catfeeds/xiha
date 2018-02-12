<?php

namespace App\Http\Middleware;

use Closure;
use Log;
use App\Http\Controllers\v1\PusherController; // 推送

/**
 * 发送请求给测试 gdc's user_id is 24156, phone is 17355100855
 */

class RequestMiddleware
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
        try {
            $info = [
                'client' => $request->ip(),
                    'method' => $request->method(),
                    'url' => $request->url(),
                    'args' => $request->all(),
                    'user-agent' => $request->headers->get('User-Agent', ''),
                ];
            $request_str = json_encode($info, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
            $pusher = new PusherController('student');
            $user_id = 24156; // gdc's user_id is 24156, phone is 17355100855
            $pusher->setTarget($user_id)
                ->setContent('【接口请求】'.$request_str)
                ->setType(1) // 系统消息
                ->setMemberId($user_id)
                ->setMemberType(1)
                ->setBeizhu('接口请求')
                ->setFrom('嘻哈学车')
                ->send();
        } catch ( Exception $e ) {
            //
        }

        return $next($request);
    }
}
