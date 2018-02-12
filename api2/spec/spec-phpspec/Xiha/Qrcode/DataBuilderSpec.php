<?php

namespace spec\Xiha\Qrcode;

use Xiha\Qrcode\DataBuilder;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class DataBuilderSpec extends ObjectBehavior
{
    function it_is_a_DataBuilder()
    {
        $this->shouldBeAnInstanceOf('Xiha\Qrcode\DataBuilder');
    }

    function it_should_throw_exception_upon_an_invalid_type()
    {
        $invalid_type = 'invalid';
        $this->fromTypeAndData($invalid_type, [])->shouldThrow('\Exception')->duringBuild();
    }

    function it_should_set_type_correctly_upon_type()
    {
        $type = 'user';
        $this->fromTypeAndData($type, [])->getType()->shouldEqual('user');
    }

    function it_should_set_data_correctly_upon_data()
    {
        $data = ['phone' => 'phone'];
        $this->fromTypeAndData('', $data)->getData()->shouldBe($data);
    }

    function it_should_build_a_valid_qrcode_upon_type_and_data()
    {
        $type = 'user';
        $data = [
            'phone' => 'phone',
            'identity_id' => 'idcard',
            'user_id' => 'uid',
            'user_name' => 'uname',
            'photo_id' => 'photoid'
        ];
        $this->fromTypeAndData($type, $data)->build()->shouldReturn('XHUSER,phone,idcard,uid,uname,photoid');
    }

}
