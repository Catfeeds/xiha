<?php

namespace spec\Xiha\Qrcode\DataTypes;

use Qrcode\DataTypes\Coach;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class CoachSpec extends ObjectBehavior
{
    function it_is_a_coach()
    {
        $this->shouldBeAnInstanceOf('Xiha\Qrcode\DataTypes\Coach');
    }
}
