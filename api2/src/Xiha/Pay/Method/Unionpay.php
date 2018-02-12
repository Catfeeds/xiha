<?php

namespace Xiha\Pay\Method;

use Omnipay\Omnipay;
use Xiha\Pay\Exception\ResponseException;

class Unionpay extends AbstractMethod
{
    const GATEWAY = 'Unionpay';

    public function __construct()
    {
        parent::__construct();
    }

    // 购买
    public function purchase(array $data)
    {
        $must_fields = ['title', 'order_id', 'price', 'attach_params'];
        $this->validateFields($must_fields, $data);

        $order = [
            'orderId'     => $data['order_id'],
            'txnTime'     => date('YmdHis'),
            'orderDesc'   => $data['title'],
            'txnAmt'      => $data['price'] * 100,
            'reqReserved' => json_encode(
                $package['attach_params'],
                JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
            ),
        ];
        $request = $this->gateway->purchase($order);
        $response = $request->send();
        if (! $response->isSuccessful()) {
            throw new ResponseException('银联支付网关下单失败');
        }

        $tn = $response->getTradeNo();

        if (null === $tn) {
            throw new ResponseException('银联支付网关下单返回交易号tn为null');
        }

        return $tn;
    }

    public function initialize($scene = null)
    {
        self::setupScene($scene);
        $this->gateway = Omnipay::create($this->gateway_name);
        $this->gateway->setMerId(getenv(getenv('UNIONPAY_ACTIVE').'UNIONPAY_MER_ID'));
        $this->gateway->setCertPath(getenv(getenv('UNIONPAY_ACTIVE').'UNIONPAY_CERT_PATH'));
        $this->gateway->setCertDir(getenv(getenv('UNIONPAY_ACTIVE').'UNIONPAY_CERT_DIR'));
        $this->gateway->setCertPassword(getenv(getenv('UNIONPAY_ACTIVE').'UNIONPAY_CERT_PASSWORD'));
        $this->gateway->setReturnUrl(getenv(getenv('UNIONPAY_ACTIVE').'UNIONPAY_RETURN_URL'));
        $this->gateway->setNotifyUrl(getenv(getenv('UNIONPAY_ACTIVE').'UNIONPAY_NOTIFY_URL'));
        $this->gateway->setEnvironment('production');
    }

    protected function app()
    {
        $this->gateway_name = 'UnionPay_Express';
    }

    protected function setDefault()
    {
        $this->gateway_name = 'UnionPay_Express';
    }
}
