<?php

namespace Xiha\Pay\Method;

use App\PayLog;
use Guzzle\Http\Client as HttpClient;
use Xiha\Pay\Exception\RuntimeException;
use Xiha\Pay\Exception\ResponseException;

abstract class AbstractMethod implements MethodInterface
{
    /**
     * 业务前辍
     */
    const BIZ_PREFIX = 'XIHAPAY_BIZ_';

    /**
     * @var string
     */
    private $gateway_name;

    /**
     * @var string
     */
    private $responseCallback;

    public function __construct()
    {
        $this->setDefault();
    }

    public function purchase(array $data)
    {
        $must_fields = ['title', 'desc', 'order_id', 'amount', 'attach_params'];
        $this->validateFields($must_fields, $data);
    }

    public function notify(array $data)
    {
    }

    public function refund(array $data)
    {
        $must_fields = ['order_id', 'amount', 'refund_amount'];
        $this->validateFields($must_fields, $data);
    }

    public function query(array $data)
    {
        $must_fields = ['order_id'];
        $this->validateFields($must_fields, $data);
    }

    public function refundQuery(array $data)
    {
        $must_fields = ['order_id'];
        $this->validateFields($must_fields, $data);
    }

    public function cancel(array $data)
    {
        $must_fields = ['order_id'];
        $this->validateFields($must_fields, $data);
    }

    public function close(array $data)
    {
        $must_fields = ['order_id'];
        $this->validateFields($must_fields, $data);
    }

    public function transfer(array $data)
    {
        $must_fields = ['order_id', 'title', 'desc', 'amount', 'pay_user_id'];
        $this->validateFields($must_fields, $data);
        $this->updateLog($data);
    }

    public function transferQuery(array $data)
    {
        $must_fields = ['order_id'];
        $this->validateFields($must_fields, $data);
    }

    protected function updateLog(array $data)
    {
        try {
            $must_fields = ['order_id'];
            $this->validateFields($must_fields, $data);

            $pay_log = PayLog::firstOrCreate(['order_id' => $data['order_id']]);
            $pay_log->update($data);
        } catch (RuntimeException $exception) {
            // if we do not have `order_id` key or the value of `order_id` is null
            // do nothing
        }
    }

    /**
     * 支付结果的异步通知转发
     */
    protected function notifyForward(array $arguments)
    {
        if (isset($arguments['attach_params'])
            && is_string($arguments['attach_params'])
        ) {
            $attach_params = json_decode($arguments['attach_params'], true);
            if (isset($attach_params['biz'])) {
                try {
                    // send request with guzzle's help
                    $http_client = new HttpClient();
                    $uri         = getenv(self::BIZ_PREFIX.strtoupper($attach_params['biz']));
                    $headers     = null;
                    $postBody    = $arguments;
                    $http_client->post($uri, $headers, $postBody)->send();
                } catch (\Exception $exception) {
                    throw $exception;
                }
            } else {
                // no biz specified
                throw new ResponseException('no biz specified when we want to send biz notification');
            }
        } else {
            // no attach_params specified
            throw new ResponseException('no attach_params specified when we want to find it');
        }
    }

    protected function validateFields(array $fields, array $data)
    {
        foreach ($fields as $field) {
            if (! array_key_exists($field, $data)) {
                throw new RuntimeException('缺少必须字段'.$field);
            }
            if (empty($data[$field])) {
                throw new RuntimeException('该字段不能置空'.$field);
            }
        }

        return true;
    }

    protected function setupScene($scene)
    {
        if (null === $scene) {
            $scene = 'app';
        }
        if (! method_exists($this, $scene)) {
            throw new RuntimeException(sprintf('[%s %s] not supported yet', GATEWAY, $scene));
        }
        static::$scene();
    }
}
