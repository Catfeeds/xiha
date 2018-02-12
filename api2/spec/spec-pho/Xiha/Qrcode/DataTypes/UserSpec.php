<?php

use Xiha\Qrcode\DataTypes\User;

describe('User', function () {

    it('is a user', function () {
        $User = new User();
        expect($User)->toBeAnInstanceOf('Xiha\Qrcode\DataTypes\User');
    });

    it('should start with prefix', function () {
        $User = new User();
        $prefix = 'XHUSER';
        $User->create([]);
        expect(substr(strval($User), 0, 6))->toBe($prefix);
    });

    it('should generate a valid qrcode with full user info', function () {
        $User = new User();
        $user_info = [
            'phone' => 'phone',
            'identity_id' => 'idcard',
            'user_id' => 1,
            'user_name' => 'uname',
            'photo_id' => 1,
        ];
        $User->create($user_info);
        expect(strval($User))->toBe('XHUSER,phone,idcard,1,uname,1');
    });

    it('should generate a valid user qrcoe with identity', function () {
        $User = new User();
        $user_info = [
            'identity_id' => 'idcard',
        ];
        $User->create($user_info);
        expect(strval($User))->toBe('XHUSER,,idcard,,,');
    });

    it('should generate a valid user qrcoe with user_id', function () {
        $User = new User();
        $user_info = [
            'user_id' => 1,
        ];
        $User->create($user_info);
        expect(strval($User))->toBe('XHUSER,,,1,,');
    });

    it('should generate a valid qrcode with user_name', function () {
        $User = new User();
        $user_info = [
            'user_name' => 'uname',
        ];
        $User->create($user_info);
        expect(strval($User))->toBe('XHUSER,,,,uname,');
    });

    it('should generate a valid qrcode with photo_id', function () {
        $User = new User();
        $user_info = [
            'photo_id' => 1,
        ];
        $User->create($user_info);
        expect(strval($User))->toBe('XHUSER,,,,,1');
    });

    it('should generate a valid qrcode with empty user_info', function () {
        $User = new User();
        $user_info = [];
        $User->create($user_info);
        expect(strval($User))->toBe('XHUSER,,,,,');
    });

});
