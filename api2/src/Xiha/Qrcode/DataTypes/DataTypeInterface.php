<?php

namespace Xiha\Qrcode\Datatypes;

interface DataTypeInterface
{
    public function create(array $arguments);

    public function __toString();
}
