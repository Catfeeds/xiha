<?php

use Xiha\Pay\Pay;
use App\PayLog;

describe('Alipay', function () {

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
                'attach_params' => json_encode(['dev' => 'yes', 'biz' => 'signup']),
            ];
        }
        return $data;
    }

    beforeEach(function () {
        $this->unpaidData           = newData(9999);
        $this->waitUserPayData      = newData(71);
        $this->paidData             = newData(79);
        $this->closeData            = newData(82);
        $this->refundedData         = newData(12);
        $this->queryData            = newData(12);
        $this->refundQueryData      = newData(12);
        $this->closedData           = newData(1);
        $this->transferData         = newData(1);
        $this->existTransferData    = newData(11);
        $this->nonExistTransferData = newData(18);
    });

    context('purchase order', function () {
        it('purchase in app', function () {
            $data   = $this->unpaidData;
            $result = (new Pay($data))->alipay()->app()->purchase();

            // app内调起支付宝支付需要的加密字串 order_string
            expect($result)->toBeA('string');
            // fwrite(STDOUT, $result."\n");
        });

        it('purchase in wap', function () {
            $data   = $this->unpaidData;
            $result = (new Pay($data))->alipay()->h5()->purchase();

            // 支付宝手机wap支付跳转链接
            expect($result)->toBeA('string');
            // fwrite(STDOUT, $result."\n");
        });

        it('purchase in pc', function () {
            $data   = $this->unpaidData;
            $result = (new Pay($data))->alipay()->pc()->purchase();

            // 支付宝pc即时到账支付跳转链接
            expect($result)->toBeA('string');
            // fwrite(STDOUT, $result.PHP_EOL);
        });
    });

    context('refund order', function () {
        it('does refund to alipay user account', function () {
            $data                  = $this->paidData;
            $data['refund_amount'] = $data['amount'];
            $result                = (new Pay($data))->alipay()->app()->refund();

            // 退款结果
            expect($result)->toBeA('boolean');
        });
    });

    context('query order', function () {
        it('does return order detail upon paid order', function () {
            $data   = $this->queryData;
            $result = (new Pay($data))->alipay()->app()->query();

            // 查询的订单详情
            expect($result)->toBeAn('array');
            expect($result)->toHaveKey('trade_status');
        });

        it('does return refund detail upon refunded order', function () {
            $data   = $this->refundQueryData;
            $result = (new Pay($data))->alipay()->app()->refundQuery();

            // 查询的退款详情
            expect($result)->toBeAn('array');
            expect($result)->toHaveKey('refund_amount');
        });
    });

    context('cancel order', function () {
        it('could not pay for a cancelled order');

        it('does get refunded when cancel a paid order');
    });

    context('close order', function () {
        it('could not pay for a closed order', function () {
            $data   = $this->waitUserPayData;
            $result = (new Pay($data))->alipay()->app()->close();

            // 关闭订单成功或失败
            expect($result)->toBeA('boolean');
        });

        it('could not close a paid order', function () {
            $data   = $this->closeData;
            $result = (new Pay($data))->alipay()->app()->close();

            // 关闭订单成功或失败
            expect($result)->toBeA('boolean');
        });
    });

    // 转账
    context('transfer money', function () {
        it('does transfer money to account', function () {
            $data = [
                'order_id'    => 'xh'.date('YmdHis').time(),
                'pay_user_id' => 'aodacheng@protonmail.ch',
                'amount'      => 0.10,
                'remark'      => '转账功能测试-高',
                'payer_name'  => '嘻哈学车',
                ];
            $result = (new Pay($data))->alipay()->app()->transfer();

            // 转账结果成功或失败
            expect($result)->toBeA('boolean');
        });

        it('does get NULL to query a non-exist transfer order', function () {
            $data = $this->nonExistTransferData;
            $result = (new Pay($data))->alipay()->app()->transferQuery();

            // 转账的结果查询
            expect($result)->toBe(null);
        });

        it('does get detail to query an exist transfer order', function () {
            $data = $this->existTransferData;
            $result = (new Pay($data))->alipay()->app()->transferQuery();

            // 转账的结果查询
            expect($result)->toBeAn('array');
        });
    });
});
