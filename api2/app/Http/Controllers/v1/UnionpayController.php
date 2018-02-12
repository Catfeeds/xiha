<?php

/**
 * @author gaodacheng
 */

namespace App\Http\Controllers\v1;

use Exception;
use InvalidArgumentException;
use Log;
use App\Http\Controllers\Controller;
use Omnipay\Omnipay;

class UnionpayController extends Controller
{
    /**
     * $var Gateway.
     */
    protected $gateway;

    protected $options;

    public function __construct()
    {
        $this->setUp();
    }

    /**
     * 配置银联支付参数.
     */
    public function setUp()
    {
        $this->gateway = Omnipay::create('UnionPay_Express');
        $this->gateway->setMerId('802310053110697');
        $this->gateway->setCertPath(app()->ROOT_PATH.'api/v2/pay/apppay/unipay/certs/acp_xiha_sign.pfx');
        $this->gateway->setCertDir(app()->ROOT_PATH.'api/v2/pay/apppay/unipay/certs/');
        $this->gateway->setCertPassword('cf7652115');
        $this->gateway->setReturnUrl(env('API_PATH').'api2/public/v1/order/return/unionpay');
        $this->gateway->setNotifyUrl(env('API_PATH').'api2/public/v1/order/notify/unionpay');
        $this->gateway->setEnvironment('production');
    }

    /**
     * @统一下单
     */
    public function purchase(array $pay_info)
    {
        $order = [
            'orderId' => $pay_info['order_no'],
            'txnTime' => date('YmdHis'),
            'orderDesc' => $pay_info['title'],
            'txnAmt' => $pay_info['money'] * 100,
            'reqReserved' => json_encode($pay_info['order_package'], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
        ];

        $request = $this->gateway->purchase($order);
        $response = $request->send();
        if (!$response->isSuccessful()) {
            throw new InvalidArgumentException('银联支付网关下单返回出错', 400);
        }

        return ['tn' => $response->getTradeNo()];
    }

    /**
     * 异步回调.
     */
    public function notify()
    {
        $notify_info = $_REQUEST;
        try {
            $response = $this->gateway->completePurchase(['request_params' => $notify_info])->send();
            if ($response->isSuccessful()) {
                if (isset($notify_info['txnType'])) {
                    switch ($notify_info['txnType']) {
                        case '01': // 消费类交易
                            if (isset($notify_info['reqReserved'])) { // 支付回调有reqReserved字段
                                $order_package = json_decode($notify_info['reqReserved'], true);
                                $pay_info = [
                                    'order_type' => $order_package['order_type'],
                                    'order_id' => $order_package['order_id'],
                                    'order_no' => $notify_info['orderId'],
                                    'transaction_no' => $notify_info['queryId'],
                                    'trade_status' => 1, // 支付完成状态
                                    'user_id' => $order_package['user_id'],
                                    'user_name' => $order_package['user_name'],
                                    'user_phone' => $order_package['user_phone'],
                                    'money' => $notify_info['txnAmt'] / 100, // 单位：元
                                    'pay_type' => 4, // 银联类型为4
                                    'pay_time' => date('Y-m-d H:i:s', strtotime($notify_info['txnTime'])), // 原时间格式 20170108044905
                                ];
                                $pay = new PayController(4);
                                Log::info('银联支付消费回调，准备更新订单状态');
                                $pay->complete($pay_info);
                            }
                            break;
                        case '04': // 退货/款交易
                            if (isset($notify_info['reqReserved'])) { // 退款回调有reqReserved字段
                                $order_package = json_decode($notify_info['reqReserved'], true);
                                $pay_info = [
                                    'order_type' => $order_package['order_type'],
                                    'order_id' => $order_package['order_id'],
                                    'trade_status' => 1007, // 退款完成状态
                                    'user_id' => $order_package['user_id'],
                                    'user_name' => $order_package['user_name'],
                                    'user_phone' => $order_package['user_phone'],
                                    'money' => $notify_info['txnAmt'] / 100, // 单位：元
                                    'pay_type' => 4, // 银联类型为4
                                    'pay_time' => date('Y-m-d H:i:s', strtotime($notify_info['txnTime'])), // 原时间格式 20170108044905
                                ];
                                $pay = new PayController(4);
                                Log::info('银联支付退款回调，准备更新订单状态');
                                $pay->complete($pay_info);
                            }
                            break;
                        default:
                            Log::info('银联其他回调');
                            break;
                    }
                } else {
                    Log::info('回调中无txnType字段');
                }
            } else {
                try {
                    Log::info('银联回调失败', ['notify' => $response->getData()]);
                } catch (Exception $e) {
                    Log::error('银联回调失败的日志写入异常');
                }
            }
        } catch (Exception $e) {
            Log::error('银联在线支付，回调出现异常', ['data' => ['code' => $e->getCode(), 'msg' => $e->getMessage(), 'line' => $e->getLine(), 'file' => $e->getFile()]]);
        }
    }

    /**
     * 查询订单.
     */
    public function query($pay_info)
    {
        $order = [
            'orderId' => $pay_info['order_no'],
            'txnTime' => date('YmdHis'),
            'txnAmt' => $pay_info['money'] * 100,
        ];

        $response = $this->gateway->query($order)->send();
        if (!$response->isSuccessful()) {
            Log::Info('银联订单查询异常', ['query' => $response->getData()]);

            return null;
        }
        $query_result = $response->getData();
        $_data['order_no'] = $query_result['orderId']; // 本平台订单号
        $_data['transaction_no'] = $query_result['queryId']; // 三方平台交易号
        $_data['money'] = (int) $query_result['txnAmt'] / 100; // 订单总金额，单位：分
        $_data['trade_status'] = 'TRADE_SUCCESS';
        $_data['query_id'] = $query_result['queryId'];

        return $_data;
    }

    /**
     * 关闭订单.
     */
    public function close($pay_info)
    {
        $order = [
            'orderId' => $pay_info['order_no'],
            'txnTime' => date('YmdHis'),
            'txnAmt' => $pay_info['money'] * 100,
        ];

        $request = $this->gateway->consumeUndo($order);
        $response = $request->send();
        if (!$response->isSuccessful()) {
            Log::Info('银联订单关闭异常', ['close' => $response->getData()]);

            return null;
        }

        return $response->getData();
    }

    /**
     * 退款订单.
     */
    public function refund($order_info)
    {
        $queryId = $this->query($order_info)['query_id'];
        $order = [
            'orderId' => $order_info['order_no'].'1',    // 嘻哈订单号
            'txnTime' => date('YmdHis'),             //Order trade time
            'txnAmt' => $order_info['money'] * 100, // 退款金额，单位：分
            'queryId' => $queryId,
            'reqReserved' => json_encode($order_info['order_package'], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
        ];
        $request = $this->gateway->refund($order);
        $response = $request->send();
        if (!$response->isSuccessful()) {
            $refund_result = $response->getData();
            if (isset($refund_result['respCode']) && '36' === $refund_result['respCode']) {
                Log::info('此订单已被退款');

                return 'refunded';
            }
            Log::Info('银联退款异常', ['refund' => $response->getData()]);

            return null;
        }

        return $response->getData();
    }

    /**
     * 退款查询订单.
     */
    public function queryRefund($pay_info)
    {
        $order = [
            'orderId' => $pay_info['order_no'],
            'txnTime' => date('YmdHis'),
            'txnAmt' => $pay_info['money'] * 100,
        ];

        $response = $this->gateway->query($order)->send();
        if (!$response->isSuccessful()) {
            Log::Info('银联退款订单查询异常', ['refund_query' => $response->getData()]);

            return null;
        }

        return $response->getData();
    }
}
