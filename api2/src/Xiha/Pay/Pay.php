<?php

namespace Xiha\Pay;

class Pay
{
    /**
     * @var array
     */
    private $data;

    /**
     * @var \Xiha\Pay\Method\MethodInterface
     */
    private $gateway;

    public function __construct($data = null)
    {
        $this->data = $data;
    }

    public function setData($data)
    {
        $this->data = $data;
        return $this;
    }

    public function getData($data)
    {
        return $this->data;
    }

    public function alipay()
    {
        $this->gateway = new Method\Alipay();
        return $this;
    }

    public function wechatpay()
    {
        $this->gateway = new Method\Wechatpay();
        return $this;
    }

    public function unionpay()
    {
        $this->gateway = new Method\Unionpay();
        return $this;
    }

    public function purchase()
    {
        return $this->gateway->purchase($this->data);
    }

    public function notify()
    {
        return $this->gateway->notify($this->data);
    }

    public function refundNotify()
    {
        return $this->gateway->refundNotify($this->data);
    }

    public function refund()
    {
        return $this->gateway->refund($this->data);
    }

    public function query()
    {
        return $this->gateway->query($this->data);
    }

    public function refundQuery()
    {
        return $this->gateway->refundQuery($this->data);
    }

    public function cancel()
    {
        return $this->gateway->cancel($this->data);
    }

    public function close()
    {
        return $this->gateway->close($this->data);
    }

    public function transfer()
    {
        return $this->gateway->transfer($this->data);
    }

    public function transferQuery()
    {
        return $this->gateway->transferQuery($this->data);
    }

    public function __call($name, $arguments)
    {
        $this->gateway->initialize($name);
        return $this;
    }
}
