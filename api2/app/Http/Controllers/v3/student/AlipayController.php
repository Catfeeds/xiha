<?php
/**
 * @package 支付宝支付驱动
 * @author gaodacheng
 */

namespace App\Http\Controllers\v3\student;

use App\Http\Controllers\Controller;
use Omnipay\Omnipay;
use App\Http\Controllers\v3\student\PayController;
use Exception;
use InvalidArgumentException;
use Log;

class AlipayController extends Controller {

    /**
     * @var AopAppGateway $gateway
     */
    protected $gateway;

    /**
     * @var $pay_type
     */
    protected $pay_type = 1;

    protected $options;

    public function __construct() {
        $this->setUp();
    }

    /**
     * 配置支付宝支付参数
     */
    public function setUp() {
        $this->gateway = Omnipay::create('Alipay_AopApp');
        // $this->gateway->setAppId('2015081100209228'); // ghgd
        // $this->gateway->setPrivateKey(app()->ROOT_PATH.'api2/libs/paykey/alipay/rsa_private_key.pem'); // ghgd
        $this->gateway->setAppId('2015082600234249'); // xhxc
        $this->gateway->setPrivateKey(app()->ROOT_PATH.'api2/libs/paykey/alipay_xh/rsa_private_key.pem'); // xhxc
        $this->gateway->setAlipayPublicKey(app()->ROOT_PATH.'api2/libs/paykey/alipay_xh/alipay_public_key.pem');
        $this->gateway->setNotifyUrl(env('API_PATH').'api2/public/v1/order/notify/alipay');
    }

    /**
     * 统一下单
     */
    public function purchase(Array $pay_info) {
        $request = $this->gateway->purchase();
        $order = [
            'subject'      => $pay_info['title'],
            'out_trade_no' => $pay_info['order_no'],
            'total_amount' => $pay_info['money'],
            'product_code' => 'QUICK_MSECURITY_PAY',
            'body'         => json_encode($pay_info['order_package'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
        ];
        $request->setBizContent($order);
        $response = $request->send();

        if ( ! $response->isSuccessful()) {
            Log::error('支付宝下单异常', ['data' => $response->getData()]);
            return NULL;
        }

        return ['order_string' => $response->getOrderString()];
    }

    /**
     * 异步回调
     */
    public function notify() {
        $notify_info = $_POST;
        $request = $this->gateway->completePurchase();
        $request->setParams($notify_info);
        try {
            $response = $request->send();
            if ( $response->isSuccessful() ) {
                $pay = new PayController();
                $order_package = json_decode($notify_info['body'], true);
                $pay_info = [
                    'order_type'     => $order_package['order_type'],
                    'order_id'       => $order_package['order_id'],
                    'order_no'       => $notify_info['out_trade_no'],
                    'transaction_no' => $notify_info['trade_no'],
                    'trade_status'   => 1,
                    'user_id'        => $order_package['user_id'],
                    'user_name'      => $order_package['user_name'],
                    'user_phone'     => $order_package['user_phone'],
                    'money'          => $notify_info['buyer_pay_amount'],   // 用户实付金额 单位：元
                    'pay_type'       => 1, // 支付宝类型为1
                    'pay_time'       => $notify_info['gmt_payment'],        // 格式： 2017-01-08 04:31:02
                ];
                switch ($notify_info['trade_status']) {
                case 'TRADE_SUCCESS':
                    Log::info('支付宝交易回调，交易成功');
                    $pay_info['trade_status'] = 1; // 交易成功，已付款
                    break;
                case 'TRADE_CLOSED':
                    Log::info('支付宝交易回调，交易关闭');
                    $pay_info['trade_status'] = 1007; // 交易关闭，八成是全额退款完成
                    break;
                default :
                    Log::info('支付宝回调', ['alipay_notify' => $notify_info]);
                    $pay_info['trade_status'] = 1; // 已付款
                    break;
                }
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
     * 查询订单
     */
    public function query($order_info) {
        $request = $this->gateway->query();
        $order = [
            'out_trade_no' => $order_info['order_no'], // 订单号
        ];
        $response = $request->setBizContent($order)->send();
        if ( ! $response->isSuccessful()) {
            Log::error('支付宝订单查询异常', ['data' => $response->getData()]);
            return NULL;
        }

        if (isset($response->getData()['alipay_trade_query_response']) && count($response->getData()['alipay_trade_query_response']) > 0) {
            $query_result = $response->getData()['alipay_trade_query_response'];
            $_data['order_no'] = $query_result['out_trade_no']; // 本平台订单号
            $_data['transaction_no'] = $query_result['trade_no']; // 三方平台交易号
            $_data['money'] = $query_result['total_amount']; // 订单金额，两位小数，单位：元
            if (isset($query_result['trade_status'])) {
                switch ($query_result['trade_status']) {
                case 'TRADE_SUCCESS':
                    $_data['trade_status'] = 'TRADE_SUCCESS';
                    break;
                case 'TRADE_CLOSED':
                    $_data['trade_status'] = 'TRADE_CLOSED';
                    break;
                default :
                    Log::info('支付宝订单查询状态：', ['trade_status' => $query_result['trade_status']]);
                    $_data['trade_status'] = 'STATUS_UNKNOWN';
                    break;
                }
            } else {
                Log::info('支付宝订单查询状态：', ['trade_status' => $query_result['trade_status']]);
                $_data['trade_status'] = 'STATUS_UNKNOWN';
            }
            return $_data;
        } else {
            return NULL;
        }

    }


    /**
     * 关闭订单
     */
    public function close($order_info) {
        $request = $this->gateway->close();
        $order = [
            'out_trade_no' => $order_info['order_no'], // 订单号
        ];
        $response = $request->setBizContent($order)->send();
        if ( ! $response->isSuccessful()) {
            Log::error('支付宝关闭订单异常', ['data' => $response->getData()]);
            return NULL;
        }

        return $response->getData();
    }


    /**
     * 退款订单
     */
    public function refund($order_info) {
        $request = $this->gateway->refund();
        $order = [
            'out_trade_no'  => $order_info['order_no'], // 订单号
            'refund_amount' => $order_info['money'], // 退款金额
        ];
        $response = $request->setBizContent($order)->send();
        if ( ! $response->isSuccessful()) {
            Log::error('支付宝关闭订单异常', ['data' => $response->getData()]);
            return NULL;
        }

        $refund_query_result = $this->queryRefund($order_info);
        if ('TRADE_CLOSED' === $refund_query_result['trade_status']) {
            $order_info['trade_status'] = 1007; // 退款成功的状态为1007
            Log::info('支付宝退款即将完成状态设置');
            (new PayController($this->pay_type))->complete($order_info);
        } else {
            // nothing to be done here
        }

        return $response->getData();
    }

    /**
     * 退款订单查询
     */
    public function queryRefund($order_info) {
        $request = $this->gateway->query();
        $order = [
            'out_trade_no' => $order_info['order_no'], // 订单号
        ];
        $response = $request->setBizContent($order)->send();
        if ( ! $response->isSuccessful()) {
            Log::error('支付宝退款订单查询异常', ['data' => $response->getData()]);
            return NULL;
        }

        if (isset($response->getData()['alipay_trade_query_response']) && count($response->getData()['alipay_trade_query_response']) > 0) {
            $query_result = $response->getData()['alipay_trade_query_response'];
            $_data['order_no'] = $query_result['out_trade_no']; // 本平台订单号
            $_data['transaction_no'] = $query_result['trade_no']; // 三方平台交易号
            $_data['money'] = $query_result['total_amount']; // 订单金额，两位小数，单位：元
            $_data['trade_status'] = $query_result['trade_status']; // 交易状态
            return $_data;
        } else {
            return NULL;
        }
    }

    /**
     * 提现
     */
    public function transfer(array $param = [])
    {
        Log::info('param:', ['data' => $param]);
        $request = $this->gateway->transfer();
        $order = [
            'out_biz_no' => $param['order_no'], // 转账订单号 2017041710250101 / 2017041710250102
            'payee_type' => 'ALIPAY_LOGONID', // 支付宝登录号，支持邮箱和手机号格式
            'payee_account' => $param['account'], // 收款账号
            'amount' => $param['money'], // 转账金额
            'remark' => '带备注的转账测试g', // 转账备注
        ];
        try
        {
            $response = $request->setBizContent($order)->send();
            if ( ! $response->isSuccessful())
            {
                Log::error('支付宝转账异常', ['data' => $response->getData()]);
                return NULL;
            }

            return $response->getData();
        }
        catch (Exception $e)
        {
            Log::info('请求参数错误', ['data' => ['file' => $e->getFile(), 'line' => $e->getLine(), 'msg' => $e->getMessage()]]);
            return $e->getMessage();
        }
    }

    /**
     * 转账查询
     */
    public function queryTransfer(array $param = [])
    {
        Log::info('param:', ['data' => $param]);
        $request = $this->gateway->queryTransfer();
        $order = [
            'out_biz_no' => $param['order_no'], // 商户系统转账订单号
        ];
        try
        {
            $response = $request->setBizContent($order)->send();
            if ( ! $response->isSuccessful())
            {
                Log::error('支付宝转账查询异常', ['data' => $response->getData()]);
                return NULL;
            }

            return $response->getData();
        }
        catch (Exception $e)
        {
            Log::info('请求参数错误', ['data' => ['file' => $e->getFile(), 'line' => $e->getLine(), 'msg' => $e->getMessage()]]);
            return $e->getMessage();
        }
    }

    /**
     * 对账单下载
     */
    public function billdownload(array $param = [])
    {
        $request = $this->gateway->queryBillDownloadUrl();
        $order = [
            'bill_type' => 'trade', // 固定传入trade
            'bill_date' => $param['date'], // 需要下载的账单日期，最晚是当期日期的前一天
        ];
        try
        {
            $response = $request->setBizContent($order)->send();
            return $response->getData();
        }
        catch (Exception $e)
        {
            Log::info('请求参数错误', ['data' => ['file' => $e->getFile(), 'line' => $e->getLine(), 'msg' => $e->getMessage()]]);
            return $e->getMessage();
        }
    }

}

?>
