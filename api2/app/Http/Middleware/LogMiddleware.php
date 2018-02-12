<?php

namespace App\Http\Middleware;

use Closure;
use Log;

class LogMiddleware
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
        $info = [
            'client' => $request->ip(),
            'method' => $request->method(),
            'url' => $request->url(),
            'args' => $request->all(),
            'user-agent' => $request->headers->get('User-Agent', ''),
        ];
        Log::info('[request begin] '.json_encode($info, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        return $next($request);
    }
}
