<?php

namespace Xiha\Pay\Method;

use Omnipay\Omnipay;
use Xiha\Pay\Exception\ResponseException;
use Log;

final class Wechatpay extends AbstractMethod
{
    const GATEWAY = 'Wechatpay';

    public function __construct()
    {
        parent::__construct();
    }

    public function initialize($scene = null)
    {
        $this->setupScene($scene);
        $this->gateway = Omnipay::create($this->gateway_name);
        $this->gateway->setAppId(getenv(getenv('WECHATPAY_ACTIVE').'WECHATPAY_APP_ID'));
        $this->gateway->setMchId(getenv(getenv('WECHATPAY_ACTIVE').'WECHATPAY_MCH_ID'));
        $this->gateway->setApiKey(getenv(getenv('WECHATPAY_ACTIVE').'WECHATPAY_API_KEY'));
        $this->gateway->setNotifyUrl(getenv(getenv('WECHATPAY_ACTIVE').'WECHATPAY_NOTIFY_URL'));
        $this->gateway->setCertPath(getenv(getenv('WECHATPAY_ACTIVE').'WECHATPAY_CERT_PATH'));
        $this->gateway->setKeyPath(getenv(getenv('WECHATPAY_ACTIVE').'WECHATPAY_KEY_PATH'));
    }

    public function purchase(array $data)
    {
        parent::purchase($data);
        try {
            $response = $this->gateway->purchase([
                'body'             => $data['title'],
                'out_trade_no'     => $data['order_id'],
                'total_fee'        => $data['amount'] * 100, // 单位：分(CNY)
                'spbill_create_ip' => '112.28.182.109',      // buyer's client ip addr
                'fee_type'         => 'CNY',
                'attach'           => $data['attach_params'],
            ])->send();
        } catch (\Exception $exception) {
            throw $exception;
        }
        if (! $response->isSuccessful()) {
            $data = $response->getData();
            // 通信失败:可能签名错误等
            if ('FAIL' === $data['return_code']
                && array_key_exists('return_msg', $data)
                && ! empty($msg = $data['return_msg'])
            ) {
                throw new ResponseException(sprintf('微信购买通信结果异常：%s', $msg));
            }
            // 交易失败:可能有参数未填写、订单号重复等
            if ('FAIL' === $data['result_code']
                && array_key_exists('err_code_des', $data)
                && ! empty($msg = $data['err_code'].' '.$data['err_code_des'])
            ) {
                throw new ResponseException(sprintf('微信购买交易结果异常：%s', $msg));
            }
            // 其他未知异常
            throw new ResponseException('微信购买操作未知异常');
        }
        // 交易创建时间
        $data['trade_create'] = date('Y-m-d H:i:s');
        $this->updateLog($data);

        return $response->{$this->responseMethod}();
    }

    public function notify(array $data)
    {
        $must_fields = ['request_params'];
        $this->validateFields($must_fields, $data);
        try {
            $response = $this->gateway->completePurchase([
                'request_params' => $data['request_params'],
            ])->send();
        } catch (\Exception $exception) {
            throw $exception;
        }
        $data = $response->getRequestData();
        if ($response->isSignMatch() && $response->isPaid()) {
            $notify = [
                'attach_params' => @$data['attach'] ?: null,
                'order_id'      => $data['out_trade_no'],
                'trade_id'      => $data['transaction_id'],
                'amount'        => (int) ($data['total_fee']) / 100,
                'pay_amount'    => (int) ($data['cash_fee']) / 100,
                'trade_status'  => 'SUCCESS',
                'trade_payment' => date('Y-m-d H:i:s', strtotime($data['time_end'])),
                'pay_user_id'   => $data['openid'],
                'app_id'        => $data['appid'],
            ];
            $notify['gateway'] = self::GATEWAY;
            $this->updateLog($notify);
            parent::notifyForward($notify);
        }
        echo 'SUCCESS';
    }

    public function refundNotify(array $data)
    {
        $must_fields = ['request_params'];
        $this->validateFields($must_fields, $data);
        try {
            $response = $this->gateway->completeRefund([
                'request_params' => $data['request_params'],
            ])->send();
        } catch (\Exception $exception) {
            throw $exception;
        }
        $data = $response->getRequestData();
        $refundNotify = [
            'order_id'      => $data['out_trade_no'],
            'trade_id'      => $data['transaction_id'],
            'amount'        => (int) $data['total_fee'] / 100,
            'refund_amount' => (int) $data['refund_fee'] / 100,
            'trade_refund'  => $data['success_time'],
            'app_id'        => $data['appid'],
        ];
        $refundNotify['gateway'] = self::GATEWAY;
        $this->updateLog($refundNotify);
        echo 'SUCCESS';

        return $refundNotify;
    }

    public function refund(array $data)
    {
        parent::refund($data);
        try {
            $response = $this->gateway->refund([
                'out_trade_no'  => $data['order_id'],
                'out_refund_no' => $data['order_id'].'3',
                'total_fee'     => $data['amount'] * 100,
                'refund_fee'    => $data['refund_amount'] * 100,
            ])->send();
        } catch (\Exception $exception) {
            throw $exception;
        }
        $data = $response->getData();
        if (! $response->isSuccessful()) {
            return false;
        }
        $refund = [
            'order_id'      => $data['out_trade_no'],
            'trade_id'      => $data['transaction_id'],
            'refund_amount' => (int) ($data['cash_refund_fee']) / 100,
            'trade_refund'  => date('Y-m-d H:i:s'),
        ];
        $refund['gateway'] = self::GATEWAY;
        $this->updateLog($refund);

        return true;
    }

    public function transfer(array $data)
    {
        parent::transfer($data);
        $transfer = array_merge(
            $data,
            [
                'gateway'         => self::GATEWAY,
                'trade_create'    => date('Y-m-d H:i:s'),
                'transfer_amount' => $data['amount'],
            ]
        );
        try {
            $response = $this->gateway->transfer([
                'partner_trade_no' => $data['order_id'],
                'open_id'          => $data['pay_user_id'],
                'amount'           => $data['amount'] * 100,
                'desc'             => $data['desc'],
                're_user_name'     => $data['pay_user_name'],
                'check_name'       => 'FORCE_CHECK',
                'spbill_create_ip' => '8.8.8.8',
            ])->send();
        } catch (\Exception $exception) {
            throw $exception;
        }
        $data = $response->getData();
        if (! $response->isSuccessful()) {
            throw new ResponseException(sprintf(
                '微信转账: [code: %s, msg: %s]',
                $data['err_code'],
                $data['err_code_des']
            ));
        }
        $transfer = array_merge(
            $transfer,
            [
                'trade_payment'  => $data['payment_time'],
                'trade_transfer' => $data['payment_time'],
                'trade_id'       => $data['payment_no'],
                'trade_status'   => 'SUCCESS',
                'remark'         => '成功',
                'app_id'         => $response->getRequest()->getAppId(),
            ]
        );
        $this->updateLog($transfer);

        return true;
    }

    public function query(array $data)
    {
        parent::query($data);
        try {
            $response = $this->gateway->query([
                'out_trade_no' => $data['order_id'],
            ])->send();
        } catch (\Exception $exception) {
            throw $exception;
        }
        $data = $response->getData();
        if (! $response->isSuccessful()) {
            throw new ResponseException(sprintf(
                '微信订单查询: [code: %s, msg: %s]',
                $data['err_code'],
                $data['err_code_des']
            ));
        }
        switch ($data['trade_state']) {
            case 'SUCCESS':
                $query = [
                    'attach_params' => @$data['attach'] ?: null,
                    'order_id'      => $data['out_trade_no'],
                    'trade_id'      => $data['transaction_id'],
                    'amount'        => (int) ($data['total_fee']) / 100,
                    'pay_amount'    => (int) ($data['cash_fee']) / 100,
                    'trade_status'  => $data['trade_state'],
                    'trade_payment' => date('Y-m-d H:i:s', strtotime($data['time_end'])),
                    'pay_user_id'   => $data['openid'],
                    'app_id'        => $data['appid'],
                ];
                break;
            default:
                $query = [
                    'order_id' => $data['out_trade_no'],
                    'trade_status' => $data['trade_state'],
                ];
        }
        $query['gateway'] = self::GATEWAY;
        $this->updateLog($query);

        return $query;
    }

    public function refundQuery(array $data)
    {
        parent::refundQuery($data);
        try {
            $response = $this->gateway->queryRefund([
                'out_trade_no' => $data['order_id'],
            ])->send();
        } catch (\Exception $exception) {
            throw $exception;
        }
        $data = $response->getData();
        if (! $response->isSuccessful()) {
            throw new ResponseException(sprintf(
                '微信退款查询: [code: %s, msg: %s]',
                $data['err_code'],
                $data['err_code_des']
            ));
        }
        $query = [
            'order_id'      => $data['out_trade_no'],
            'trade_id'      => $data['transaction_id'],
            'amount'        => (int) ($data['total_fee']) / 100,
            'pay_amount'    => (int) ($data['cash_fee']) / 100,
            'refund_amount' => (int) ($data['refund_fee']) / 100,
            'trade_refund'  => @$data['refund_success_time_'.(string) ($data['refund_count'] - 1)],
            'app_id'        => $data['appid'],
        ];
        $query['gateway'] = self::GATEWAY;
        $this->updateLog($query);

        return $query;
    }

    public function transferQuery(array $data)
    {
        parent::transferQuery($data);
        try {
            $response = $this->gateway->queryTransfer([
                'partner_trade_no' => $data['order_id'],
            ])->send();
        } catch (\Exception $exception) {
            throw $exception;
        }
        $data = $response->getData();
        if (! $response->isSuccessful()) {
            throw new ResponseException(sprintf(
                '微信转账查询: [code: %s, msg: %s]',
                $data['err_code'],
                $data['err_code_des']
            ));
        }
        $transferQuery = [
            'desc'            => $data['desc'],
            'order_id'        => $data['partner_trade_no'],
            'trade_id'        => $data['detail_id'],
            'pay_user_id'     => $data['openid'],
            'trade_status'    => $data['status'],
            'pay_user_name'   => $data['transfer_name'],
            'trade_transfer'  => $data['transfer_time'],
            'transfer_amount' => $data['payment_amount'] / 100,
            'gateway'         => self::GATEWAY,
        ];
        $this->updateLog($transferQuery);

        return $transferQuery;
    }

    protected function app()
    {
        $this->gateway_name   = 'WechatPay_App';
        $this->responseMethod = 'getAppOrderData';
    }

    protected function h5()
    {
        $this->gateway_name   = 'WechatPay_Mweb';
        $this->responseMethod = 'getMwebUrl';
    }

    public function pc()
    {
        $this->gateway_name   = 'WechatPay_Native';
        $this->responseMethod = 'getCodeUrl';
    }

    protected function setDefault()
    {
        $this->gateway_name   = 'WechatPay';
    }
}
