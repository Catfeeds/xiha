<?php  

namespace App\Http\Controllers\v1;

use Exception;
use InvalidArgumentException;
use Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Omnipay\Omnipay;
use Omnipay\Alipay\Requests\LegacyAppPurchaseRequest;
use Omnipay\Alipay\Requests\LegacyCompletePurchaseRequest;
use Omnipay\Alipay\Requests\LegacyCompleteRefundRequest;
use Omnipay\Alipay\Requests\LegacyNotifyRequest;
use Omnipay\Alipay\Requests\LegacyQueryRequest;
use Omnipay\Alipay\Requests\LegacyRefundRequest;
use Omnipay\Alipay\Requests\LegacyExpressPurchaseRequest;
use Omnipay\Alipay\Requests\LegacyVerifyAppPayReturnRequest;
use Omnipay\Alipay\Requests\LegacyVerifyNotifyIdRequest;
use Omnipay\Alipay\Requests\LegacyWapPurchaseRequest;
use Omnipay\Alipay\Responses\LegacyAppPurchaseResponse;
use App\Http\Controllers\v1\PayController;

class AlipayController extends Controller {

    /**
     * @var AopAppGateway $gateway
     */
    protected $gateway;

    protected $options;

    public function __construct() {
        $this->setUp();
    }

    /**
     * 配置支付宝支付参数
     */
    public function setUp() {
        $this->gateway = Omnipay::create('Alipay_LegacyApp');
        $this->gateway->setPartner('2088811269500873');
        $this->gateway->setSellerId('2088811269500873');
        $this->gateway->setPrivateKey(app()->ROOT_PATH.'api2/libs/paykey/alipay/rsa_private_key.pem');
        $this->gateway->setAlipayPublicKey(app()->ROOT_PATH.'api2/libs/paykey/alipay/alipay_public_key.pem');
        /*
        $this->gateway->setPartner('2088021589743498');
        $this->gateway->setSellerId('2088021589743498');
        $this->gateway->setPrivateKey(app()->ROOT_PATH.'api2/libs/paykey/alipay_xh/rsa_private_key.pem');
        $this->gateway->setAlipayPublicKey(app()->ROOT_PATH.'api2/libs/paykey/alipay_xh/alipay_public_key.pem');
        */
        $this->gateway->setNotifyUrl(env('API_PATH').'api2/public/v1/order/notify/alipay');
    }

    /**
     * @统一下单
     *
     */
    public function purchase(Array $pay_info) {
        $order = [
            'subject' => $pay_info['title'],
            'out_trade_no' => $pay_info['order_no'],
            'total_fee' => $pay_info['money'],
            'body' => json_encode($pay_info['order_package'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
        ];
        $response = $this->gateway->purchase($order)->send();

        if (! $response->isSuccessful()) {
            throw new InvalidResponseException("支付宝网关调起失败", 400);
        }

        Log::Info('orderString:'.$response->getOrderString());
        return ['order_string' => $response->getOrderString()];
    }

    /**
     * 异步回调
     *
     */
    public function notify() {
        $notify_info = $_POST;
        $pay = new PayController();
        $request = $this->gateway->completePurchase();
        $request->setParams($notify_info);
        try {
            $response = $request->send();
            if ( $response->isSuccessful() && $response->isPaid() ) {
                $order_package = json_decode($notify_info['body'], true);
                $pay_info = [
                    'order_type'        => $order_package['order_type'],
                    'order_id'          => $order_package['order_id'],
                    'order_no'          => $notify_info['out_trade_no'],
                    'transaction_no'    => $notify_info['trade_no'],
                    'trade_status'      => $notify_info['trade_status'],
                    'user_id'           => $order_package['user_id'],
                    'user_name'         => $order_package['user_name'],
                    'user_phone'        => $order_package['user_phone'],
                    'money'             => $notify_info['total_fee'], // 单位：元
                    'pay_type'          => 1, // 支付宝类型为1
                    'gmt_payment'       => $notify_info['gmt_payment'],
                ];
                $pay->complete($pay_info);
                echo 'success';
            } else {
                echo 'fail';
            }
        } catch (Exception $e) {
            echo 'fail';
        }
    }

    /**
     * 关闭订单
     */
    public function close($order_info) {
        $order = [
            'out_trade_no' => $order_info['order_no'],
        ];
        $response = $this->gateway->close($order)->send();
    }

}

?>
