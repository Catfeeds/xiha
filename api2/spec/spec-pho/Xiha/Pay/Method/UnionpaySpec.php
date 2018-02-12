<?php

use Xiha\Pay\Pay;

describe('Unionpay', function () {
    context('purchase order', function () {
        it('in app', function () {
            $data = [
                'title'         => 'unionpay inside app',
                'order_id'      => 'gdcpaydev'.time(),
                'price'         => 0.01,
                'attach_params' => json_encode(['dev' => 'yes']),
            ];
            expect((new Pay($data))->unionpay()->app()->purchase())->toBeAn('string');
        });

        it('in wap');
        it('in pc');
    });

    context('query order', function () {
        it('not complemented');
    });

    context('refund order', function () {
        it('not complemented');
    });
});
