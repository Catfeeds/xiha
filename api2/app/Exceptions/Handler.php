<?php

namespace App\Exceptions;

use App\Http\Controllers\v1\PusherController;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use InvalidArgumentException;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;
use Log;
use Omnipay\Common\Exception\InvalidRequestException;
use Omnipay\Common\Exception\InvalidResponseException;
use RuntimeException;
use Symfony\Component\Debug\Exception\FatalThrowableError;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
        // 尝试不记录以下种类异常的日志trace信息
        'InvalidArgumentException',
        'InvalidRequestException',
        'InvalidResponseException',
        'RuntimeException',
        'HttpException',
        'NotFoundHttpException',
        'FatalThrowableError',
        'FatalErrorException',
        'MethodNotAllowedHttpException',
        'Exception',
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param \Exception $e
     */
    public function report(Exception $e)
    {
        parent::report($e);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Exception               $e
     *
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $e)
    {
        // if ($e instanceof \E) {
        //     $response = [
        //         'error_code' => $e->getCode(),
        //         'error_msg' => $e->getMsg(),
        //         'extra' => $e->getExtra()
        //     ];
        // } elseif ($e instanceof \PDOException) {
        //     \DB::rollback();
        //     $response = [
        //         'error_code' => 1002,
        //         'error_msg' => $e
        //     ];
        // } elseif ($e instanceof HttpException) {
        //     $response = [
        //         'error_code' => 1002,
        //         'error_msg' => $e->getStatusCode()
        //     ];
        // } elseif ($e instanceof ModelNotFoundException) {
        //     $response = [
        //         'error_code' => 1002,
        //         'error_msg' => $e->getMessage()
        //     ];
        // } elseif ($e instanceof \Exception) {
        //     $response = [
        //         'error_code' => 1002,
        //         'error_msg' => $e->getMessage()
        //     ];
        // }
        // $response = array_filter($response);
        // return response()->json($response, 200);
        //return response()->json(array('code' => $e->getCode(), 'msg' => $e->getMessage(), 'data' => 'At file '.$e->getFile().' line '.$e->getLine().' goes wrong'));
        //return parent::render($request, $e);
        if ($e instanceof NotFoundHttpException) {
            $data = [
                'code' => 404,
                'msg' => 'Not Found',
                'data' => new \Stdclass(),
            ];
        } elseif ($e instanceof FatalThrowableError) {
            $data = [
                'code' => 503,
                'msg' => '无法提供服务',
                'data' => new \Stdclass(),
            ];
        } elseif ($e instanceof InvalidRequestException) {
            $data = [
                'code' => 400,
                'msg' => '参数错误',
                'data' => new \Stdclass(),
            ];
        } elseif ($e instanceof InvalidResponseException) {
            $data = [
                'code' => 400,
                'msg' => '参数错误',
                'data' => new \Stdclass(),
            ];
        } elseif ($e instanceof MethodNotAllowedHttpException) {
            $data = [
                'code' => 405,
                'msg' => '请求方式错误',
                'data' => new \Stdclass(),
            ];
        } elseif ($e instanceof ErrorException) {
            $data = [
                'code' => 500,
                'msg' => '无法提供服务',
                'data' => new \Stdclass(),
            ];
        } elseif ($e instanceof InvalidArgumentException) {
            $data = [
                'code' => $e->getCode(),
                'msg' => $e->getMessage(),
                'data' => new \Stdclass(),
            ];
        } elseif ($e instanceof RuntimeException) {
            $data = [
                'code' => 400,
                'msg' => '无法提供服务',
                'data' => new \Stdclass(),
            ];
        } elseif ($e instanceof \ErrorException) {
            $data = [
                'code' => 500,
                'msg' => '无法提供服务',
                'data' => new \Stdclass(),
            ];
        } else {
            $data = [
                'code' => 500,
                'msg' => '其它错误',
                'data' => new \Stdclass(),
            ];
        }
        $log = get_class($e).': '.$e->getMessage().' in '.$e->getFile().':'.$e->getLine();

        try {
            // push error to gdc's phone
            $pusher = new PusherController('student');
            $pusher->setTarget(24156)
                ->setContent('【Err Happen】 '.date('Y-m-d H:i:s').' '.$log)
                ->setType(1) // 系统消息
                ->setMemberId(24156)
                ->setMemberType(1) // 学员类型
                ->setBeizhu('Err Happen')
                ->setFrom('嘻哈学车')
                ->send();
            // push error to gdc's phone
        } catch (Exception $e) {
        }

        // debug
        // 开发环境，直接返回具体的异常及错误信息，方便调试，
        // 生产环境，不会有此提示，要在.env配置文件中指定环境为production
        if (app()->environment('local')) {
            $data['msg'] = $log;
        }
        // debug

        Log::Error($log);

        // 异常发生时的请求记录，以便追踪排查
        $info = [
            'client' => $request->ip(),
            'method' => $request->method(),
            'url' => $request->url(),
            'args' => $request->all(),
            'user-agent' => $request->headers->get('User-Agent', ''),
        ];
        Log::info('[request begin] '.json_encode($info, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));

        return response()->json($data);
    }
}
