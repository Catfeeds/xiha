<?php

use Xiha\Qrcode\DataBuilder;

describe('DataBuilder', function () {

    $data_builder = new DataBuilder();

    it('should be a DataBuilder', function () use ($data_builder) {
        expect($data_builder)->toBeAnInstanceOf('Xiha\Qrcode\DataBuilder');
    });

    it('should throw exception when build upon an invalid type', function () use ($data_builder) {
        $callable = function () use ($data_builder) {
            $invalid_type = 'invalid';
            $data_builder->fromTypeAndData($invalid_type, [])
                ->build();
        };
        expect($callable)->toThrow('\Exception');
    });

    it('should set type correctly upon type', function () use ($data_builder) {
        $type = 'user';
        expect($data_builder->fromTypeAndData($type, [])->getType())->toBe('user');
    });

    it('should set set data correctly upon data', function () use ($data_builder) {
        $data = ['phone' => 'phone'];
        expect($data_builder->fromTypeAndData('', $data)->getData())->toBe($data);
    });

    it('should build a valid qrcode upon type and data', function () use ($data_builder) {
        $type = 'user';
        $data = [
            'phone' => 'phone',
            'identity_id' => 'idcard',
            'user_id' => 'uid',
            'user_name' => 'uname',
            'photo_id' => 'photoid'
        ];
        expect($data_builder->fromTypeAndData($type, $data)->build())->toBe('XHUSER,phone,idcard,uid,uname,photoid');
    });

});
