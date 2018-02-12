<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\v1\SmsController as Sms;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Exception;
use InvalidArgumentException;
use Log;
use Xiha\Qrcode\DataBuilder;

class DemoController extends Controller {

    /**
     * 验证接口安全的demo
     */
    public function apiSafety(Request $request) {
        return response()->json([
            'code' => 200,
            'msg' => 'ok',
            'data' => [
                'params' => $request->input(),
                'sign' => self::sign($request->input(), env('XIHA_API_KEY')),
            ],
        ]);
    }

    public static function sign($data, $key)
    {
        unset($data['sign']);

        ksort($data);

        $query = urldecode(http_build_query($data));
        $query .= "&key={$key}";

        return strtoupper(md5($query));
    }

    /**
     * 短信demo
     *
     * 发送变量模版短信
     *
     * @param string $template_type 模版类型
     * @param string $phone 手机号
     * @param string $content JSON字符串变量或全文内容
     */
    public function sms()
    {
        $_data = [];
        try
        {
            $sms = new Sms();
            $sms->sms()
                ->setTemplate('student_code')
                ->send('17355100855', ['code' => '631511', 'shift_name' => '嘻哈超级大成VIP班', 'order_no' => '201704018888']);
            print_r($sms);
            exit();
            $_data = [
                'code' => 200,
                'msg'  => 'debug',
                'data' => [
                    'debug' => env('LINUX'),
                    'sms' => [
                        'var' => get_class_vars(get_class($sms)),
                        'methods' => get_class_methods($sms),
                    ]
                ]
            ];
            return response()->json($_data);
        }
        catch (Exception $e)
        {
            $_data = [
                'code' => 500,
                'msg'  => 'err',
                'data' => [
                    'err' => [
                        'file' => $e->getFile(),
                        'line' => $e->getLine(),
                        'msg'  => $e->getMessage(),
                    ]
                ]
            ];
            return response()->json($_data);
        }
    }

    public function qrcode(Request $request)
    {
        if (! $request->has('s')) {
            $qrcode_contents = "http://www.gaodacheng.com/";
        } else {
            $qrcode_contents = $request->input('s');
        }
        $qrcode = QrCode::format('png')
            ->errorCorrection('H')
            ->size(400)
            ->encoding('utf-8')
            ->margin(1)
            ->generate($qrcode_contents);
        // $qrcode = QrCode::format('svg')->size(600)->generate($qrcode_contents);
        return response($qrcode, 200)->header('Content-Type', 'image/png');
    }
} /* DemoController */

?>
