<?php

use Xiha\Pay\Pay;
use App\PayLog;

describe('Wechatpay', function () {

    function newData($log_id = null)
    {
        if ($pay_log = PayLog::find($log_id)) {
            $data = [
                'title'         => $pay_log->title,
                'desc'          => $pay_log->desc,
                'order_id'      => $pay_log->order_id,
                'amount'        => $pay_log->amount,
                'attach_params' => $pay_log->attach_params,
            ];
        } else {
            $data = [
                'title'         => '测试商品-'.date('YmdHis'),
                'desc'          => '付款测试-高',
                'order_id'      => 'gdcpaydev'.time(),
                'amount'        => 0.02,
                'attach_params' => json_encode(['dev' => 'yes']),
            ];
        }
        return $data;
    }

    beforeEach(function () {
        $this->noneExistData        = newData(99999);
        $this->unpaidData           = newData(151);
        $this->waitUserPayData      = newData(71);
        $this->paidData             = newData(203);
        $this->closeData            = newData(82);
        $this->refundedData         = newData(144);
        $this->queryPaidData        = newData(144);
        $this->queryRefundedData    = newData(203);
        $this->closedData           = newData(1);
        $this->transferData         = newData(1);
        $this->existTransferData    = newData(11);
        $this->nonExistTransferData = newData(18);
    });

    context('purchase order', function () {
        it('purchase in our app', function () {
            $data   = newData(88);
            $result = (new Pay($data))->wechatpay()->app()->purchase();
            expect($result)->toBeAn('array');
            expect($result)->toHaveKey('prepayid');
            expect($result['prepayid'])->not()->toBeEmpty();
            // fwrite(STDOUT, json_encode($result)."\n");
        });

        it('purchase in h5 outside wechat', function () {
            $data   = $this->unpaidData;
            $result = (new Pay($data))->wechatpay()->h5()->purchase();
            expect($result)->toBeA('string');
            // fwrite(STDOUT, $result."\n");
        });

        it('purchase in pc through wechat QR-code scaning', function () {
            $data   = $this->unpaidData;
            $result = (new Pay($data))->wechatpay()->pc()->purchase();
            expect($result)->toBeA('string');
            // fwrite(STDOUT, $result."\n");
        });
    });

    context('refund order', function () {
        it('does refund to wechat user account', function () {
            $data                  = $this->paidData;
            $data['refund_amount'] = $data['amount'];
            $result                = (new Pay($data))->wechatpay()->app()->refund();

            // 退款结果
            expect($result)->toBeA('boolean');
        });
    });

    context('query order', function () {
        it('does query None-Exist order throws exception', function () {
            $callable = function () {
                $data   = $this->noneExistData;
                $result = (new Pay($data))->wechatpay()->app()->query();
            };

            // 抛异常
            expect($callable)->toThrow('Exception');
        });

        it('does query paid order', function () {
            $data   = $this->queryPaidData;
            $result = (new Pay($data))->wechatpay()->app()->query();

            // 订单查询结果当是一个数组
            expect($result)->toBeA('array');
        });

        it('does query unpaid order', function () {
            $data   = $this->unpaidData;
            $result = (new Pay($data))->wechatpay()->app()->query();

            // 订单查询结果当是一个数组
            expect($result)->toBeA('array');
            // fwrite(STDOUT, json_encode($result));
        });

        it('does query none-refunded order throws exception', function () {
            $callable = function () {
                $data   = $this->noneExistData;
                $result = (new Pay($data))->wechatpay()->app()->query();
            };

            // 抛异常
            expect($callable)->toThrow('Exception');
        });

        it('does query refunded order', function () {
            $data   = $this->queryRefundedData;
            $result = (new Pay($data))->wechatpay()->app()->refundQuery();

            // 订单查询结果当是一个数组
            expect($result)->toBeA('array');
        });
    });

    // 转账
    context('transfer money', function () {
        it('does transfer money to weixin account', function () {
            $data = [
                // 'order_id'      => 'wx'.date('YmdHis').time(),
                'order_id'      => 'wx201710171437171508222237',
                'title'         => '微信转账',
                'desc'          => '这是一次转账测试',
                'amount'        => 1,
                'pay_user_name' => '高大成',
                'pay_user_id'   => 'o7le9t2GThPEYtqOKByf-o-I9LFM',
            ];
            $result = (new Pay($data))->wechatpay()->app()->transfer();

            expect($result)->toBeA('boolean');
        });

        it('does query detail after transfer money', function () {
            $data = ['order_id' => 'wx201710171437171508222237'];
            $result = (new Pay($data))->wechatpay()->app()->transferQuery();

            // fwrite(STDOUT, json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE).PHP_EOL);
            expect($result)->toBeA('array');
        });
    });
});
