<?php
/**
 * @author gaodacheng
 */

namespace App\Http\Controllers\v1;

use Exception;
use Log;
use App\Http\Controllers\Controller;
use Omnipay\Omnipay;
use Omnipay\WechatPay\Gateway;

class WechatpayController extends Controller
{
    /**
     * $var Gateway.
     */
    protected $gateway;

    protected $options;

    /**
     * @var
     */
    protected $pay_type = 3;

    public function __construct()
    {
        $this->setUp();
    }

    /**
     * 配置微信支付参数.
     */
    public function setUp()
    {
        $this->gateway = Omnipay::create('WechatPay');
        $this->gateway->setAppId('wx267ff7ef00f0c6f8');
        $this->gateway->setMchId('1294996701');
        $this->gateway->setApiKey('XIHAXUECHE2016XIHAXUECHE2016XIHA');
        $this->gateway->setNotifyUrl(env('API_PATH').'api2/public/v1/order/notify/wechatpay');
        $this->gateway->setTradeType('APP');
        $this->gateway->setCertPath(app()->ROOT_PATH.'api/v2/pay/apppay/wxpay/cert/apiclient_cert.pem');
        $this->gateway->setKeyPath(app()->ROOT_PATH.'api/v2/pay/apppay/wxpay/cert/apiclient_key.pem');
    }

    /**
     * 统一下单.
     */
    public function purchase(array $pay_info)
    {
        $order = [
            'body' => $pay_info['title'],
            'out_trade_no' => $pay_info['order_no'],
            'total_fee' => $pay_info['money'] * 100, // 单位：分(CNY)
            'spbill_create_ip' => '',
            'fee_type' => 'CNY',
            'attach' => json_encode($pay_info['order_package'], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
            'time_start' => date('YmdHis', $pay_info['order_time']),
            'time_expire' => date('YmdHis', $pay_info['order_time'] + 300),
        ];

        $response = $this->gateway->purchase($order)->send();
        if (!$response->isSuccessful()) {
            Log::error('微信下单异常', ['data' => $response->getData()]);
            $purchase_result = $response->getData();
            if (isset($purchase_result['err_code_desc'])) {
                return $purchase_result['err_code_desc'];
            }

            return null;
        }

        return $response->getAppOrderData();
    }

    /**
     * 异步回调.
     */
    public function notify()
    {
        $response = $this->gateway->completePurchase([
            'request_params' => file_get_contents('php://input'),
        ])->send();
        $notify_info = $response->getRequestData();
        $attach = json_decode($notify_info['attach'], true);
        $pay_info = [
            'order_type' => $attach['order_type'],
            'order_id' => $attach['order_id'],
            'order_no' => $notify_info['out_trade_no'],
            'transaction_no' => $notify_info['transaction_id'],
            'user_id' => $attach['user_id'],
            'user_name' => $attach['user_name'],
            'user_phone' => $attach['user_phone'],
            'money' => $notify_info['total_fee'] / 100, // 转化单位：元
            'pay_time' => date('Y-m-d H:i:s', strtotime($notify_info['time_end'])), // 原时间格式为 20170108044905
            'pay_type' => 3,
            'pay_from' => $notify_info['trade_type'],
        ];
        $pay = new PayController();
        if ($response->isSignMatch() && $response->isSuccessful()) {
            $pay_info['trade_status'] = 1; // 已付款状态为1
            Log::info('微信支付回调，交易成功');
            $pay->complete($pay_info);
            echo 'SUCCESS';
        } else {
            $pay->fail($pay_info);
            echo 'FAIL';
        }
    }

    /**
     * 关闭订单.
     */
    public function close($order_info)
    {
        $order = [
            'out_trade_no' => $order_info['order_no'],
        ];
        $response = $this->gateway->close($order)->send();
        if (!$response->isSuccessful()) {
            Log::error('微信关闭订单异常', ['data' => $response->getData()]);

            return null;
        }

        return $response->getData();
    }

    /**
     * 查询订单.
     */
    public function query($order_info)
    {
        $options = [
            'out_trade_no' => $order_info['order_no'],
        ];
        $response = $this->gateway->query($options)->send();
        if (!$response->isSuccessful()) {
            Log::error('微信订单查询异常', ['data' => $response->getData()]);

            return null;
        }

        $query_result = $response->getData();
        $_data['order_no'] = $query_result['out_trade_no']; // 本平台订单号
        $_data['transaction_no'] = isset($query_result['transaction_id']) ? $query_result['transaction_id'] : ''; // 三方平台交易号
        $_data['money'] = isset($query_result['total_fee']) ? $query_result['total_fee'] / 100 : 0; // 订单总金额，单位：分
        if (isset($query_result['trade_state'])) {
            switch ($query_result['trade_state']) {
                case 'SUCCESS':
                    $_data['trade_status'] = 'TRADE_SUCCESS';
                    break;
                case 'REFUND':
                    $_data['trade_status'] = 'REFUND';
                    break;
                default:
                    $_data['trade_status'] = $query_result['trade_state'];
                    break;
            }
        } else {
            $_data['trade_status'] = 'STATUS_UNKNOWN';
        }

        return $_data;
    }

    /**
     * 退款订单.
     */
    public function refund($order_info)
    {
        $order = [
            'transaction_id' => $order_info['transaction_no'], // 微信交易号
            'out_trade_no' => $order_info['order_no'],       // 嘻哈订单号
            'out_refund_no' => $order_info['order_no'],       // 嘻哈退款单号
            'total_fee' => $order_info['money'] * 100,    // 订单总额，单位：分
            'refund_fee' => $order_info['money'] * 100,    // 退款金额，单位：分
        ];
        $request = $this->gateway->refund($order);
        $response = $request->send();
        if (!$response->isSuccessful()) {
            Log::error('微信退款异常', ['data' => $response->getData()]);

            return null;
        }
        $refund_query_result = $this->query($order_info);
        if ('REFUND' === $refund_query_result['trade_status'] or 'REFUND_SUCCESS' === $refund_query_result['trade_status']) {
            $order_info['trade_status'] = 1007; // 退款成功的状态为1007
            Log::info('微信退款，完成订单操作，更新订单状态');
            (new PayController($this->pay_type))->complete($order_info);
        } else {
            // nothing to be done here
        }

        return $response->getData();
    }

    /**
     * 退款查询.
     */
    public function queryRefund($order_info)
    {
        $options = [
            'out_trade_no' => $order_info['order_no'],
        ];
        $response = $this->gateway->queryRefund($options)->send();
        if (!$response->isSuccessful()) {
            Log::error('微信退款查询异常', ['data' => $response->getData()]);

            return null;
        }

        $query_result = $response->getData();
        $_data['order_no'] = $query_result['out_trade_no']; // 本平台订单号
        $_data['transaction_no'] = $query_result['transaction_id']; // 三方平台交易号
        $_data['money'] = $query_result['total_fee'] / 100; // 订单总金额，单位：分
        if ('SUCCESS' === $query_result['result_code'] && isset($query_result['refund_status_0'])) {
            switch ($query_result['refund_status_0']) {
                case 'SUCCESS':
                    $_data['trade_status'] = 'REFUND_SUCCESS';
                    break;
                case 'PROCESSING':
                    $_data['trade_status'] = 'REFUND_PROCESSING';
                    break;
                default:
                    $_data['trade_status'] = 'UNKNOWN';
                    break;
            }
        } else {
            $_data['trade_status'] = 'ERROR';
        }

        return $_data;
    }

    /**
     * 提现.
     */
    public function transfer(array $param)
    {
        return ['data' => $this->gateway->getParameters()];
        $options = [
            'partner_trade_no' => '2017041710250101', // 订单号 2017041710250101
            'openid' => 'o7le9t2GThPEYtqOKByf-o-I9LFM', // gaodacheng22的微信号的openid
        ];
        try {
            $response = $this->gateway->promotionTransfer($options)->send();

            return $response->getData();
        } catch (Exception $e) {
            Log::error('提现异常', ['data' => ['file' => $e->getFile(), 'line' => $e->getLine(), 'msg' => $e->getMessage()]]);

            return ['data' => ['file' => $e->getFile(), 'line' => $e->getLine(), 'msg' => $e->getMessage()]];
        }
    }
}
