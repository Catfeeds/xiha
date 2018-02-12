<?php

namespace App\Http\Middleware;

use Log;
use InvalidArgumentException;
use Closure;
use Omnipay\WechatPay\Helper;

class EntranceMiddleware
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

        // Must fields: sign, timestamp
        if (! $request->has('sign')
            || ! $request->has('timestamp')
        ) {
            Log::Error('没有sign和timestamp字段');
            throw new InvalidArgumentException('未通过我们的安全验证', 400);
        }

        // 请求在一定时间过后失效
        if ($request->input('timestamp') < time() - 300) {
            Log::Error('超过5分钟有效期');
            throw new InvalidArgumentException('未通过我们的安全验证', 400);
        }

        // 验证参数签名
        if ($request->input('sign') != Helper::sign($request->input(), env('XIHA_API_KEY'))) {
            Log::Error('接口字段的签名错误');
            throw new InvalidArgumentException('未通过我们的安全验证', 400);
        }

        // go next
        return $next($request);
    }
}
