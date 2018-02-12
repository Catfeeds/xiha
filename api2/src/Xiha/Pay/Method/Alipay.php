<?php

namespace Xiha\Pay\Method;

use Omnipay\Omnipay;
use Xiha\Pay\Exception\ResponseException;

final class Alipay extends AbstractMethod
{
    const GATEWAY = 'Alipay';

    protected $product_code;

    public function __construct()
    {
        parent::__construct();
    }

    public function initialize($scene = null)
    {
        $this->setupScene($scene);
        $this->gateway = Omnipay::create($this->gateway_name);
        $this->gateway->setAppId(getenv(getenv('ALIPAY_ACTIVE').'ALIPAY_APP_ID'));
        $this->gateway->setPrivateKey(getenv(getenv('ALIPAY_ACTIVE').'ALIPAY_PRIVATE_KEY'));
        $this->gateway->setAlipayPublicKey(getenv(getenv('ALIPAY_ACTIVE').'ALIPAY_ALIPAY_PUBLIC_KEY'));
        $this->gateway->setNotifyUrl(getenv(getenv('ALIPAY_ACTIVE').'ALIPAY_NOTIFY_URL'));
    }

    public function purchase(array $data)
    {
        parent::purchase($data);
        try {
            $request  = $this->gateway->purchase();
            $response = $request->setBizContent([
                'subject'             => $data['title'],
                'body'                => $data['desc'],
                'total_amount'        => $data['amount'],
                'out_trade_no'        => $data['order_id'],
                'passback_params'     => $data['attach_params'],
                'product_code'        => $this->product_code,
                'enable_pay_channels' => 'balance,moneyFund,coupon,pcredit,debitCardExpress,bankPay',
            ])->send();
        } catch (\Exception $exception) {
            throw $exception;
        }
        if (! $response->issuccessful()) {
            throw new ResponseException('支付宝购买操作返回结果异常');
        }
        $this->updateLog($data);

        return $response->{$this->responseMethod}();
    }

    public function notify(array $data)
    {
        parent::notify($data);
        try {
            $request  = $this->gateway->completePurchase();
            $response = $request->setParams($data)->send();
        } catch (\Exception $exception) {
            throw $exception;
        }
        if ($response->isPaid()) {
            $notify = [
                'title'             => @$data['subject'] ?: null,
                'desc'              => @$data['body'] ?: null,
                'attach_params'     => @$data['passback_params'] ?: null,
                'order_id'          => $data['out_trade_no'],
                'trade_id'          => $data['trade_no'],
                'amount'            => @$data['total_amount'] ?: null,
                'pay_amount'        => @$data['buyer_pay_amount'] ?: null,
                'trade_status'      => @$data['trade_status'] ?: null,
                'trade_create'      => @$data['gmt_create'] ?: null,
                'trade_payment'     => @$data['gmt_payment'] ?: null,
                'pay_user_id'       => @$data['buyer_id'] ?: null,
                'pay_user_logon_id' => @$data['buyer_logon_id'] ?: null,
                'app_id'            => $data['app_id'],
            ];
            $notify['gateway'] = self::GATEWAY;
            $this->updateLog($notify);
            parent::notifyForward($notify);
        }
        echo $response->getResponseText();
    }

    public function refund(array $data)
    {
        parent::refund($data);
        try {
            $request  = $this->gateway->refund();
            $response = $request->setBizContent([
                'out_trade_no'  => $data['order_id'],
                'refund_amount' => $data['refund_amount'],
            ])->send();
        } catch (\Exception $exception) {
            throw $exception;
        }
        if (! $response->isSuccessful()) {
            return false;
        }
        $refund = [
            'order_id'      => $response->getAlipayResponse('out_trade_no'),
            'trade_id'      => $response->getAlipayResponse('trade_no'),
            'refund_amount' => $response->getAlipayResponse('refund_fee'),
            'trade_refund'  => $response->getAlipayResponse('gmt_refund_pay'),
        ];
        $refund['gateway'] = self::GATEWAY;
        $this->updateLog($refund);

        return true;
    }

    public function query(array $data)
    {
        parent::query($data);
        try {
            $request  = $this->gateway->query();
            $response = $request->setBizContent([
                'out_trade_no' => $data['order_id'],
            ])->send();
        } catch (\Exception $exception) {
            throw $exception;
        }
        if (! $response->isSuccessful()) {
            throw new ResponseException(
                '支付宝订单查询:'.
                sprintf(
                    '[code:%s, message:%s; sub_code:%s, sub_message:%s]',
                    $response->getCode(),
                    $response->getMessage(),
                    $response->getSubCode(),
                    $response->getSubMessage()
                )
            );
        }
        $query = [
            'order_id'     => $response->getAlipayResponse('out_trade_no'),
            'trade_id'     => $response->getAlipayResponse('trade_no'),
            'amount'       => $response->getAlipayResponse('total_amount'),
            'trade_status' => $response->getTradeStatus(),
        ];
        $query['gateway'] = self::GATEWAY;
        $this->updateLog($query);

        return $query;
    }

    public function refundQuery(array $data)
    {
        parent::refundQuery($data);
        try {
            $request  = $this->gateway->refundQuery();
            $response = $request->setBizContent([
                'out_trade_no'   => $data['order_id'],
                'out_request_no' => $data['order_id'],
            ])->send();
        } catch (\Exception $exception) {
            throw $exception;
        }
        if (! $response->isSuccessful()) {
            throw new ResponseException(
                '支付宝退款订单查询:'.
                sprintf(
                    '[code:%s, message:%s; sub_code:%s, sub_message:%s]',
                    $response->getCode(),
                    $response->getMessage(),
                    $response->getSubCode(),
                    $response->getSubMessage()
                )
            );
        }
        $refundQuery = [
            'order_id'      => $response->getAlipayResponse('out_trade_no'),
            'trade_id'      => $response->getAlipayResponse('trade_no'),
            'refund_amount' => $response->getAlipayResponse('refund_amount'),
            'amount'        => $response->getAlipayResponse('total_amount'),
        ];
        $this->updateLog($refundQuery);

        return $refundQuery;
    }

    public function cancel(array $data)
    {
        // 未完成 2017/09/30 by GDC
        parent::cancel($data);
        try {
            $request  = $this->gateway->cancel();
            $response = $request->setBizContent([
                'out_trade_no' => $data['order_id'],
            ])->send();
        } catch (\Exception $exception) {
            throw $exception;
        }
        if (! $response->isSuccessful()) {
            throw new ResponseException(
                '支付宝撤消交易:'.
                sprintf(
                    '[code:%s, message:%s; sub_code:%s, sub_message:%s]',
                    $response->getCode(),
                    $response->getMessage(),
                    $response->getSubCode(),
                    $response->getSubMessage()
                )
            );
        }
    }

    public function close(array $data)
    {
        parent::close($data);
        try {
            $request  = $this->gateway->close();
            $response = $request->setBizContent([
                'out_trade_no' => $data['order_id'],
            ])->send();
        } catch (\Exception $exception) {
            throw $exception;
        }
        if (! $response->isSuccessful()) {
            return false;
        }
        $close = [
            'order_id'     => $data['order_id'],
            'trade_close'  => date('Y-m-d H:i:s'),
            'trade_status' => 'TRADE_CLOSED',
        ];
        $this->updateLog($close);

        return true;
    }

    public function transfer(array $data)
    {
        parent::transfer($data);
        try {
            // MUST
            $biz_content = [
                'out_biz_no'    => $data['order_id'],
                'payee_account' => $data['pay_user_id'],
                'amount'        => $data['amount'],
            ];
            if (isset($data['pay_user_type'])) {
                $biz_content['payee_type'] = $data['pay_user_type'];
            } else {
                $biz_content['payee_type'] = 'ALIPAY_LOGONID';
            }
            // OPTIONAL
            if (isset($data['remark'])) {
                $biz_content['remark'] = $data['remark'];
            }
            if (isset($data['payer_name'])) {
                $biz_content['payer_show_name'] = $data['payer_name'];
            }

            $request  = $this->gateway->transfer();
            $response = $request->setBizContent($biz_content)->send();
        } catch (\Exception $exception) {
            throw $exception;
        }
        if ($response->isSuccessful()) {
            $transfer = [
                'title'           => '转账',
                'desc'            => '单笔转账到支付宝账户',
                'amount'          => $data['amount'],
                'order_id'        => $response->getAlipayResponse('out_biz_no'),
                'trade_id'        => $response->getAlipayResponse('order_id'),
                'trade_transfer'  => $response->getAlipayResponse('pay_date'),
                'transfer_amount' => $data['amount'],
                'remark'          => '转账成功',
                'app_id'          => $request->getAppId(),
            ];
            if (isset($data['pay_user_type'])) {
                $transfer['pay_user_type'] = $data['pay_user_type'];
            } else {
                $transfer['pay_user_type'] = 'ALIPAY_LOGONID';
            }
            if ('ALIPAY_LOGONID' === $transfer['pay_user_type']) {
                $transfer['pay_user_logon_id'] = $data['pay_user_id'];
                unset($transfer['pay_user_id']);
            } else {
                $transfer['pay_user_id'] = $data['pay_user_id'];
            }
            $transfer['gateway'] = self::GATEWAY;
            $this->updateLog($transfer);

            return true;
        }
        $result = sprintf(
            '[code:%s,message:%s; sub_code:%s,sub_message:%s]',
            $response->getCode(),
            $response->getMessage(),
            $response->getSubCode(),
            $response->getSubMessage()
        );
        $transfer = [
            'title'       => '转账',
            'desc'        => '单笔转账到支付宝账户',
            'amount'      => $data['amount'],
            'order_id'    => $data['order_id'],
            'pay_user_id' => $data['pay_user_id'],
            'remark'      => "转账失败 $result",
            'app_id'      => $request->getAppId(),
        ];
        if (isset($data['pay_user_type'])) {
            $transfer['pay_user_type'] = $data['pay_user_type'];
        } else {
            $transfer['pay_user_type'] = 'ALIPAY_LOGONID';
        }
        if ('ALIPAY_LOGONID' === $transfer['pay_user_type']) {
            $transfer['pay_user_logon_id'] = $data['pay_user_id'];
            unset($transfer['pay_user_id']);
        } else {
            $transfer['pay_user_id'] = $data['pay_user_id'];
        }
        $transfer['gateway'] = self::GATEWAY;
        $this->updateLog($transfer);

        return false;
    }

    public function transferQuery(array $data)
    {
        parent::transferQuery($data);
        try {
            $request  = $this->gateway->transferQuery();
            $response = $request->setBizContent([
                'out_biz_no' => $data['order_id'],
            ])->send();
        } catch (\Exception $exception) {
            throw $exception;
        }
        if (! $response->isSuccessful()) {
            $result = sprintf(
                '[code:%s, message:%s; sub_code:%s, sub_message:%s]',
                $response->getCode(),
                $response->getMessage(),
                $response->getSubCode(),
                $response->getSubMessage()
            );
            $transferQuery = [
                'title'    => '转账',
                'desc'     => '单笔转账到支付宝账户',
                'order_id' => $data['order_id'],
                'remark'   => "转账查询 $result",
                'app_id'   => $request->getAppId(),
                'gateway'  => self::GATEWAY,
            ];
            $this->updateLog($transferQuery);

            return null;
        }
        $transferQuery = [
            'order_id'       => $response->getAlipayResponse('out_biz_no'),
            'trade_id'       => $response->getAlipayResponse('order_id'),
            'trade_transfer' => $response->getAlipayResponse('pay_date'),
            'trade_status'   => $response->getAlipayResponse('status'),
        ];
        $this->updateLog($transferQuery);

        return $transferQuery;
    }

    protected function app()
    {
        $this->gateway_name   = 'Alipay_AopApp';
        $this->product_code   = 'QUICK_MSECURITY_PAY';
        $this->responseMethod = 'getOrderString';
    }

    protected function h5()
    {
        $this->gateway_name   = 'Alipay_AopWap';
        $this->product_code   = 'QUICK_MSECURITY_PAY';
        $this->responseMethod = 'getRedirectUrl';
    }

    protected function pc()
    {
        $this->gateway_name   = 'Alipay_AopPage';
        $this->product_code   = 'FAST_INSTANT_TRADE_PAY';
        $this->responseMethod = 'getRedirectUrl';
    }

    protected function setDefault()
    {
        $this->gateway_name   = 'Alipay_AopApp';
        $this->product_code   = 'QUICK_MSECURITY_PAY';
    }
}
