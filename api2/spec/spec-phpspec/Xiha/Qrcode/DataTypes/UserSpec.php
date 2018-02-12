<?php

namespace spec\Xiha\Qrcode\DataTypes;

use Xiha\Qrcode\DataTypes\User;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class UserSpec extends ObjectBehavior
{
    function it_is_a_user()
    {
        $this->shouldBeAnInstanceOf('Xiha\Qrcode\DataTypes\User');
    }

    function it_should_start_with_prefix_XHUSER()
    {
        $prefix = 'XHUSER';
        $this->__toString()->shouldStartWith($prefix);
    }

    function it_should_generate_a_valid_user_qrcode_with_full_user_info()
    {
        $user_info = [
            'phone' => 'phone',
            'identity_id' => 'idcard',
            'user_id' => 1,
            'user_name' => 'name',
            'photo_id' => 1,
        ];
        $this->create($user_info);
        $this->__toString()->shouldEqual('XHUSER,phone,idcard,1,name,1');
    }

    function it_should_generate_a_valid_user_qrcode_with_phone()
    {
        $user_info = [
            'phone' => 'phone',
        ];
        $this->create($user_info);
        $this->__toString()->shouldEqual('XHUSER,phone,,,,');
    }

    function it_should_generate_a_valid_user_qrcode_with_identity()
    {
        $user_info = [
            'identity_id' => 'idcard',
        ];
        $this->create($user_info);
        $this->__toString()->shouldEqual('XHUSER,,idcard,,,');
    }

    function it_should_generate_a_valid_user_qrcode_with_user_id()
    {
        $user_info = [
            'user_id' => 1,
        ];
        $this->create($user_info);
        $this->__toString()->shouldEqual('XHUSER,,,1,,');
    }

    function it_should_generate_a_valid_user_qrcode_with_user_name()
    {
        $user_info = [
            'user_name' => 'name',
        ];
        $this->create($user_info);
        $this->__toString()->shouldEqual('XHUSER,,,,name,');
    }

    function it_should_generate_a_valid_user_qrcode_with_photo()
    {
        $user_info = [
            'photo_id' => 1,
        ];
        $this->create($user_info);
        $this->__toString()->shouldEqual('XHUSER,,,,,1');
    }

    function it_should_generate_a_valid_user_qrcode_with_empty_user_info()
    {
        $user_info = [];
        $this->create($user_info);
        $this->__toString()->shouldEqual('XHUSER,,,,,');
    }
}
